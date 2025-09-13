<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->withPersonalTeam()->create();

        User::factory()->withPersonalTeam()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            Admin::class,
            SettingSeeder::class,
            MenuSeeder::class,
            MenuBadgeConfigSeeder::class,
            MediaSeeder::class,
            ControllerPermissionSeeder::class,

            PassportClientSeeder::class,
        ]);

        // Jalankan perintah otomatis setelah migrasi
        // Artisan::call('passport:client', ['--personal' => true]);

        // Menjalankan Passport Client secara otomatis
        // Artisan::call('passport:install --force');
        // Artisan::call('passport:client --personal');
    }
}
