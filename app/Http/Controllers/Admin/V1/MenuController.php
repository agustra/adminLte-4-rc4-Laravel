<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Traits\HandleErrors;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MenuController extends Controller
{
    use AuthorizesRequests, HandleErrors;

    public function index()
    {
        $this->authorize('read menus');

        try {
            return view('admin.menus.index');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function create()
    {
        $this->authorize('create menus');

        try {
            $menu = new Menu;
            $parentMenus = Menu::whereNull('parent_id')->orderBy('order')->pluck('name', 'id');

            return view('admin.menus.Form', compact('menu', 'parentMenus'));
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function edit($id)
    {
        $this->authorize('edit menus');

        try {
            $menu = Menu::findOrFail($id);

            // dd($menu);

            // Mapping permission ID
            $menu->permission_id = null;
            if ($menu->permission && is_numeric($menu->permission)) {
                $permission = \Spatie\Permission\Models\Permission::find($menu->permission);
                if ($permission) {
                    $menu->permission_id = $permission->id;
                    $menu->permission = $permission->name;
                }
            } elseif ($menu->permission) {
                $permission = \Spatie\Permission\Models\Permission::where('name', $menu->permission)->first();
                if ($permission) {
                    $menu->permission_id = $permission->id;
                }
            }

            // Mapping roles ID untuk TomSelect
            $menu->role_id = '';
            if ($menu->roles && is_array($menu->roles)) {
                // Convert role names to IDs for TomSelect
                $roleIds = \Spatie\Permission\Models\Role::whereIn('name', $menu->roles)->pluck('id')->toArray();
                $menu->role_id = implode(',', $roleIds);
            }

            // Ambil daftar permission untuk select
            $permissions = \Spatie\Permission\Models\Permission::pluck('name', 'id');

            $parentMenus = Menu::whereNull('parent_id')
                ->where('id', '!=', $id)
                ->orderBy('order')
                ->pluck('name', 'id');

            return view('admin.menus.Form', compact('menu', 'parentMenus', 'permissions'));
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        $this->authorize('read menus');

        try {
            $menu = Menu::with(['parent', 'children'])->findOrFail($id);

            return view('admin.menus.Show', compact('menu'));
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
