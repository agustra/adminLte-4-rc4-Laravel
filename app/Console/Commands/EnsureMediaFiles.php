<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class EnsureMediaFiles extends Command
{
    protected $signature = 'media:ensure';

    protected $description = 'Ensure default media files exist';

    public function handle()
    {
        $this->ensureMediaStructure();
        $this->copyDefaultFiles();

        $this->info('Media files ensured successfully!');

        return self::SUCCESS;
    }

    protected function ensureMediaStructure()
    {
        $directories = [
            public_path('media/settings'),
            public_path('media/avatars'),
        ];

        foreach ($directories as $dir) {
            if (! File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
                $this->info("Created directory: {$dir}");
            } else {
                $this->line("Directory exists: {$dir}");
            }
        }
    }

    protected function copyDefaultFiles()
    {
        $files = [
            'settings/AdminLTELogo.png',
            'avatars/avatar-default.webp',
        ];

        foreach ($files as $file) {
            $source = storage_path('app/media-defaults/' . $file);
            $target = public_path('media/' . $file);

            if (! File::exists($target)) {
                if (File::exists($source)) {
                    File::ensureDirectoryExists(dirname($target));
                    File::copy($source, $target);
                    $this->info("Copied: {$file}");
                } else {
                    $this->error("Source file not found: {$source}");
                }
            } else {
                $this->line("File already exists: {$file}");
            }
        }
    }
}
