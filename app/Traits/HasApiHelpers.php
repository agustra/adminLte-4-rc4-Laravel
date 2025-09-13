<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HasApiHelpers
{
    use ApiResponse, HandleErrors;

    /**
     * Dapatkan model untuk bulkDelete
     */
    protected function getDeleteModel()
    {
        if (property_exists($this, 'thisModel')) {
            return $this->thisModel;
        }

        return property_exists($this, 'model') ? $this->model : null;
    }

    /**
     * single delete data
     */
    public function singleDelete($id)
    {
        try {
            $model = $this->getDeleteModel();
            if (! $model) {
                throw new \Exception('Model tidak ditemukan di trait.');
            }

            $data = $model::findOrFail($id);
            $dataName = $data->name ?? $data->title ?? 'Item';

            $data->delete();

            return $this->deletedResponse([
                'id' => $id,
                'name' => $dataName,
            ], 'Data berhasil dihapus');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Bulk delete data dengan validasi aman
     */
    public function bulkDelete(Request $request)
    {
        // Gunakan permission yang didefinisikan di controller
        if (! empty($this->authorizeAction)) {
            $this->authorize('delete '.$this->authorizeAction, 'web');
        }

        try {
            $model = $this->getDeleteModel();
            if (! $model) {
                return $this->errorResponse('Model tidak ditemukan di trait.', 500);
            }

            $ids = array_map('intval', array_filter((array) $request->ids, 'is_numeric'));

            if (empty($ids)) {
                return $this->errorResponse('Tidak ada data yang valid untuk dihapus.', 400);
            }

            $deleted = $model::whereIn('id', $ids)->delete();

            return $this->deletedResponse([
                'deleted_count' => $deleted,
            ], 'Data berhasil dihapus');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Get data by IDs untuk TomSelect auto-select
     */
    public function byIds(Request $request)
    {
        try {
            $model = $this->getDeleteModel();
            if (! $model) {
                return $this->errorResponse('Model tidak ditemukan di trait.', 500);
            }

            $ids = array_filter((array) $request->get('ids', []), 'is_numeric');

            if (empty($ids)) {
                return $this->successResponse([]);
            }

            $columns = property_exists($this, 'jsonColumns') ? $this->jsonColumns : ['id', 'name'];
            $items = $model::whereIn('id', $ids)->get($columns);

            return $this->successResponse($items);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Return JSON list (all data)
     */
    public function json(Request $request)
    {
        // Gunakan permission yang didefinisikan di controller
        if (! empty($this->authorizeAction)) {
            $this->authorize('read '.$this->authorizeAction, 'web');
        }

        try {
            $model = $this->getDeleteModel();
            if (! $model) {
                return $this->errorResponse('Model tidak ditemukan di trait.', 500);
            }

            $columns = property_exists($this, 'jsonColumns') ? $this->jsonColumns : ['id', 'name'];
            $searchable = property_exists($this, 'jsonSearchable') ? $this->jsonSearchable : ['id'];

            $page = (int) $request->get('page', 1);
            $limit = min(50, max(1, (int) $request->get('limit', 10)));
            $search = htmlspecialchars($request->get('search', ''), ENT_QUOTES, 'UTF-8');

            $query = $model::query();

            if ($search) {
                $query->where(function ($q) use ($searchable, $search) {
                    foreach ((array) $searchable as $col) {
                        $q->orWhere($col, 'LIKE', "%{$search}%");
                    }
                });
            }

            $sortColumn = $request->get('sort_column', $searchable[0]);
            $sortDir = $request->get('sort_dir', 'asc');

            // pastikan kolom valid di model
            if (! in_array($sortColumn, $columns)) {
                $sortColumn = $columns[0];
            }

            $total = $query->count();

            $items = $query->orderBy($sortColumn, $sortDir)
                ->skip(($page - 1) * $limit)
                ->take($limit)
                ->get($columns);

            return $this->successResponse([
                'items' => $items,
                'has_more' => ($page * $limit) < $total,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    protected function validateAndGetParams(Request $request)
    {
        // Ambil default dari controller
        $defaultSortColumn = property_exists($this, 'sortDefaultColumn') ? $this->sortDefaultColumn : 'id';
        $defaultSortDirection = property_exists($this, 'sortDefaultDirection') ? $this->sortDefaultDirection : 'desc';

        $sortColumn = $request->input('sort_column', '');
        $sortDirection = strtolower($request->input('sort_dir', ''));

        // Jika tidak ada param atau param sama dengan default global (id, desc), pakai default halaman
        if ($sortColumn === '' || ($sortColumn === 'id' && $sortDirection === 'desc')) {
            $sortColumn = $defaultSortColumn;
            $sortDirection = $defaultSortDirection;
        } elseif (! in_array($sortDirection, ['asc', 'desc'])) {
            // Jika sort_dir tidak valid, fallback ke default direction
            $sortDirection = $defaultSortDirection;
        }

        return [
            'perPage' => max(1, (int) $request->get('size', 10)),
            'offset' => max(0, (int) $request->get('offset', 0)),
            'search' => $request->get('search', ''),
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
            'date' => $request->date,
            'year' => $request->year,
            'month' => $request->month,
        ];
    }

    protected function buildBaseQuery($model)
    {
        return $model::query();
    }

    protected function applyFiltersAndSearch($query, $params, $searchableColumns)
    {
        // Filter berdasarkan tanggal/bulan/tahun jika ada
        if (! empty($params['date'])) {
            $query->whereDate('created_at', $params['date']);
        } elseif (! empty($params['year']) && ! empty($params['month'])) {
            $query->whereYear('created_at', $params['year'])
                ->whereMonth('created_at', $params['month']);
        }

        // Pencarian dengan support relasi
        if (! empty($params['search'])) {
            $query->where(function ($q) use ($params, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    if (str_contains($column, '.')) {
                        // Relasi search (contoh: 'permissions.name')
                        [$relation, $relationColumn] = explode('.', $column, 2);
                        $q->orWhereHas($relation, function ($subQuery) use ($params, $relationColumn) {
                            $subQuery->where($relationColumn, 'like', "%{$params['search']}%");
                        });
                    } else {
                        // Direct column search
                        $q->orWhere($column, 'like', "%{$params['search']}%");
                    }
                }
            });
        }

        return $query;
    }

    protected function applySorting($query, $params, $allowedSortColumns)
    {
        // Support both array formats: ['id', 'name'] or ['id' => 'id', 'name' => 'name']
        if (is_array($allowedSortColumns) && isset($allowedSortColumns[$params['sortColumn']])) {
            $sortColumn = $allowedSortColumns[$params['sortColumn']];
        } elseif (is_array($allowedSortColumns) && in_array($params['sortColumn'], $allowedSortColumns)) {
            $sortColumn = $params['sortColumn'];
        } else {
            $sortColumn = 'id';
        }

        return $query->orderBy($sortColumn, $params['sortDirection']);
    }

    protected function formatResponse($data, $total, $params)
    {
        $currentPage = (int) floor($params['offset'] / $params['perPage']) + 1;

        return response()->json([
            'data' => $data,
            'meta' => [
                'total' => $total,
                'size' => $params['perPage'],
                'currentPage' => $currentPage,
                'offset' => $params['offset'],
                'sort' => [
                    'column' => $params['sortColumn'],
                    'dir' => $params['sortDirection'],
                ],
                'filter' => [
                    'date' => $params['date'],
                    'year' => $params['year'],
                    'month' => $params['month'],
                ],
            ],
        ], 200);
    }
}
