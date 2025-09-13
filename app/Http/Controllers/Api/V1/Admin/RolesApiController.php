<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Traits\ApiResponse;
use App\Traits\HandleErrors;
use App\Traits\HasApiJson;
use App\Traits\HasDynamicPermissions;
use App\Traits\HasQueryBuilder;
use App\Traits\ModernTableHelper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesApiController extends Controller
{
    use ApiResponse, HandleErrors, HasApiJson, HasDynamicPermissions, HasQueryBuilder, ModernTableHelper;
    use AuthorizesRequests;

    protected $authorizeAction = 'roles';

    protected $tableName = 'roles';

    protected array $tableSearchable = ['r.name', 'p.name']; // Untuk pencarian di tabel

    // Untuk HasApiJson trait
    protected array $jsonColumns = ['id', 'name'];

    protected array $jsonSearchable = ['name'];

    public function index(Request $request)
    {
        try {
            $this->authorize('read ' . $this->authorizeAction, 'web');

            // Ambil data mentah dari helper
            $result = $this->handleModernTableRequest($request, [
                'table'   => $this->tableName,
                'alias'   => 'r',
                'joins'   => [
                    [
                        'type'  => 'leftJoin',
                        'table' => 'role_has_permissions as rp',
                        'first' => 'r.id',
                        'second' => 'rp.role_id',
                    ],
                    [
                        'type'  => 'leftJoin',
                        'table' => 'permissions as p',
                        'first' => 'rp.permission_id',
                        'second' => 'p.id',
                    ],
                ],
                'select' => [
                    'r.id',
                    'r.name',
                    'r.created_at',
                    DB::raw('GROUP_CONCAT(DISTINCT p.name) as permissions'),
                    DB::raw('COUNT(DISTINCT p.id) as permissions_count'),
                ],
                'searchable' => $this->tableSearchable,
                'sortable'   => [
                    'id'          => 'r.id',
                    'name'        => 'r.name',
                    // 'permissions' => 'p.name',
                    'created_at'  => 'r.created_at'
                ],
                'default_sort' => 'r.id',
                'default_dir'  => 'desc',
                'filterable'   => [
                    'date'       => 'r.created_at',
                    'year'       => 'r.created_at',
                    'month'      => 'r.created_at',
                    'start_date' => fn($q, $v) => $q->whereDate('r.created_at', '>=', $v),
                    'end_date'   => fn($q, $v) => $q->whereDate('r.created_at', '<=', $v),
                ],
                'actions' => true,
                'action_permissions' => [
                    'edit'   => 'edit',
                    'delete' => 'delete',
                    'show'   => 'show',
                ],
                'action_routes' => [
                    'edit'   => 'roles.edit',
                    'delete' => 'api.roles.destroy',
                    'show'   => 'roles.show',
                ],
                'group_by' => ['r.id', 'r.name', 'r.created_at'],
                'column_mapping' => [
                    'id'          => 'r.id',
                    'name'        => 'r.name',
                    'permissions' => 'p.name',
                    'created_at'  => 'r.created_at'
                ],
                'transform' => function ($role) {
                    $permissions = $role->permissions ? array_unique(explode(',', $role->permissions)) : [];
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'permissions' => array_map(fn($perm) => ['name' => trim($perm)], array_filter($permissions)),
                        'permissions_count' => (int) $role->permissions_count,
                        'created_at' => date('Y-m-d', strtotime($role->created_at)),
                    ];
                },
                'meta' => [
                    'permissions' => $this->generatePermissions(),
                    'api_version' => 'v1',
                    'timestamp'   => now()->toISOString(),
                ]
            ]);

            return $result;
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }


    public function store(RoleRequest $request)
    {
        try {
            $this->authorize('create ' . $this->authorizeAction, 'web');

            DB::beginTransaction();
            $validated = $request->validated();
            $role = Role::create([
                'name' => $validated['name'],
                'guard_name' => 'web', // default guard
            ]);

            // Sinkronisasi permissions langsung
            if (! empty($validated['permissions'])) {
                $permissions = Permission::whereIn('id', $validated['permissions'])->get();
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions([]);
            }

            DB::commit();

            return $this->createdResponse($role, 'Data berhasil dibuat');
        } catch (\Throwable $e) {
            return $this->handleException($e); // DB::rollBack(); sudah ada di handleException() dari trait HandleErrors
        }
    }

    public function show($id)
    {
        $this->authorize('show ' . $this->authorizeAction, 'web');

        try {
            $results = Role::with('permissions')->findOrFail($id);

            return $this->successResponse(
                $results,
                'Data berhasil diambil',
            );
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(RoleRequest $request, $id)
    {

        try {
            $this->authorize('edit ' . $this->authorizeAction, 'web');

            DB::beginTransaction();
            $role = Role::findOrFail($id);
            $validated = $request->validated();
            $role->update([
                'name' => $validated['name'],
            ]);

            // Sinkronisasi permissions
            if (! empty($validated['permissions'])) {
                $permissions = Permission::whereIn('id', $validated['permissions'])->get();
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions([]);
            }

            DB::commit();

            return $this->updatedResponse($role, 'Data berhasil diperbarui');
        } catch (\Throwable $e) {
            return $this->handleException($e); // DB::rollBack(); sudah ada di handleException() dari trait HandleErrors
        }
    }

    public function destroy($id)
    {
        $this->authorize('delete ' . $this->authorizeAction, 'web');

        return $this->singleDelete($id); // dari trait HasQueryBuilder
    }

    public function getByIds(Request $request)
    {
        try {
            $ids = $request->get('ids', '');

            // Handle both string and array input
            $idsArray = is_array($ids) ? array_filter($ids) : array_filter(explode(',', (string) $ids));

            $includePermissions = $request->get('include_permissions', false);

            if ($idsArray === []) {
                return response()->json(['data' => [], 'permissions' => []]);
            }

            $query = Role::whereIn('id', $idsArray);

            if ($includePermissions) {
                $query->with('permissions');
            }

            $roles = $query->select(['id', 'name'])->get();

            $response = ['data' => $roles];

            if ($includePermissions) {
                // Collect all unique permission IDs from selected roles
                $permissionIds = [];
                foreach ($roles as $role) {
                    foreach ($role->permissions as $permission) {
                        $permissionIds[] = $permission->id;
                    }
                }
                $response['permissions'] = array_unique($permissionIds);
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Get role permissions with server-side pagination
     */
    public function getPermissionsPaginated(Request $request, $id)
    {
        try {
            $this->authorize('read ' . $this->authorizeAction, 'web');

            $role = Role::with('permissions')->findOrFail($id);

            // Group permissions by module
            $groupedPermissions = $role->permissions->groupBy(function ($permission): string {
                $parts = explode(' ', strtolower((string) $permission->name));

                return count($parts) > 1 ? $parts[count($parts) - 1] : 'general';
            });

            // Pagination
            $page = (int) $request->get('page', 1);
            $limit = min(12, (int) $request->get('limit', 6));
            $offset = ($page - 1) * $limit;

            $modules = $groupedPermissions->map(fn($permissions, $moduleName): array => [
                'name' => $moduleName,
                'permissions' => $permissions->map(fn($p): array => ['id' => $p->id, 'name' => $p->name])->values(),
            ])->values();

            $totalModules = $modules->count();
            $paginatedModules = $modules->slice($offset, $limit)->values();
            $totalPages = ceil($totalModules / $limit);

            return response()->json([
                'modules' => $paginatedModules,
                'total' => $role->permissions->count(),
                'totalModules' => $totalModules,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'limit' => $limit,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
