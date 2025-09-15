<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing menus
        Menu::truncate();

        // Essential menus (hardcoded structure)
        $dashboard = Menu::create([
            'name' => 'Dashboard',
            'url' => '/dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'permission' => 'menu dashboard',
            'order' => 1,
            'is_active' => 'aktif',
        ]);



        $managementSystem = Menu::create([
            'name' => 'Management System',
            'url' => '#',
            'icon' => 'fas fa-cogs',
            'permission' => null,
            'roles' => ['Super Admin', 'Admin', 'Manager'], // Hanya role ini yang bisa akses
            'order' => 2,
            'is_active' => 'aktif',
        ]);

        // Management System submenus - Logical Flow Order
        Menu::create([
            'name' => 'Users',
            'url' => '/admin/users',
            'icon' => 'fas fa-users',
            'permission' => 'menu users',
            'parent_id' => $managementSystem->id,
            'order' => 1,
            'is_active' => 'aktif',
        ]);

        Menu::create([
            'name' => 'Roles',
            'url' => '/admin/roles',
            'icon' => 'fas fa-user-shield',
            'permission' => 'menu roles',
            'parent_id' => $managementSystem->id,
            'order' => 2,
            'is_active' => 'aktif',
        ]);

        Menu::create([
            'name' => 'Permissions',
            'url' => '/admin/permissions',
            'icon' => 'fas fa-key',
            'permission' => 'menu permissions',
            'parent_id' => $managementSystem->id,
            'order' => 3,
            'is_active' => 'aktif',
        ]);

        Menu::create([
            'name' => 'Dynamic Permissions',
            'url' => '/admin/controller-permissions',
            'icon' => 'fas fa-lock',
            'permission' => 'menu controller-permissions',
            'parent_id' => $managementSystem->id,
            'order' => 4,
            'is_active' => 'aktif',
        ]);

        Menu::create([
            'name' => 'File Manager',
            'url' => '/admin/file-manager',
            'icon' => 'fas fa-folder-open',
            'permission' => 'menu filemanager',
            'parent_id' => $managementSystem->id,
            'order' => 5,
            'is_active' => 'aktif',
        ]);

        Menu::create([
            'name' => 'Menus',
            'url' => '/admin/menus',
            'icon' => 'fas fa-bars',
            'permission' => 'menu menus',
            'parent_id' => $managementSystem->id,
            'order' => 6,
            'is_active' => 'aktif',
        ]);

        Menu::create([
            'name' => 'Badge Config',
            'url' => '/admin/badge-configs',
            'icon' => 'fas fa-tags',
            'permission' => 'menu badge',
            'parent_id' => $managementSystem->id,
            'order' => 7,
            'is_active' => 'aktif',
        ]);

        Menu::create([
            'name' => 'Settings',
            'url' => '/admin/settings',
            'icon' => 'fas fa-cog',
            'permission' => 'menu settings',
            'parent_id' => $managementSystem->id,
            'order' => 8,
            'is_active' => 'aktif',
        ]);

        Menu::create([
            'name' => 'Backup',
            'url' => '/admin/backup',
            'icon' => 'fas fa-database',
            'permission' => 'menu backup',
            'parent_id' => $managementSystem->id,
            'order' => 9,
            'is_active' => 'aktif',
        ]);
    }
}
