<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait Crud
{
    use HandleErrors;

    /**
     * Get validation rules for store/update
     * Override this in your controller to set custom rules
     */
    protected function getValidationRules($id = null): array
    {
        return [];
    }

    /**
     * Get custom validation messages
     * Override this in your controller to set custom messages
     */
    protected function getValidationMessages(): array
    {
        return [];
    }

    /**
     * Get model for CRUD operations
     * Override this in your controller to set the model
     */
    protected function getCrudModel()
    {
        return $this->thisModel ?? null;
    }

    /**
     * Get resource class for response
     * Override this in your controller to set custom resource
     */
    protected function getResourceClass(): null
    {
        return null;
    }

    /**
     * Transform data before save
     * Override this in your controller to modify data before saving
     *
     * @param  array  $data  The validated data to transform
     * @return array The transformed data ready for database operations
     */
    protected function transformData($data)
    {
        return $data;
    }

    /**
     * Store new record
     */
    protected function storeSingle(Request $request)
    {
        try {
            $model = $this->getCrudModel();
            if (! $model) {
                throw new \Exception('Model tidak ditemukan di trait.');
            }

            // Validasi
            $validator = Validator::make(
                $request->all(),
                $this->getValidationRules(),
                $this->getValidationMessages()
            );

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            // Transform data jika diperlukan
            $data = $this->transformData($validator->validated());

            // Simpan data
            $result = $model::create($data);

            // Format response dengan resource jika ada
            $resourceClass = $this->getResourceClass();
            $formattedResult = $resourceClass && class_exists($resourceClass) ? new $resourceClass($result) : $result;

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil ditambahkan',
                'results' => $formattedResult,
            ], 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Update existing record
     */
    protected function updateSingle(Request $request, $id)
    {
        try {
            $model = $this->getCrudModel();
            if (! $model) {
                throw new \Exception('Model tidak ditemukan di trait.');
            }

            // Use findOrFail for better exception handling
            $item = $model::findOrFail($id);

            // Validasi
            $validator = Validator::make(
                $request->all(),
                $this->getValidationRules($id),
                $this->getValidationMessages()
            );

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            // Transform data jika diperlukan
            $data = $this->transformData($validator->validated());

            // Update data
            $item->update($data);

            // Format response dengan resource jika ada
            $resourceClass = $this->getResourceClass();
            $formattedResult = $resourceClass && class_exists($resourceClass) ? new $resourceClass($item) : $item;

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil diperbarui',
                'results' => $formattedResult,
            ], 200);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Delete single record
     */
    protected function deleteSingle($id)
    {
        try {
            $model = $this->getCrudModel();
            if (! $model) {
                throw new \Exception('Model tidak ditemukan di trait.');
            }

            $data = $model::findOrFail($id);
            $dataName = $data->name ?? $data->title ?? 'Item';

            $data->delete();

            // Return minimal data after deletion
            $formattedResult = ['id' => $id, 'name' => $dataName];

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus',
                'results' => $formattedResult,
            ], 200);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Delete multiple records
     */
    protected function deleteBatch(Request $request)
    {
        try {
            $model = $this->getCrudModel();
            if (! $model) {
                throw new \Exception('Model tidak ditemukan di trait.');
            }

            $ids = array_filter($request->ids, 'is_numeric');

            if ($ids === []) {
                throw new \Exception('Tidak ada data yang valid untuk dihapus.');
            }

            // Validate and count existing records
            $existingCount = $model::whereIn('id', $ids)->count();

            if ($existingCount === 0) {
                throw new \Exception('Data tidak ditemukan.');
            }

            // Delete records
            $deletedCount = $model::whereIn('id', $ids)->delete();

            // Return count instead of full data
            $formattedResult = ['deleted_count' => $deletedCount];

            return response()->json([
                'status' => 'success',
                'message' => count($ids).' data berhasil dihapus',
                'results' => $formattedResult,
            ], 200);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
