<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait ModernTableHelper
{
    /**
     * Handle ModernTable.js request with full features
     */
    protected function handleModernTableRequest(Request $request, array $config = [])
    {
        try {
            // Extract ModernTable parameters
            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            $draw = (int) $request->input('draw', 1); 

            // Build base query
            $baseQuery = $this->buildModernTableQuery($config);

            // Apply search
            $this->applyModernTableSearch($baseQuery, $request, $config);

            // Apply individual column search
            $this->applyModernTableColumnSearch($baseQuery, $request, $config);

            // Apply filters
            $this->applyModernTableFilters($baseQuery, $request, $config);

            // Apply sorting
            $this->applyModernTableSorting($baseQuery, $request, $config);

            // Calculate totals
            $recordsTotal = $this->getRecordsTotal($request, $config);
            $recordsFiltered = $this->getRecordsFiltered($baseQuery, $config);

            // Get paginated data
            $data = $baseQuery->skip($start)->take($length)->get();

            // Apply custom columns if defined
            if (!empty($config['custom_columns'])) {
                $data->transform(function ($row) use ($config) {
                    foreach ($config['custom_columns'] as $column => $callback) {
                        // Jalankan callback untuk menambahkan kolom custom
                        $row->$column = $callback($row);
                    }
                    return $row;
                });
            }

            // Transform data
            if (isset($config['transform']) && is_callable($config['transform'])) {
                $data = $data->map($config['transform']);
            }

            // Add actions if configured
            if (isset($config['actions']) && $config['actions']) {
                $data = $this->addModernTableActions($data, $config);
            }

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
                'meta' => $config['meta'] ?? []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Build base query with joins
     */
    protected function buildModernTableQuery(array $config)
    {
        $query = DB::table($config['table'] . ($config['alias'] ? ' as ' . $config['alias'] : ''));

        // Apply joins
        if (isset($config['joins'])) {
            foreach ($config['joins'] as $join) {
                $type = $join['type'] ?? 'leftJoin';
                $query->{$type}($join['table'], $join['first'], $join['operator'] ?? '=', $join['second']);
            }
        }

        // Apply select
        if (isset($config['select'])) {
            $query->select($config['select']);
        }

        // Apply group by
        if (isset($config['group_by'])) {
            $query->groupBy($config['group_by']);
        }

        return $query;
    }

    /**
     * Apply search to query
     */
    protected function applyModernTableSearch($query, Request $request, array $config)
    {
        $searchTerm = $request->input('search.value', '');

        if (!empty($searchTerm) && isset($config['searchable'])) {
            $search = trim($searchTerm);
            $search = preg_replace('/[^\w\s\-\.@]/', '', $search);

            if (strlen($search) > 0 && strlen($search) <= 100) {
                $query->where(function ($q) use ($search, $config) {
                    foreach ($config['searchable'] as $column) {
                        if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]*$/', $column)) {
                            // Check if there's a search transformer for this column
                            $transformedValue = $search;
                            if (isset($config['search_transformers'][$column])) {
                                $transformer = $config['search_transformers'][$column];
                                if (is_callable($transformer)) {
                                    $transformedValue = $transformer($search);
                                }
                            }
                            
                            // Use exact match if transformed value is different from original
                            if ($transformedValue !== $search && in_array($transformedValue, ['0', '1'])) {
                                $q->orWhere($column, '=', $transformedValue);
                            } else {
                                $q->orWhereRaw("LOWER({$column}) LIKE LOWER(?)", ["%{$transformedValue}%"]);
                            }
                        }
                    }
                });
            }
        }
    }

    /**
     * Apply individual column search to query
     */
    protected function applyModernTableColumnSearch($query, Request $request, array $config): void
    {
        $columns = $request->input('columns', []);

        if (empty($columns) || !is_array($columns)) {
            return;
        }

        foreach ($columns as $column) {
            $searchValue = $column['search']['value'] ?? null;
            $columnData  = $column['data'] ?? null;

            if ($searchValue === null || $columnData === null) {
                continue;
            }

            // Cari kolom DB yg sesuai
            $searchColumns = $this->mapColumnToSearchable($columnData, $config);

            if (empty($searchColumns)) {
                continue;
            }

            $query->where(function ($q) use ($searchColumns, $searchValue, $columnData, $config) {
                foreach ($searchColumns as $dbColumn) {
                    if ($columnData === 'created_at') {
                        // dukung format YYYY-MM-DD dan DD/MM/YYYY
                        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $searchValue)) {
                            $q->orWhereDate($dbColumn, $searchValue);
                        } elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $searchValue)) {
                            $date = \DateTime::createFromFormat('d/m/Y', $searchValue);
                            if ($date) {
                                $q->orWhereDate($dbColumn, $date->format('Y-m-d'));
                            }
                        }
                    } else {
                        // Check if there's a search transformer for this column
                        $transformedValue = $searchValue;
                        if (isset($config['search_transformers'][$dbColumn])) {
                            $transformer = $config['search_transformers'][$dbColumn];
                            if (is_callable($transformer)) {
                                $transformedValue = $transformer($searchValue);
                            }
                        }
                        
                        // Use exact match if transformed value is different from original
                        if ($transformedValue !== $searchValue && in_array($transformedValue, ['0', '1'])) {
                            $q->orWhere($dbColumn, '=', $transformedValue);
                        } else {
                            $q->orWhereRaw("LOWER({$dbColumn}) LIKE LOWER(?)", ["%{$transformedValue}%"]);
                        }
                    }
                }
            });
        }
    }

    /**
     * Map frontend column to real database columns
     */
    protected function mapColumnToSearchable(string $column, array $config = []): array
    {
        // 1. Cek column_mapping manual
        if (!empty($config['column_mapping'][$column])) {
            $mapping = $config['column_mapping'][$column];
            return is_array($mapping) ? $mapping : [$mapping];
        }

        // 2. Ambil daftar searchable dari config / properti
        $searchable = $config['searchable'] ?? $this->tableSearchable ?? [];
        $matches    = [];

        foreach ($searchable as $dbColumn) {
            $plain = str_contains($dbColumn, '.')
                ? substr(strrchr($dbColumn, '.'), 1)
                : $dbColumn;

            if ($plain === $column) {
                $matches[] = $dbColumn;
            }
        }

        return $matches;
    }

    /**
     * Apply filters to query
     */
    protected function applyModernTableFilters($query, Request $request, array $config)
    {
        if (!isset($config['filterable'])) return;

        foreach ($config['filterable'] as $filter => $column) {
            $value = $request->input($filter);
            if (empty($value)) continue;

            if (is_callable($column)) {
                $column($query, $value);
            } elseif ($filter === 'date') {
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    $query->whereDate($column, $value);
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
                if (is_string($column) && preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]*$/', $column)) {
                    $query->where($column, $value);
                }
            }
        }
    }

    /**
     * Apply sorting to query
     */
    protected function applyModernTableSorting($query, Request $request, array $config)
    {
        if ($request->has('order') && is_array($request->order)) {
            $order = $request->order[0];
            $columnIndex = $order['column'] ?? 0;
            $direction = $order['dir'] ?? 'asc';

            // Get column name from columns data
            $columnName = $request->input("columns.{$columnIndex}.data");

            if ($columnName && isset($config['sortable'][$columnName])) {
                $sortColumn = $config['sortable'][$columnName];
                $query->orderBy($sortColumn, $direction);
                return;
            }
        }

        // Default sorting
        $defaultSort = $config['default_sort'] ?? 'id';
        $defaultDir = $config['default_dir'] ?? 'desc';
        $query->orderBy($defaultSort, $defaultDir);
    }

    /**
     * Get total records count (with filters applied)
     */
    protected function getRecordsTotal(Request $request, array $config)
    {
        // Build query with same filters as main query
        $totalQuery = $this->buildModernTableQuery($config);

        // Apply same searches and filters
        $this->applyModernTableColumnSearch($totalQuery, $request, $config);
        $this->applyModernTableFilters($totalQuery, $request, $config);

        // Count with group by if needed
        if (isset($config['group_by'])) {
            $subQuery = $totalQuery->select($config['group_by'][0])->toSql();
            return DB::table(DB::raw("({$subQuery}) as sub"))
                ->mergeBindings($totalQuery)
                ->count();
        }

        return $totalQuery->count();
    }

    /**
     * Get filtered records count
     */
    protected function getRecordsFiltered($baseQuery, array $config)
    {
        // Clone query for counting
        $countQuery = clone $baseQuery;

        // Remove orders and limits for counting
        $countQuery->orders = null;
        $countQuery->limit = null;
        $countQuery->offset = null;

        if (isset($config['group_by'])) {
            // For GROUP BY queries, count distinct groups
            $subQuery = $countQuery->select($config['group_by'][0])->toSql();
            return DB::table(DB::raw("({$subQuery}) as sub"))
                ->mergeBindings($countQuery)
                ->count();
        }

        return $countQuery->count();
    }

    /**
     * Add action buttons to data
     */
    protected function addModernTableActions($data, array $config)
    {
        $user = request()->user();

        return $data->map(function ($item) use ($user, $config) {
            // Convert to array if it's an object
            $itemArray = is_object($item) ? (array) $item : $item;

            // Jika actions sudah ada, gunakan itu
            if (isset($itemArray['actions']) && is_array($itemArray['actions'])) {
                return $itemArray;
            }

            $itemId = $itemArray['id'] ?? null;
            if (!$itemId) {
                return $itemArray;
            }

            $actions = ['id' => $itemId];

            foreach ($config['action_permissions'] as $action => $permission) {
                $fullPermission = $permission . ' ' . $this->authorizeAction;
                $actions["can_{$action}"] = $user->can($fullPermission);

                if (isset($config['action_routes'][$action]) && $user->can($fullPermission)) {
                    $actions["{$action}_url"] = route($config['action_routes'][$action], $itemId);
                }
            }

            $itemArray['actions'] = $actions;

            return $itemArray;
        });
    }
}
