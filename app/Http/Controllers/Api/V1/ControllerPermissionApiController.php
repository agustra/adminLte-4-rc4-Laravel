<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ControllerPermission;
use App\Traits\ApiResponse;
use App\Traits\HandleErrors;
use App\Traits\HasDynamicPermissions;
use App\Traits\HasQueryBuilder;
use App\Traits\ModernTableHelper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ControllerPermissionApiController extends Controller
{
    use ApiResponse, HandleErrors, HasDynamicPermissions, HasQueryBuilder, ModernTableHelper;
    use AuthorizesRequests;

    protected $authorizeAction = 'controller-permissions';

    protected $tableName = 'controller_permissions';

    protected array $tableSearchable = ['cp.controller', 'cp.method', 'cp.is_active', 'cp.permissions'];

    public function index(Request $request)
    {
        try {
            $this->authorize('read ' . $this->authorizeAction, 'web');
            return $this->handleModernTableRequest($request, [
                'table' => $this->tableName,
                'alias' => 'cp',
                'select' => [
                    'cp.id',
                    'cp.controller',
                    'cp.method',
                    DB::raw('CAST(cp.permissions AS JSON) as permissions'),
                    'cp.is_active',
                ],
                'searchable' => $this->tableSearchable,
                'sortable' => [
                    'id' => 'cp.id',
                    'controller' => 'cp.controller',
                    'method' => 'cp.method',
                    'permission' => 'permissions',
                    'is_active' => 'cp.is_active',
                ],
                'default_sort' => 'cp.id',
                'default_dir' => 'desc',
                'filterable' => [
                    'date' => 'cp.created_at',
                    'year' => 'cp.created_at',
                    'month' => 'cp.created_at',
                    'start_date' => function ($query, $value) {
                        $query->whereDate('cp.created_at', '>=', $value);
                    },
                    'end_date' => function ($query, $value) {
                        $query->whereDate('cp.created_at', '<=', $value);
                    },
                ],
                'actions' => true,
                'action_permissions' => [
                    'edit' => 'edit',
                    'delete' => 'delete',
                    'show' => 'show',
                ],
                'action_routes' => [
                    'edit' => 'controller-permissions.edit',
                    'delete' => 'api.controller-permissions.destroy',
                    'show' => 'controller-permissions.show',
                ],
                'meta' => [
                    'permissions' => $this->generatePermissions(),
                    'api_version' => 'v1',
                    'timestamp' => now()->toISOString(),
                ]
            ]);

            return response()->json($data);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'controller' => 'required|string',
                'method' => [
                    'required',
                    'string',
                    \Illuminate\Validation\Rule::unique('controller_permissions', 'method')
                        ->where('controller', $request->controller)
                ],
                'permissions' => 'required|array',
                'permissions.*' => 'string',
                'is_active' => 'boolean',
            ]);

            $validated['is_active'] = $request->has('is_active');

            ControllerPermission::create($validated);
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Mapping permission berhasil dibuat',
            ], 201);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $controllerPermission = ControllerPermission::findOrFail($id);

            // Only check for duplicates if controller or method is actually changing
            $isControllerChanging = $controllerPermission->controller !== $request->controller;
            $isMethodChanging = $controllerPermission->method !== $request->method;
            
            if ($isControllerChanging || $isMethodChanging) {
                // Custom validation for unique combination
                $existingRecord = ControllerPermission::where('controller', $request->controller)
                    ->where('method', $request->method)
                    ->where('id', '!=', $id)
                    ->first();

                if ($existingRecord) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Validation failed',
                        'errors' => [
                            'method' => [
                                "Kombinasi controller dan method sudah digunakan oleh record ID: {$existingRecord->id}. "
                                . "Gunakan method lain atau hapus record yang sudah ada terlebih dahulu."
                            ]
                        ],
                        'meta' => [
                            'error_code' => 'VALIDATION_ERROR',
                            'existing_record_id' => $existingRecord->id,
                            'api_version' => 'v1',
                            'timestamp' => now()->toISOString(),
                        ]
                    ], 422);
                }
            }

            $validated = $request->validate([
                'controller' => 'required|string',
                'method' => 'required|string',
                'permissions' => 'required|array',
                'permissions.*' => 'string',
                'is_active' => 'boolean',
            ]);

            $validated['is_active'] = $request->has('is_active');

            $controllerPermission->update($validated);
            DB::commit();



            return response()->json([
                'status' => 'success',
                'message' => 'Mapping permission berhasil diperbarui',
            ]);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        return $this->singleDelete($id);
    }

    public function bulkDelete(Request $request)
    {
        return $this->bulkDelete($request);
    }
}
