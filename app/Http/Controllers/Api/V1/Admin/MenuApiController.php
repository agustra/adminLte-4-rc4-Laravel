<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use App\Traits\ApiResponse;
use App\Traits\HandleErrors;
use App\Traits\HasDynamicPermissions;
use App\Traits\HasQueryBuilder;
use App\Traits\ModernTableHelper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuApiController extends Controller
{
    use ApiResponse, HandleErrors, HasDynamicPermissions, HasQueryBuilder, ModernTableHelper;
    use AuthorizesRequests;

    protected $authorizeAction = 'menus';

    protected $tableName = 'menus';

    protected array $tableSearchable = ['m.name', 'm.url', 'm.permission', 'p.name', 'm.roles', 'm.is_active'];

    protected $rules = [
        'name' => 'required|string|max:255',
        'url' => 'nullable|string|max:255',
        'icon' => 'required|string|max:255',
        'permission' => 'nullable|string|max:255',
        'roles' => 'nullable|array',
        'roles.*' => 'integer|exists:roles,id',
        'parent_id' => 'nullable|exists:menus,id',
        'order' => 'integer|min:0',
        'is_active' => 'in:aktif,inaktif',
    ];

    private function addSidebarRefreshFlag($response, $expectedStatusCode)
    {
        if ($response->getStatusCode() === $expectedStatusCode) {
            $data = json_decode($response->getContent(), true);
            if ($data && isset($data['status']) && $data['status'] === 'success') {
                $data['refresh_sidebar'] = true;

                return response()->json($data, $expectedStatusCode);
            }
        }

        return $response;
    }

    public function index(Request $request)
    {
        try {
            $this->authorize('read ' . $this->authorizeAction, 'web');

            $result = $this->handleModernTableRequest($request, [
                'table' => $this->tableName,
                'alias' => 'm',
                'joins' => [
                    [
                        'type' => 'leftJoin',
                        'table' => 'menus as p',
                        'first' => 'p.id',
                        'second' => 'm.parent_id',
                    ],
                ],
                'select' => [
                    'm.id',
                    'm.name',
                    'm.url',
                    'm.icon',
                    'm.permission',
                    'm.roles',
                    'm.parent_id',
                    'p.name as parent_name',
                    'm.order',
                    'm.is_active',
                ],
                'searchable' => $this->tableSearchable,
                'column_mapping' => [
                    'parent_name' => 'p.name',
                    'roles' => 'm.roles',
                    'permission' => 'm.permission',
                    'name' => 'm.name',
                    'url' => 'm.url',
                    'icon' => 'm.icon',
                    'order' => 'm.order',
                    'is_active' => 'm.is_active'
                ],
                'sortable' => [
                    'id' => 'm.id',
                    'name' => 'm.name',
                    'url' => 'm.url',
                    'permission' => 'm.permission',
                    'parent_name' => 'p.name',
                    'order' => 'm.order',
                    'is_active' => 'm.is_active',
                ],
                'default_sort' => 'm.id',
                'default_dir' => 'desc',
                'filterable' => [
                    'date' => 'm.created_at',
                    'year' => 'm.created_at',
                    'month' => 'm.created_at',
                    'start_date' => function ($query, $value) {
                        $query->whereDate('m.created_at', '>=', $value);
                    },
                    'end_date' => function ($query, $value) {
                        $query->whereDate('m.created_at', '<=', $value);
                    },
                ],
                'actions' => true,
                'action_permissions' => [
                    'edit' => 'edit',
                    'delete' => 'delete',
                    'show' => 'show',
                ],
                'action_routes' => [
                    'edit' => 'menus.edit',
                    'delete' => 'api.menus.destroy',
                    'show' => 'menus.show',
                ],
                'meta' => [
                    // 'permissions' => $this->generatePermissions(),
                    'api_version' => 'v1',
                    'timestamp' => now()->toISOString(),
                ]
            ]);

            return $result;
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $this->authorize('create ' . $this->authorizeAction, 'web');

            $request->validate($this->rules);

            DB::beginTransaction();
            $validated = $request->all();

            // Convert empty string to null for parent_id
            if (isset($validated['parent_id']) && $validated['parent_id'] === '') {
                $validated['parent_id'] = null;
            }

            // Convert role IDs to role names
            if (isset($validated['roles']) && is_array($validated['roles'])) {
                $roleNames = \Spatie\Permission\Models\Role::whereIn('id', $validated['roles'])->pluck('name')->toArray();
                $validated['roles'] = $roleNames;
            }

            $menu = Menu::create($validated);
            DB::commit();

            $response = $this->createdResponse($menu, 'Menu berhasil dibuat');

            return $this->addSidebarRefreshFlag($response, 201);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $this->authorize('read ' . $this->authorizeAction, 'web');

            $menu = Menu::with(['parent'])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'results' => new MenuResource($menu),
            ]);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->authorize('edit ' . $this->authorizeAction, 'web');

            $request->validate($this->rules);

            DB::beginTransaction();
            $menu = Menu::findOrFail($id);
            $validated = $request->all();

            // Convert empty string to null for parent_id
            if (isset($validated['parent_id']) && $validated['parent_id'] === '') {
                $validated['parent_id'] = null;
            }

            // Convert role IDs to role names
            if (isset($validated['roles']) && is_array($validated['roles'])) {
                $roleNames = \Spatie\Permission\Models\Role::whereIn('id', $validated['roles'])->pluck('name')->toArray();
                $validated['roles'] = $roleNames;
            }

            $menu->update($validated);
            DB::commit();

            $response = $this->updatedResponse($menu, 'Menu berhasil diperbarui');

            return $this->addSidebarRefreshFlag($response, 200);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        $this->authorize('delete ' . $this->authorizeAction, 'web');

        try {
            $menu = Menu::findOrFail($id);
            $menuName = $menu->name;
            $menu->delete();

            $response = $this->deletedResponse(['id' => $id, 'name' => $menuName], 'Menu berhasil dihapus');

            return $this->addSidebarRefreshFlag($response, 200);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $this->authorize('delete ' . $this->authorizeAction, 'web');

            $ids = array_map('intval', array_filter((array) $request->ids, 'is_numeric'));

            if (empty($ids)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada menu yang valid untuk dihapus',
                ], 400);
            }

            $deleted = Menu::whereIn('id', $ids)->delete();

            $response = $this->deletedResponse(['deleted_count' => $deleted], 'Menu berhasil dihapus');

            return $this->addSidebarRefreshFlag($response, 200);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }



    public function getSidebarMenu()
    {
        // Allow sidebar access for authenticated users (web or api)
        $user = auth('web')->user() ?? auth('api')->user();

        try {
            $html = \App\Services\MenuBuilder::build();

            return response()->json([
                'success' => true,
                'html' => $html,
                'menu_count' => Menu::where('is_active', true)->count(),
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
