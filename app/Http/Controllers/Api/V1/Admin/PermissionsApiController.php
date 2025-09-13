<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionRequest;
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

class PermissionsApiController extends Controller
{
    use ApiResponse, HandleErrors, HasApiJson, HasDynamicPermissions, HasQueryBuilder, ModernTableHelper;
    use AuthorizesRequests;

    protected $authorizeAction = 'permissions';

    protected $tableName = 'permissions';

    protected array $tableSearchable = ['p.id', 'p.name', 'r.name']; // Untuk pencarian di tabel

    // Untuk HasApiJson trait
    protected array $jsonColumns = ['id', 'name'];

    protected array $jsonSearchable = ['name'];

    public function index(Request $request)
    {
        try {
            $this->authorize('read ' . $this->authorizeAction, 'web');

            return $this->handleModernTableRequest($request, [
                'table' => $this->tableName, // table permissions
                'alias' => 'p',
                'joins' => [
                    [
                        'type' => 'leftJoin',
                        'table' => 'role_has_permissions as rp',
                        'first' => 'p.id',
                        'second' => 'rp.permission_id',
                    ],
                    [
                        'type' => 'leftJoin',
                        'table' => 'roles as r',
                        'first' => 'rp.role_id',
                        'second' => 'r.id',
                    ],
                ],
                'select' => [
                    'p.id',
                    'p.name',
                    'p.guard_name',
                    'p.created_at',
                    DB::raw('GROUP_CONCAT(DISTINCT r.name) as roles'),
                    DB::raw('(SELECT r2.name FROM role_has_permissions rp2 JOIN roles r2 ON rp2.role_id = r2.id WHERE rp2.permission_id = p.id ORDER BY r2.name LIMIT 1) as first_role_name'),
                ],
                'searchable' => [
                    'p.id',
                    'p.name',
                    'r.name',
                    'p.guard_name',
                ],
                'sortable' => [
                    'id' => 'p.id',
                    'name' => 'p.name',
                    'roles' => 'first_role_name',
                    'guard_name' => 'p.guard_name',
                    'created_at' => 'p.created_at',
                ],
                'default_sort' => 'p.id',
                'default_dir' => 'desc',
                'filterable' => [
                    'date' => 'p.created_at',
                    'year' => 'p.created_at',
                    'month' => 'p.created_at',
                    'start_date' => function ($query, $value) {
                        $query->whereDate('p.created_at', '>=', $value);
                    },
                    'end_date' => function ($query, $value) {
                        $query->whereDate('p.created_at', '<=', $value);
                    },
                ],
                'actions' => true,
                'action_permissions' => [
                    'edit' => 'edit',
                    'delete' => 'delete',
                    'show' => 'show',
                ],
                'action_routes' => [
                    'edit' => 'permissions.edit',
                    'delete' => 'api.permissions.destroy',
                    'show' => 'permissions.show',
                ],
                'group_by' => ['p.id', 'p.name', 'p.guard_name', 'p.created_at'],
                'column_mapping' => [       // untuk search column
                    'name'        => 'p.name',
                    'roles'       => 'r.name',
                    'created_at'  => 'p.created_at',
                ],
                'meta' => [
                    'permissions' => $this->generatePermissions(),
                    'api_version' => 'v1',
                    'timestamp' => now()->toISOString(),
                ]
            ]);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function json(Request $request)
    {
        try {
            $grouped = $request->get('grouped', false);

            if ($grouped) {
                // First get all permissions and group them
                $search = htmlspecialchars((string) $request->get('search', ''), ENT_QUOTES, 'UTF-8');
                $query = Permission::query();

                if ($search !== '' && $search !== '0') {
                    $query->where('name', 'LIKE', "%{$search}%");
                }

                $allPermissions = $query->orderBy('name', 'asc')->get(['id', 'name']);

                // Group all permissions by category
                $groupedData = [];
                foreach ($allPermissions as $permission) {
                    $parts = explode(' ', (string) $permission->name);
                    $category = $parts[1] ?? 'other';

                    if (! isset($groupedData[$category])) {
                        $groupedData[$category] = [
                            'label' => ucfirst($category),
                            'options' => [],
                        ];
                    }

                    $groupedData[$category]['options'][] = [
                        'id' => $permission->id,
                        'name' => $permission->name,
                    ];
                }

                // Convert to array and apply pagination on groups
                $allGroups = array_values($groupedData);
                $page = (int) $request->get('page', 1);
                $limit = min(10, max(1, (int) $request->get('limit', 3))); // 3 groups per page

                $totalGroups = count($allGroups);
                $offset = ($page - 1) * $limit;
                $paginatedGroups = array_slice($allGroups, $offset, $limit);

                return response()->json([
                    'data' => $paginatedGroups,
                    'has_more' => ($offset + $limit) < $totalGroups,
                ]);
            }

            // Use parent trait method for normal pagination
            return $this->jsonFromTrait($request);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    private function jsonFromTrait(Request $request)
    {
        $model = Permission::class;
        $columns = property_exists($this, 'jsonColumns') ? $this->jsonColumns : ['id', 'name'];
        $searchable = property_exists($this, 'jsonSearchable') ? $this->jsonSearchable : ['id'];

        $page = (int) $request->get('page', 1);
        $limit = min(50, max(1, (int) $request->get('limit', 10)));
        $search = htmlspecialchars((string) $request->get('search', ''), ENT_QUOTES, 'UTF-8');

        $query = $model::query();

        if ($search !== '' && $search !== '0') {
            $query->where(function ($q) use ($searchable, $search): void {
                foreach ($searchable as $col) {
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
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get($columns);

        return response()->json([
            'data' => $items,
            'has_more' => ($page * $limit) < $total,
        ]);
    }

    public function store(PermissionRequest $request)
    {
        $this->authorize('create ' . $this->authorizeAction, 'web');

        try {
            $validated = $request->validated();

            // buat role
            $permission = Permission::create([
                'name' => $validated['name'],
                'guard_name' => 'web', // default guard
            ]);

            // Sync roles jika ada
            if ($request->has('roles')) {
                $roleIds = $request->roles;
                $roles = Role::whereIn('id', $roleIds)->get();
                $permission->syncRoles($roles);
            }

            // Load relasi untuk response
            $permission->load('roles');

            return $this->createdResponse($permission->load('roles'), 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        $this->authorize('show ' . $this->authorizeAction, 'web');

        try {
            $permission = Permission::with('roles')->findOrFail($id);

            return $this->successResponse(
                $permission,
                'Data berhasil diambil',
            );
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(PermissionRequest $request, $id)
    {
        $this->authorize('edit ' . $this->authorizeAction, 'web');

        try {
            $permission = Permission::findOrFail($id);

            $validated = $request->validated();

            // Update nama dan guard_name
            $permission->update([
                'name' => $validated['name'],
                'guard_name' => 'web', // default guard
            ]);

            // Sync roles jika ada
            if ($request->has('roles')) {
                $roleIds = $request->roles;
                $roles = Role::whereIn('id', $roleIds)->get();
                $permission->syncRoles($roles);
            }

            return $this->updatedResponse($permission->load('roles'), 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        $this->authorize('delete ' . $this->authorizeAction, 'web');

        return $this->singleDelete($id); // dari trait HandleErrors
    }

    public function getByIds(Request $request)
    {
        try {
            $ids = $request->get('ids', '');

            // Handle both string and array input
            $idsArray = is_array($ids) ? array_filter($ids) : array_filter(explode(',', (string) $ids));

            if ($idsArray === []) {
                return response()->json(['data' => []]);
            }

            $permissions = Permission::whereIn('id', $idsArray)
                ->select(['id', 'name'])
                ->get();

            return response()->json(['data' => $permissions]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
