<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Admin extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat permission
        $permissions = [
            'menu dashboard',
            'read dashboard',
            'create dashboard',
            'edit dashboard',
            'show dashboard',
            'delete dashboard',

            'menu users',
            'read users',
            'create users',
            'edit users',
            'show users',
            'delete users',

            'menu roles',
            'read roles',
            'create roles',
            'edit roles',
            'show roles',
            'delete roles',

            'menu permissions',
            'read permissions',
            'create permissions',
            'edit permissions',
            'show permissions',
            'delete permissions',

            'menu settings',
            'read settings',
            'create settings',
            'edit settings',
            'show settings',
            'delete settings',

            'menu menus',
            'read menus',
            'create menus',
            'edit menus',
            'show menus',
            'delete menus',

            'menu badge',
            'read badge',
            'create badge',
            'edit badge',
            'show badge',
            'delete badge',

            'menu media',
            'read media',
            'create media',
            'edit media',
            'show media',
            'delete media',

            'menu backup',
            'read backup',
            'create backup',
            'edit backup',
            'show backup',
            'delete backup',

            'filter date',

            'menu controller-permissions',
            'read controller-permissions',
            'create controller-permissions',
            'edit controller-permissions',
            'show controller-permissions',
            'delete controller-permissions',

            'menu badge-configs',
            'read badge-configs',
            'create badge-configs',
            'edit badge-configs',
            'show badge-configs',
            'delete badge-configs',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission, 'guard_name' => 'web']
            );
        }

        // Membuat peran
        $roles = [
            'Admin',
            'User',
            'Kasir',
            'Resepsionis',
            'Super Admin',
            'Technician',
            'Owner',
            'Manager Outlet',
            'Manager Umum',
            'Team Inti',
            'karyawan',
            // 'visitor',
            // 'author',
            // 'manager',
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Define role permissions
        $rolePermissions = [
            'user' => ['read users'],
            'kasir' => [
                'read users',
                'create users',
                'menu pinjaman',
                'read pinjaman',
                'edit pinjaman',
            ],
            'resepsionis' => [
                'read users',
                'create users',
            ],
        ];

        // Assign permissions to roles
        $allPermissions = Permission::all();

        // Admin gets all permissions
        Role::where('name', 'admin')->first()->syncPermissions($allPermissions);

        // Other roles get specific permissions
        foreach ($rolePermissions as $roleName => $permissionNames) {
            $permissions = Permission::whereIn('name', $permissionNames)->get();
            Role::where('name', $roleName)->first()->syncPermissions($permissions);
        }

        // Membuat pengguna dengan role-based permissions only
        $users = [
            'admin' => 'admin',
            'Owner Utama' => 'owner',
            'kamarut' => 'karyawan',
            'afif' => 'technician',
            'muhajir' => 'karyawan',
            'akbar' => 'karyawan',
            'Manager Umum' => 'Manager Umum',
            'Kasir Utama' => 'kasir',
            'Karyawan Teknisi' => 'karyawan',
            'kasir' => 'kasir',
            'resepsionis' => 'resepsionis',
        ];

        foreach ($users as $name => $role) {
            $email = strtolower($name) . '@mail.com';

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'email_verified_at' => now(),
                    'profile_photo_path' => null,
                    'password' => Hash::make('password'),
                ]
            );

            // Role-based permissions only - no direct user permissions
            $user->syncRoles([$role]);
        }
    }
}
