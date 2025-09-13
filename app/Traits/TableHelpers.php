<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait TableHelpers
{
    /**
     * Handle table data with pagination, sorting, filtering, and search
     */
    protected function handleTableData(Request $request, array $config = [])
    {
        $this->authorize('read ' . $this->authorizeAction, 'web');
        $user = $request->user();

        // Get default sort from controller property
        $defaultSort = 'id';
        $defaultDir = 'desc';

        if (property_exists($this, 'shortDefaultTable') && is_array($this->shortDefaultTable)) {
            $defaultSort = $this->shortDefaultTable[0];
            $defaultDir = $this->shortDefaultTable[1];
        }

        // Default configuration
        $defaultConfig = [
            'table' => null,
            'alias' => null,
            'joins' => [],
            'select' => ['*'],
            'custom_columns' => [],
            'searchable' => [],
            'sortable' => [],
            'filterable' => [],
            'default_sort' => $defaultSort,
            'default_dir' => $defaultDir,
            'default_size' => 10,
            'actions' => true,
            'action_permissions' => [
                'edit' => 'edit',
                'delete' => 'delete',
                'show' => 'show',
            ],
            'action_routes' => [
                'edit' => 'edit',
                'delete' => 'delete',
                'show' => 'show',
            ],
        ];

        $config = array_merge($defaultConfig, $config);

        // Parameters with validation
        $size = min(max((int) $request->input('size', $config['default_size']), 1), 100); // Limit size between 1-100
        $page = max((int) $request->input('page', 1), 1); // Ensure page is at least 1

        // Use custom default only if no sort params or default params sent
        $requestSortColumn = $request->input('sort_column');
        $requestSortDir = in_array(strtolower($request->input('sort_dir')), ['asc', 'desc']) ?
            strtolower($request->input('sort_dir')) : 'desc'; // Validate sort direction

        // Use custom default if:
        // 1. No sort params sent, OR
        // 2. Default system params sent (id + desc), OR
        // 3. Page 1 and no explicit user sorting
        if (
            empty($requestSortColumn) ||
            ($requestSortColumn === 'id' && $requestSortDir === 'desc') ||
            ($page === 1 && ! $request->has('user_sorted'))
        ) {
            // Use our custom default
            $sortColumn = $config['default_sort'];
            $sortDir = $config['default_dir'];
        } else {
            // Use frontend params
            $sortColumn = $requestSortColumn;
            $sortDir = $requestSortDir;
        }

        $offset = ($page - 1) * $size;

        // Build query
        $query = $this->buildTableQuery($config);

        // Apply filters
        $this->applyTableFilters($query, $request, $config);

        // Apply search
        $this->applyTableSearch($query, $request, $config);

        // Apply group by if specified (before count for proper total)
        if (! empty($config['group_by'])) {
            $query->groupBy($config['group_by']);
        }

        // Apply having clauses - use parameterized queries for security
        if (! empty($config['having'])) {
            foreach ($config['having'] as $having) {
                if (is_array($having) && isset($having['column'], $having['operator'], $having['value'])) {
                    $query->having($having['column'], $having['operator'], $having['value']);
                } else {
                    // Fallback for backward compatibility, but log warning
                    Log::warning('Using raw having clause, consider using parameterized version', ['having' => $having]);
                    $query->havingRaw($having);
                }
            }
        }

        // Get total count - handle GROUP BY properly with safer approach
        if (! empty($config['group_by'])) {
            // For GROUP BY queries, use a safer counting method
            $countQuery = clone $query;
            $countQuery->select($config['group_by']);
            $total = $countQuery->distinct()->count($config['group_by'][0] ?? 'id');
        } else {
            $total = $query->count();
        }

        // Apply custom order by if specified, otherwise use default sorting
        if (! empty($config['order_by'])) {
            foreach ($config['order_by'] as $orderBy) {
                if (is_array($orderBy) && isset($orderBy['column'], $orderBy['direction'])) {
                    $query->orderBy($orderBy['column'], $orderBy['direction']);
                } else {
                    // Fallback for backward compatibility, but log warning
                    Log::warning('Using raw order by clause, consider using parameterized version', ['orderBy' => $orderBy]);
                    $query->orderByRaw($orderBy);
                }
            }
        } else {
            // Apply sorting
            $this->applyTableSorting($query, $sortColumn, $sortDir, $config);
        }

        // Log final query
        // \Log::info('Final Query SQL:', [
        //     'sql' => $query->toSql(),
        //     'bindings' => $query->getBindings()
        // ]);

        // Get data with pagination
        $data = $query->offset($offset)->limit($size)->get();

        // \Log::info('Query Results:', [
        //     'count' => $data->count(),
        //     'total' => $total
        // ]);

        // Apply custom columns if defined
        if (! empty($config['custom_columns'])) {
            $data->transform(function ($row) use ($config) {
                foreach ($config['custom_columns'] as $column => $callback) {
                    // Jalankan callback untuk menambahkan kolom custom
                    $row->$column = $callback($row);
                }

                return $row;
            });
        }

        // Add actions if enabled
        if ($config['actions']) {
            $data = $this->addTableActions($data, $user, $config);
        }

        $response = [
            'data' => $data,
            'meta' => [
                'total' => $total,
                'size' => (int) $size,
                'current_page' => (int) $page,
                'offset' => (int) $offset,
                'sort' => [
                    'column' => $sortColumn,
                    'dir' => $sortDir,
                ],
                'filter' => $this->getFilterMeta($request),
            ],
        ];

        // Add additional data if callback provided
        if (isset($config['additional_data']) && is_callable($config['additional_data'])) {
            $additionalData = $config['additional_data']();
            $response = [$response, $additionalData];
        }

        return response()->json($response, 200);
    }

    /**
     * Build table query with joins
     */
    protected function buildTableQuery(array $config)
    {
        $table = $config['table'];
        $alias = $config['alias'];

        // Validate table name to prevent injection
        if (! preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table)) {
            throw new \InvalidArgumentException('Invalid table name');
        }

        // Validate alias if provided
        if ($alias && ! preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $alias)) {
            throw new \InvalidArgumentException('Invalid table alias');
        }

        $query = DB::table($table . ($alias ? " as {$alias}" : ''));

        // Apply joins with validation
        foreach ($config['joins'] as $join) {
            if (is_array($join)) {
                // Validate join parameters
                $allowedJoinTypes = ['join', 'leftJoin', 'rightJoin', 'crossJoin'];
                $type = $join['type'] ?? 'leftJoin';

                if (! in_array($type, $allowedJoinTypes)) {
                    throw new \InvalidArgumentException('Invalid join type');
                }

                // Validate join table name
                $joinTable = explode(' as ', $join['table'])[0];
                if (! preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $joinTable)) {
                    throw new \InvalidArgumentException('Invalid join table name');
                }

                $query->{$type}($join['table'], $join['first'], $join['operator'] ?? '=', $join['second']);
            }
        }

        // Validate and apply select with whitelist approach
        $validatedSelect = [];
        foreach ($config['select'] as $column) {
            // Allow DB::raw expressions and string columns
            if (
                $column === '*' ||
                $column instanceof \Illuminate\Database\Query\Expression ||
                (is_string($column) && preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]*(\s+as\s+[a-zA-Z_][a-zA-Z0-9_]*)?$/i', $column))
            ) {
                $validatedSelect[] = $column;
            }
        }

        if (empty($validatedSelect)) {
            $validatedSelect = ['*']; // Fallback to all columns
        }

        $query->select($validatedSelect);

        return $query;
    }

    /**
     * Apply filters to query
     */
    protected function applyTableFilters($query, Request $request, array $config)
    {
        foreach ($config['filterable'] as $filter => $column) {
            $value = $request->input($filter);
            if (! empty($value)) {
                // Validate filter name
                if (! preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $filter)) {
                    Log::warning('Invalid filter name detected', ['filter' => $filter]);

                    continue;
                }

                // Handle callback functions (already validated by caller)
                if (is_callable($column)) {
                    $column($query, $value);
                } elseif ($filter === 'date') {
                    // Validate date format
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                        $query->whereDate($column, $value);
                    }
                } elseif ($filter === 'year' && $request->has('month')) {
                    $year = (int) $value;
                    $month = (int) $request->input('month');
                    if ($year >= 1900 && $year <= 2100 && $month >= 1 && $month <= 12) {
                        $query->whereYear($column, $year)
                            ->whereMonth($column, $month);
                    }
                } elseif ($filter === 'year') {
                    $year = (int) $value;
                    if ($year >= 1900 && $year <= 2100) {
                        $query->whereYear($column, $year);
                    }
                } elseif ($filter === 'month') {
                    $month = (int) $value;
                    if ($month >= 1 && $month <= 12) {
                        $query->whereMonth($column, $month);
                    }
                } else {
                    // Validate column name and sanitize value
                    if (is_string($column) && preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]*$/', $column)) {
                        $sanitizedValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                        $query->where($column, $sanitizedValue);
                    }
                }
            }
        }
    }

    /**
     * Apply search to query
     */
    // protected function applyTableSearch($query, Request $request, array $config)
    // {
    //     if ($request->has('search') && ! empty($request->search)) {
    //         $search = trim($request->search);

    //         // Basic sanitization - remove potentially dangerous characters
    //         $search = preg_replace('/[^\w\s\-\.@]/', '', $search);

    //         if (strlen($search) > 0 && strlen($search) <= 100) { // Limit search length
    //             $query->where(function ($q) use ($search, $config) {
    //                 foreach ($config['searchable'] as $column) {
    //                     // Validate column name to prevent injection
    //                     if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]*$/', $column)) {
    //                         $q->orWhere($column, 'LIKE', "%{$search}%");
    //                     }
    //                 }
    //             });
    //         }
    //     }
    // }

    protected function applyTableSearch($query, Request $request, array $searchableColumns)
    {
        $searchTerm = $request->input('search.value', '');

        if (!empty($searchTerm)) {
            $search = trim($searchTerm);

            // Basic sanitization
            $search = preg_replace('/[^\w\s\-\.@]/', '', $search);

            if (strlen($search) > 0 && strlen($search) <= 100) {
                $query->where(function ($q) use ($search, $searchableColumns) {
                    foreach ($searchableColumns as $column) {
                        if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]*$/', $column)) {
                            $q->orWhere($column, 'LIKE', "%{$search}%");
                        }
                    }
                });
            }
        }
    }

    /**
     * Apply sorting to query
     */
    protected function applyTableSorting($query, $sortColumn, $sortDir, array $config)
    {
        // Validate sort direction
        $sortDir = in_array(strtolower($sortDir), ['asc', 'desc']) ? strtolower($sortDir) : 'desc';

        // Skip DT_RowIndex or use default sort
        if ($sortColumn === 'DT_RowIndex' || empty($sortColumn)) {
            $sortField = $config['default_sort'];
            $sortDir = $config['default_dir'];
        } elseif (is_array($config['sortable'])) {
            $sortField = $config['sortable'][$sortColumn] ?? $config['default_sort'];
        } else {
            $sortField = $sortColumn;
        }

        // Validate sort field to prevent injection
        if (! preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]*$/', $sortField)) {
            $sortField = $config['default_sort']; // Fallback to safe default
            Log::warning('Invalid sort field detected, using default', ['requested' => $sortColumn, 'fallback' => $sortField]);
        }

        $query->orderBy($sortField, $sortDir);
    }

    /**
     * Add actions to data
     */
    protected function addTableActions($data, $user, array $config)
    {
        return $data->map(function ($item) use ($user, $config) {
            // Jika actions sudah ada dari custom_columns, gunakan itu
            if (isset($item->actions) && is_array($item->actions)) {
                return $item;
            }

            $actions = ['id' => $item->id];

            foreach ($config['action_permissions'] as $action => $permission) {
                $fullPermission = $permission . ' ' . $this->authorizeAction;
                $actions["can_{$action}"] = $user->can($fullPermission);

                if ($config['action_routes'][$action] && $user->can($fullPermission)) {
                    $actions["{$action}_url"] = route($config['action_routes'][$action], $item->id);
                }
            }

            $item->actions = $actions;

            return $item;
        });
    }

    /**
     * Get filter metadata
     */
    protected function getFilterMeta(Request $request)
    {
        return [
            'date' => $request->input('date') ? htmlspecialchars($request->input('date'), ENT_QUOTES, 'UTF-8') : null,
            'year' => $request->input('year') ? (int) $request->input('year') : null,
            'month' => $request->input('month') ? (int) $request->input('month') : null,
        ];
    }
}
