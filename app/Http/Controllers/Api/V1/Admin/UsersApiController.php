<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use App\Traits\HandleErrors;
use App\Traits\HasApiJson;
use App\Traits\HasDynamicPermissions;
use App\Traits\HasQueryBuilder;
use App\Traits\ModernTableHelper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersApiController extends Controller
{
    use ApiResponse, HandleErrors, HasApiJson, HasDynamicPermissions, HasQueryBuilder, ModernTableHelper;
    use AuthorizesRequests;

    protected $thisModel = User::class;

    protected $authorizeAction = 'users';

    protected $tableName = 'users';

    // Untuk HasApiJson trait
    protected array $jsonColumns = ['id', 'name'];

    protected array $jsonSearchable = ['name'];

    protected array $tableSearchable = [
        'u.name',
        'u.email',
        'r.name',
        'rp.name',
        'up.name',
        'u.created_at',
    ];

    protected $allowedSortColumns = [
        'id' => 'id',
        'name' => 'name',
        'email' => 'email',
        'roles_count' => 'roles_count',
        'permissions_count' => 'permissions_count',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at',
    ];

    protected function getCrudModel()
    {
        return $this->thisModel;
    }

    protected function getResourceClass(): string
    {
        return UserResource::class;
    }

    protected function buildBaseQuery($model)
    {
        return $model::with(['roles', 'permissions'])->withCount(['roles', 'permissions']);
    }

    protected function transformData($data)
    {
        if (isset($data['password']) && ! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (isset($data['avatar']) && ! empty($data['avatar'])) {
            $data['profile_photo_path'] = $this->extractRelativePath($data['avatar']);
        } elseif (isset($data['avatar']) && empty($data['avatar'])) {
            $data['profile_photo_path'] = null;
        }

        unset($data['avatar'], $data['old_password']);

        return $data;
    }

    private function extractRelativePath($avatarUrl): ?string
    {
        if (empty($avatarUrl)) {
            return null;
        }
        
        // If it's a full URL, extract the path part
        if (filter_var($avatarUrl, FILTER_VALIDATE_URL)) {
            $path = parse_url($avatarUrl, PHP_URL_PATH);
            $path = ltrim($path, '/');
        } else {
            $path = ltrim($avatarUrl, '/');
        }
        
        // Remove 'storage/' prefix if present (since we'll add it in view)
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }
        
        // Remove 'media/' prefix if present (legacy)
        if (str_starts_with($path, 'media/')) {
            $path = substr($path, 6);
        }
        
        // Ensure path starts with filemanager/ for user avatars
        if (!str_starts_with($path, 'filemanager/') && !str_starts_with($path, 'avatars/')) {
            // If it's just a filename, assume it's in user's private folder
            if (!str_contains($path, '/')) {
                $userId = auth()->id();
                $userFolderName = $this->getUserFolderName(auth()->user());
                $path = "filemanager/images/{$userFolderName}/{$path}";
            }
        }
        
        return $path;
    }
    
    private function getUserFolderName($user): string
    {
        $folderName = strtolower(str_replace(' ', '-', $user->name));
        return preg_replace('/[^a-z0-9\-]/', '', $folderName);
    }

    public function index(Request $request)
    {
        $this->authorize('read ' . $this->authorizeAction, 'web');

        return $this->handleModernTableRequest($request, [
            'table' => 'users',
            'alias' => 'u',
            'joins' => [
                [
                    'type' => 'leftJoin',
                    'table' => 'model_has_roles as mhr',
                    'first' => 'u.id',
                    'second' => 'mhr.model_id',
                ],
                [
                    'type' => 'leftJoin',
                    'table' => 'roles as r',
                    'first' => 'mhr.role_id',
                    'second' => 'r.id',
                ],
                [
                    'type' => 'leftJoin',
                    'table' => 'role_has_permissions as rhp',
                    'first' => 'r.id',
                    'second' => 'rhp.role_id',
                ],
                [
                    'type' => 'leftJoin',
                    'table' => 'permissions as rp',
                    'first' => 'rhp.permission_id',
                    'second' => 'rp.id',
                ],
                [
                    'type' => 'leftJoin',
                    'table' => 'model_has_permissions as mhp',
                    'first' => 'u.id',
                    'second' => 'mhp.model_id',
                ],
                [
                    'type' => 'leftJoin',
                    'table' => 'permissions as up',
                    'first' => 'mhp.permission_id',
                    'second' => 'up.id',
                ],
            ],
            'select' => [
                'u.id',
                'u.name',
                'u.email',
                'u.profile_photo_path',
                'u.created_at',
                DB::raw('GROUP_CONCAT(DISTINCT r.name) as roles'),
                DB::raw('GROUP_CONCAT(DISTINCT COALESCE(rp.name, up.name)) as permissions'),
                DB::raw('COUNT(DISTINCT r.id) as roles_count'),
                DB::raw('COUNT(DISTINCT COALESCE(rp.id, up.id)) as permissions_count')
            ],
            'group_by' => ['u.id', 'u.name', 'u.email', 'u.profile_photo_path', 'u.created_at'],
            'searchable' => $this->tableSearchable,
            'filterable' => [
                'date' => 'u.created_at',
                'start_date' => function ($query, $value) {
                    $query->whereDate('u.created_at', '>=', $value);
                },
                'end_date' => function ($query, $value) {
                    $query->whereDate('u.created_at', '<=', $value);
                },
                'year' => 'u.created_at',
                'month' => 'u.created_at',
            ],
            'sortable' => [
                'id' => 'u.id',
                'name' => 'u.name',
                'email' => 'u.email',
                'roles' => 'r.name',
                'permissions' => 'rp.name',
                'created_at' => 'u.created_at',
            ],

            'default_sort' => 'u.id',
            'default_dir' => 'desc',
            'actions' => true,
            'action_permissions' => [
                'edit' => 'edit',
                'delete' => 'delete',
                'show' => 'show',
            ],
            'action_routes' => [
                'edit' => 'users.edit',
                'delete' => 'api.users.destroy',
                'show' => 'users.show',
            ],
            'column_mapping' => [
                'id' => 'u.id',
                'name' => 'u.name',
                'email' => 'u.email',
                'roles' => 'r.name',
                'permissions' => ['rp.name', 'up.name'],
                'created_at' => 'u.created_at'
            ],
            'transform' => function ($user) {
                $roles = $user->roles ? explode(',', $user->roles) : [];
                $permissions = $user->permissions ? array_unique(explode(',', $user->permissions)) : [];

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar_url' => $user->profile_photo_path
                        ? (str_starts_with($user->profile_photo_path, 'http')
                            ? $user->profile_photo_path
                            : (str_starts_with($user->profile_photo_path, '/storage/')
                                ? url($user->profile_photo_path)
                                : asset('storage/' . $user->profile_photo_path)))
                        : asset('storage/filemanager/images/public/avatar-default.webp'),
                    'roles' => array_map(fn($role) => ['name' => trim($role)], $roles),
                    'permissions' => array_map(fn($perm) => ['name' => trim($perm)], array_filter($permissions)),
                    'permissions_count' => (int) $user->permissions_count,
                    'roles_count' => (int) $user->roles_count,
                    'created_at' => date('Y-m-d', strtotime($user->created_at)),
                    // 'action' => $this->generatePermissions(),
                ];
            },
            'meta' => [
                'permissions' => $this->generatePermissions(),
                'api_version' => 'v1',
                'timestamp' => now()->toISOString(),
            ]
        ]);
    }

    public function store(UserRequest $request)
    {
        $this->authorize('create ' . $this->authorizeAction, 'web');

        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $data = $this->transformData($validated);

            $user = User::create($data);

            // Role-based permissions only
            if (! empty($validated['roles'])) {
                $roleNames = Role::whereIn('id', $validated['roles'])
                    ->where('guard_name', 'web')
                    ->pluck('name')
                    ->toArray();
                $user->syncRoles($roleNames);
            }

            DB::commit();

            return $this->createdResponse(
                new UserResource($user->fresh(['roles', 'permissions'])),
                'Data berhasil ditambahkan'
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        $this->authorize('show ' . $this->authorizeAction, 'web');

        try {
            $user = $this->getCrudModel()::with(['roles', 'permissions'])->findOrFail($id);

            return $this->successResponse(
                new UserResource($user),
                'Data berhasil diambil',
            );
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UserRequest $request, $id)
    {
        $this->authorize('edit ' . $this->authorizeAction, 'web');

        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);

            // Validasi password lama jika ingin update password
            if (! empty($request->password) && ! Hash::check($request->input('old_password'), $user->password)) {
                return $this->errorResponse('Kata sandi lama salah!', 422);
            }

            $validated = $request->validated();
            $data = $this->transformData($validated);

            $user->update($data);

            // Role-based permissions only
            if (! empty($validated['roles'])) {
                $roleNames = Role::whereIn('id', $validated['roles'])
                    ->where('guard_name', 'web')
                    ->pluck('name')
                    ->toArray();
                $user->syncRoles($roleNames);
            } else {
                $user->syncRoles([]);
            }

            DB::commit();

            return $this->updatedResponse(
                new UserResource($user->fresh(['roles', 'permissions'])),
                'Data berhasil diperbarui'
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        $this->authorize('delete ' . $this->authorizeAction, 'web');

        return $this->singleDelete($id);
    }

    /**
     * Get user permissions with server-side pagination
     */
    public function getPermissionsPaginated(Request $request, $id)
    {
        try {
            $this->authorize('show ' . $this->authorizeAction, 'web');

            $user = User::with(['permissions', 'roles.permissions'])->findOrFail($id);

            // Get all permissions (direct + through roles)
            $allPermissions = collect();
            $allPermissions = $allPermissions->merge($user->permissions);

            foreach ($user->roles as $role) {
                $allPermissions = $allPermissions->merge($role->permissions);
            }

            $allPermissions = $allPermissions->unique('id')->values();

            // Group by module
            $groupedPermissions = $allPermissions->groupBy(function ($permission): string {
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
                'total' => $allPermissions->count(),
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
