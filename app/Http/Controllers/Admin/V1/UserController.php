<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\HandleErrors;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use AuthorizesRequests, HandleErrors;

    public function index(Request $request)
    {
        $this->authorize('read users');

        try {
            return view('admin.users.index');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    // public function create()
    // {
    //     try {
    //         $user = new User();
    //         // select options role
    //         $rolesSelect = $this->SelectRole();
    //         $permissions = Permission::pluck('name', 'id');
    //         // Ambil izin per role
    //         $rolePermissions = Role::with('permissions')->get()->mapWithKeys(function ($role) {
    //             return [$role->name => $role->permissions->pluck('name')->toArray()];
    //         });
    //         return view('admin.users.Form', compact('user', 'rolesSelect', 'rolePermissions'));
    //     } catch (\Throwable $e) {
    //         return $this->handleException($e);
    //     }
    // }

    public function create()
    {
        try {
            $user = new User;

            // User baru -> tidak punya permission
            $userPermissionIds = [];

            return view('admin.users.Form', compact(
                'user',
                'userPermissionIds'
            ));
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $user = User::with('roles', 'permissions')->findOrFail($id);
            $permissions = Permission::pluck('name', 'id');

            // Gabungkan permissions langsung user + permissions via roles
            // $directPermissions = $user->permissions; // Permissions langsung dari user
            // $rolePermissions = $user->getPermissionsViaRoles(); // Permissions dari roles

            // // Gabungkan dan hilangkan duplikasi
            // $allUserPermissions = $directPermissions->merge($rolePermissions)->unique('id');

            // // Kirim ke view sebagai array ID untuk TomSelect
            // $userPermissionIds = $allUserPermissions->pluck('id')->toArray();

            // // Ambil izin per role
            // $rolePermissions = Role::with('permissions')->get()->mapWithKeys(function ($role) {
            //     return [$role->name => $role->permissions->pluck('name')->toArray()];
            // });

            $categoryPermissions = $this->categoryPermissions($permissions);

            return view('admin.users.Show', compact('user', 'categoryPermissions'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return response()->json(['status' => 'error', 'msg' => $bug]);
        }
    }

    public function edit($id)
    {
        $this->authorize('edit users');

        try {
            $user = User::with('roles')->findOrFail($id);

            // Role-based permissions only - no direct permissions
            $userPermissionIds = [];

            return view('admin.users.Form', compact('user', 'userPermissionIds'));
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    private function SelectRole()
    {
        // Mengambil semua peran dan mengubah nama peran menjadi huruf kapital untuk select options role
        $rolesSelect = Role::all()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => Str::ucfirst($name)];
        })->toArray();

        return $rolesSelect;
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
