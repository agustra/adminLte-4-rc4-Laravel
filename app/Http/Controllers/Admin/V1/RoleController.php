<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Controller;
use App\Traits\HandleErrors;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use AuthorizesRequests, HandleErrors;

    protected $authorizeAction = 'roles';

    protected $model = Role::class;

    public function index()
    {
        $this->authorize('read roles');

        try {
            return view('admin.roles.index');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function create(Role $role)
    {
        try {
            $this->authorize('create '.$this->authorizeAction, 'web');
            $roles = new $this->model;

            return view('admin.'.$this->authorizeAction.'.Form', compact('role'));
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $this->authorize('create '.$this->authorizeAction, 'web');
            $role = $this->model::with(['permissions'])->findOrFail($id);
            $permissions = Permission::pluck('name', 'id');
            $categoryPermissions = $this->categoryPermissions($permissions);

            return view('admin.'.$this->authorizeAction.'.Show', compact('permissions', 'role', 'categoryPermissions'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return response()->json(['status' => 'error', 'msg' => $bug]);
        }
    }

    public function edit($id)
    {
        try {
            $this->authorize('edit '.$this->authorizeAction, 'web');
            $role = $this->model::findOrFail($id);

            return view('admin.'.$this->authorizeAction.'.Form', compact('role'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return response()->json(['status' => 'error', 'msg' => $bug]);
        }
    }

    // Categorize Permissions
    private function categoryPermissions($permissions)
    {
        $categories = $permissions->map(function ($permission) {
            $parts = explode(' ', $permission);

            return isset($parts[1]) ? $parts[1] : 'other'; // Fallback ke 'other' jika tidak ada kata kedua
        })->unique()->values()->toArray();

        $categorizedPermissions = [];
        foreach ($categories as $category) {
            $categorizedPermissions[$category] = $permissions->filter(function ($permission) use ($category) {
                return str_contains($permission, $category);
            })->toArray();
        }

        return $categorizedPermissions;
    }
}
