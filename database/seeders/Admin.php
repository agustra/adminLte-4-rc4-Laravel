<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Admin extends Seeder
{
    public function run(): void
    {
        $this->createPermissions();
        $this->createRoles();
        $this->assignRolePermissions();
        $this->createUsers();
    }

    private function createPermissions(): void
    {
        $modules = [
            'dashboard',
            'users',
            'roles',
            'permissions',
            'settings',
            'menus',
            'badge',
            'media',
            'backup',
            'controller-permissions',
            'badge-configs'
        ];

        $actions = ['menu', 'read', 'create', 'edit', 'show', 'delete'];
        $permissions = ['filter date'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $permissions[] = "$action $module";
            }
        }

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission, 'guard_name' => 'web']
            );
        }
    }

    private function createRoles(): void
    {
        $roles = [
            'Super Admin',
            'Admin',
            'Manager',
            'Editor',
            'Author',
            'Moderator',
            'User',
            'Visitor',
            'Auditor',
            'Guest'
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    private function assignRolePermissions(): void
    {
        $rolePermissions = [
            'Super Admin' => Permission::all()->pluck('name')->toArray(),
            'Admin' => ['menu dashboard', 'read dashboard', 'menu users', 'read users', 'create users', 'edit users', 'show users', 'delete users'],
            'Manager' => ['menu dashboard', 'read dashboard', 'menu users', 'read users', 'show users'],
            'Editor' => ['menu dashboard', 'read dashboard', 'read users', 'edit users'],
            'Author' => ['menu dashboard', 'read dashboard', 'read users'],
            'Moderator' => ['read dashboard', 'read users'],
            'User' => ['read dashboard'],
            'Visitor' => ['read dashboard'],
            'Auditor' => ['read dashboard', 'read users', 'read roles', 'read permissions'],
            'Guest' => []
        ];

        foreach ($rolePermissions as $roleName => $permissionNames) {
            $role = Role::where('name', $roleName)->first();
            if ($role && !empty($permissionNames)) {
                if ($roleName === 'Super Admin') {
                    $role->syncPermissions(Permission::all());
                } else {
                    $permissions = Permission::whereIn('name', $permissionNames)->get();
                    $role->syncPermissions($permissions);
                }
            }
        }
    }

    private function createUsers(): void
    {
        // Create admin users
        $adminUsers = [
            ['name' => 'Super Admin', 'role' => 'Super Admin'],
            ['name' => 'Admin', 'role' => 'Admin'],
            ['name' => 'Manager', 'role' => 'Manager'],
            ['name' => 'Editor', 'role' => 'Editor'],
            ['name' => 'Author', 'role' => 'Author'],
            ['name' => 'User', 'role' => 'User'],
            ['name' => 'Visitor', 'role' => 'Visitor'],
            ['name' => 'Auditor', 'role' => 'Auditor'],
        ];

        foreach ($adminUsers as $userData) {
            $email = strtolower(str_replace(' ', '', $userData['name'])) . '@mail.com';

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $userData['name'],
                    'email_verified_at' => now(),
                    'profile_photo_path' => 'avatars/avatar-default.webp',
                    'password' => Hash::make('password'),
                ]
            );

            $user->syncRoles([$userData['role']]);
        }

        // Generate 300 visitor users
        for ($i = 1; $i <= 300; $i++) {
            $user = User::updateOrCreate(
                ['email' => "visitor{$i}@mail.com"],
                [
                    'name' => "Visitor {$i}",
                    'email_verified_at' => now(),
                    'profile_photo_path' => 'avatars/avatar-default.webp',
                    'password' => Hash::make('password'),
                ]
            );

            $user->syncRoles(['Visitor']);
        }
    }
}
