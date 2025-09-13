<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait HasQueryBuilder
{
    use HandleErrors;

    /**
     * Bulk delete dengan Query Builder
     */
    public function bulkDelete(Request $request)
    {
        if (! empty($this->authorizeAction)) {
            $this->authorize('delete '.$this->authorizeAction, 'web');
        }

        try {
            $table = $this->getTableName();
            $ids = array_map('intval', array_filter((array) $request->ids, 'is_numeric'));

            if (empty($ids)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada data yang valid untuk dihapus.',
                ], 400);
            }

            $deleted = DB::table($table)->whereIn('id', $ids)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus',
                'results' => $deleted,
            ], 200);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Single delete dengan Query Builder
     */
    public function singleDelete($id)
    {
        try {
            $table = $this->getTableName();
            $data = DB::table($table)->where('id', $id)->first();

            if (! $data) {
                throw new \Exception('Data tidak ditemukan');
            }

            $dataName = $data->name ?? $data->title ?? 'Item';
            DB::table($table)->where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus',
                'results' => ['id' => $id, 'name' => $dataName],
            ], 200);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Get table name from model or property
     */
    protected function getTableName()
    {
        if (property_exists($this, 'tableName')) {
            return $this->tableName;
        }

        if (property_exists($this, 'model')) {
            return (new $this->model)->getTable();
        }

        throw new \Exception('Table name tidak ditemukan. Set property tableName atau model.');
    }
}
