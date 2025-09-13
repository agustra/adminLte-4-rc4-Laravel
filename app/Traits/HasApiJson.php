<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait HasApiJson
{
    /**
     * Get data by IDs untuk TomSelect auto-select (Query Builder version)
     */
    public function getByIds(Request $request)
    {
        try {
            if (! property_exists($this, 'tableName') || empty($this->tableName)) {
                throw new \Exception('Property tableName harus didefinisikan di controller');
            }
            $table = $this->tableName;
            $ids = array_filter((array) $request->get('ids', []), 'is_numeric');

            if (empty($ids)) {
                return response()->json(['data' => []]);
            }

            $columns = property_exists($this, 'jsonColumns') ? $this->jsonColumns : ['id', 'name'];
            $items = DB::table($table)->whereIn('id', $ids)->get($columns);

            return response()->json(['data' => $items]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Return JSON list dengan Query Builder
     */
    public function json(Request $request)
    {
        if (! empty($this->authorizeAction)) {
            $this->authorize('read '.$this->authorizeAction, 'web');
        }

        try {
            if (! property_exists($this, 'tableName') || empty($this->tableName)) {
                throw new \Exception('Property tableName harus didefinisikan di controller');
            }
            $table = $this->tableName;
            $columns = property_exists($this, 'jsonColumns') ? $this->jsonColumns : ['id', 'name'];
            $searchable = property_exists($this, 'jsonSearchable') ? $this->jsonSearchable : ['id'];

            $page = (int) $request->get('page', 1);
            $limit = min(50, max(1, (int) $request->get('limit', 10)));
            $search = htmlspecialchars($request->get('search', ''), ENT_QUOTES, 'UTF-8');

            $query = DB::table($table);

            if ($search) {
                $query->where(function ($q) use ($searchable, $search) {
                    foreach ((array) $searchable as $col) {
                        $q->orWhere($col, 'LIKE', "%{$search}%");
                    }
                });
            }

            $sortColumn = $request->get('sort_column', $searchable[0]);
            $sortDir = $request->get('sort_dir', 'asc');

            if (! in_array($sortColumn, $columns)) {
                $sortColumn = $columns[0];
            }

            $total = $query->count();
            $items = $query->orderBy($sortColumn, $sortDir)
                ->offset(($page - 1) * $limit)
                ->limit($limit)
                ->get($columns);

            return response()->json([
                'data' => $items,
                'has_more' => ($page * $limit) < $total,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
