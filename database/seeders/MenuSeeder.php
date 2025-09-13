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

        Menu::create([
            'name' => 'Media Library',
            'url' => '/media-library',
            'icon' => 'fa-solid fa-photo-film',
            'permission' => 'menu media',
            'order' => 2,
            'is_active' => 'aktif',
        ]);

        $managementSystem = Menu::create([
            'name' => 'Management System',
            'url' => '#',
            'icon' => 'fas fa-cogs',
            'permission' => null,
            'order' => 4,
            'is_active' => 'aktif',
        ]);

        // Management System submenus
        Menu::create([
            'name' => 'Users',
            'url' => '/admin/users',
            'icon' => 'far fa-circle',
            'permission' => 'menu users',
            'parent_id' => $managementSystem->id,
            'order' => 1,
            'is_active' => 'aktif',
        ]);

        Menu::create([
            'name' => 'Roles',
            'url' => '/admin/roles',
            'icon' => 'far fa-circle',
            'permission' => 'menu roles',
            'parent_id' => $managementSystem->id,
            'order' => 2,
            'is_active' => 'aktif',
        ]);

        Menu::create([
            'name' => 'Permissions',
            'url' => '/admin/permissions',
            'icon' => 'far fa-circle',
            'permission' => 'menu permissions',
            'parent_id' => $managementSystem->id,
            'order' => 3,
            'is_active' => 'aktif',
        ]);

        Menu::create([
            'name' => 'Menus',
            'url' => '/admin/menus',
            'icon' => 'far fa-circle',
            'permission' => 'menu menus',
            'parent_id' => $managementSystem->id,
            'order' => 4,
            'is_active' => 'aktif',
        ]);
        Menu::create([
            'name' => 'Badge Config',
            'url' => '/admin/badge-configs',
            'icon' => 'far fa-circle',
            'permission' => 'menu badge',
            'parent_id' => $managementSystem->id,
            'order' => 5,
            'is_active' => 'aktif',
        ]);

        Menu::create([
            'name' => 'Settings',
            'url' => '/admin/settings',
            'icon' => 'far fa-circle',
            'permission' => 'menu settings',
            'parent_id' => $managementSystem->id,
            'order' => 6,
            'is_active' => 'aktif',
        ]);

        Menu::create([
            'name' => 'Backup',
            'url' => '/admin/backup',
            'icon' => 'far fa-circle',
            'permission' => 'menu backup',
            'parent_id' => $managementSystem->id,
            'order' => 7,
            'is_active' => 'aktif',
        ]);

        Menu::create([
            'name' => 'Dynamic Permissions',
            'url' => '/admin/controller-permissions',
            'icon' => 'far fa-circle',
            'permission' => 'menu permissions',
            'parent_id' => $managementSystem->id,
            'order' => 8,
            'is_active' => 'aktif',
        ]);
    }
}
