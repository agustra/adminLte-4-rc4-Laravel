<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeAll extends Command
{
    protected $signature = 'make:all {name}';

    protected $description = 'Generate model, migration, seeder, requests, resources, and controllers (Admin & API)';

    public function handle()
    {
        $name = $this->argument('name');

        // Model + Migration
        $this->call('make:model', [
            'name' => $name,
            '--migration' => true,
        ]);

        // Seeder
        $this->call('make:seeder', [
            'name' => "{$name}Seeder",
        ]);

        // Form Request
        $this->call('make:request', [
            'name' => "{$name}Request",
        ]);

        // API Resource
        $this->call('make:resource', [
            'name' => "{$name}Resource",
        ]);

        // Controller untuk Admin Panel (resource style)
        $this->call('make:controller', [
            'name' => "V1/{$name}Controller",
            '--resource' => true,
        ]);

        // Controller untuk API (API style)
        $this->call('make:controller', [
            'name' => "Api/V1/{$name}ApiController",
            '--api' => true,
        ]);

        $this->info("✅ Semua file terkait {$name} berhasil dibuat!");
    }
}

// cara pakainya:
// php artisan make:all Karyawan

// Hasil Akhir (php artisan make:all Karyawan)

// app/Models/Karyawan.php

// database/migrations/xxxx_xx_xx_create_karyawans_table.php

// database/seeders/KaryawanSeeder.php

// app/Http/Requests/KaryawanRequest.php

// app/Http/Resources/KaryawanResource.php

// app/Http/Controllers/Admin/V1/KaryawanController.php

// app/Http/Controllers/Api/V1/KaryawanController.php

// Hanya API controller:
// php artisan make:all Karyawan --api-only

// Hanya Admin controller:
// php artisan make:all Karyawan --admin-only
