<?php

namespace Database\Seeders;

use App\Models\MenuBadgeConfig;
use Illuminate\Database\Seeder;

class MenuBadgeConfigSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            [
                'menu_url' => '/admin/users',
                'model_class' => 'App\Models\User',
                'date_field' => 'created_at',
                'is_active' => true,
                'description' => 'Badge untuk menu Users - menampilkan jumlah user baru hari ini',
            ],
            [
                'menu_url' => '/admin/roles',
                'model_class' => 'Spatie\Permission\Models\Role',
                'date_field' => 'created_at',
                'is_active' => true,
                'description' => 'Badge untuk menu Roles - menampilkan jumlah role baru hari ini',
            ],
            [
                'menu_url' => '/admin/permissions',
                'model_class' => 'Spatie\Permission\Models\Permission',
                'date_field' => 'created_at',
                'is_active' => true,
                'description' => 'Badge untuk menu Permissions - menampilkan jumlah permission baru hari ini',
            ],
            [
                'menu_url' => '/admin/menus',
                'model_class' => 'App\Models\Menu',
                'date_field' => 'created_at',
                'is_active' => true,
                'description' => 'Badge untuk menu Menus - menampilkan jumlah menu baru hari ini',
            ],
        ];

        foreach ($configs as $config) {
            MenuBadgeConfig::updateOrCreate(
                ['menu_url' => $config['menu_url']],
                $config
            );
        }
    }
}
