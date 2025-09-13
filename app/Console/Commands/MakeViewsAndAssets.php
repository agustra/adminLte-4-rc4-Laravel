<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeViewsAndAssets extends Command
{
    protected $signature = 'make:views {name}';

    protected $description = 'Generate view files and JS asset folder for a module';

    public function handle()
    {
        $name = strtolower($this->argument('name'));

        // 📂 Folder views
        $viewsPath = resource_path("views/{$name}");
        File::ensureDirectoryExists($viewsPath);

        // File view
        $views = ['Form.blade.php', 'Index.blade.php', 'Show.blade.php'];
        foreach ($views as $view) {
            $file = $viewsPath.'/'.$view;
            if (! File::exists($file)) {
                File::put($file, "<!-- {$view} for {$name} -->");
                $this->info("✅ Created: resources/views/{$name}/{$view}");
            } else {
                $this->warn("⚠️ Already exists: resources/views/{$name}/{$view}");
            }
        }

        // 📂 Folder JS
        $jsPath = public_path("js/{$name}");
        File::ensureDirectoryExists($jsPath);

        // File JS
        $jsFile = $jsPath."/{$name}.js";
        if (! File::exists($jsFile)) {
            File::put($jsFile, "// {$name}.js");
            $this->info("✅ Created: public/js/{$name}/{$name}.js");
        } else {
            $this->warn("⚠️ Already exists: public/js/{$name}/{$name}.js");
        }

        $this->info("🎉 Semua view & asset untuk {$name} berhasil dibuat!");
    }
}

// cara pakainya:
// php artisan make:views Karyawan   ganti karyawan dengan nama module yang diinginkan
// Hasil Akhir (php artisan make:views Karyawan)

// resources/views/Karyawan/Form.blade.php
// resources/views/Karyawan/Index.blade.php
// resources/views/Karyawan/Show.blade.php
// public/js/Karyawan/Karyawan.js

//
