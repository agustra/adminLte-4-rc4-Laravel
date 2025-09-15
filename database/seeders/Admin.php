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
        $this->ensureDefaultAvatar();
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

            'backup',
            'controller-permissions',
            'badge-configs',
            'filemanager'
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
            'Admin' => [
                'menu dashboard',
                'read dashboard',
                'menu users',
                'read users',
                'create users',
                'edit users',
                'show users',
                'delete users',
                'menu filemanager',
                'read filemanager',
                'create filemanager',
                'edit filemanager',
                'delete filemanager'
            ],
            'Manager' => [
                'menu dashboard',
                'read dashboard',
                'menu users',
                'read users',
                'show users',
                'read filemanager',
                'create filemanager',
                'edit filemanager',
                'delete filemanager'
            ],
            'Editor' => [
                'menu dashboard',
                'read dashboard',
                'read users',
                'edit users',
                'read filemanager',
                'create filemanager',
                'edit filemanager',
                'delete filemanager'
            ],
            'Author' => [
                'menu dashboard',
                'read dashboard',
                'read users',
                'read filemanager',
                'create filemanager',
                'edit filemanager',
                'delete filemanager'
            ],
            'Moderator' => [
                'menu dashboard',
                'read dashboard',
                'read users',
                'read filemanager',
                'create filemanager',
                'edit filemanager',
                'delete filemanager'
            ],
            'User' => [
                'menu dashboard',
                'read dashboard',
                'read filemanager',
                'create filemanager',
                'edit filemanager',
                'delete filemanager'
            ],
            'Visitor' => ['read dashboard'],
            'Auditor' => [
                'menu dashboard',
                'read dashboard',
                'read users',
                'read roles',
                'read permissions',
                'read filemanager',
                'create filemanager',
                'edit filemanager',
                'delete filemanager'
            ],
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
                    'profile_photo_path' => 'filemanager/images/public/avatar-default.webp',
                    'password' => Hash::make('password'),
                ]
            );

            $user->syncRoles([$userData['role']]);
        }

        // Generate 300 visitor users
        // for ($i = 1; $i <= 300; $i++) {
        //     $user = User::updateOrCreate(
        //         ['email' => "visitor{$i}@mail.com"],
        //         [
        //             'name' => "Visitor {$i}",
        //             'email_verified_at' => now(),
        //             'profile_photo_path' => 'avatars/avatar-default.webp',
        //             'password' => Hash::make('password'),
        //         ]
        //     );

        //     $user->syncRoles(['Visitor']);
        // }
    }

    private function ensureDefaultAvatar(): void
    {
        $sourcePath = public_path('img/avatars/avatar-default.webp');
        $targetDir = storage_path('app/public/filemanager/images/public');
        $targetPath = $targetDir . '/avatar-default.webp';

        // Create directory if not exists
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Copy default avatar if source exists and target doesn't exist
        if (file_exists($sourcePath) && !file_exists($targetPath)) {
            copy($sourcePath, $targetPath);
            $this->command->info('✅ Default avatar copied to FileManager public folder');
        } elseif (file_exists($targetPath)) {
            $this->command->info('ℹ️ Default avatar already exists in FileManager');
        } else {
            $this->command->warn('⚠️ Source avatar not found: ' . $sourcePath);
        }
    }
}
