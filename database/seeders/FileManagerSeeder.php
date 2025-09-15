<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class FileManagerSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureDirectories();
        $this->copyEssentialFiles();
        $this->createUserFolders();
    }

    private function ensureDirectories(): void
    {
        $directories = [
            storage_path('app/public/filemanager'),
            storage_path('app/public/filemanager/images'),
            storage_path('app/public/filemanager/images/public'),
            storage_path('app/public/filemanager/documents'),
            storage_path('app/public/filemanager/documents/public'),
        ];

        foreach ($directories as $dir) {
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
                $this->command->info("✅ Created directory: {$dir}");
            }
        }
    }

    private function copyEssentialFiles(): void
    {
        $files = [
            // Default avatar
            [
                'source' => public_path('img/avatars/avatar-default.webp'),
                'target' => storage_path('app/public/filemanager/images/public/avatar-default.webp')
            ],
            // App logo
            [
                'source' => public_path('img/AdminLTELogo.png'),
                'target' => storage_path('app/public/filemanager/images/public/AdminLTELogo.png')
            ],
        ];

        foreach ($files as $file) {
            if (File::exists($file['source']) && !File::exists($file['target'])) {
                File::copy($file['source'], $file['target']);
                $this->command->info("✅ Copied: " . basename($file['source']));
            } elseif (!File::exists($file['source'])) {
                $this->command->warn("⚠️ Source not found: " . basename($file['source']));
            } else {
                $this->command->info("ℹ️ Already exists: " . basename($file['target']));
            }
        }
    }

    private function createUserFolders(): void
    {
        $this->command->info('👥 Creating user folders...');
        
        $users = \App\Models\User::all();
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        
        foreach ($users as $user) {
            $folderName = $this->getUserFolderName($user);
            
            $userFolders = [
                "filemanager/images/{$folderName}",
                "filemanager/files/{$folderName}"
            ];
            
            foreach ($userFolders as $folder) {
                $fullPath = storage_path("app/public/{$folder}");
                if (!File::exists($fullPath)) {
                    File::makeDirectory($fullPath, 0755, true);
                    $this->command->info("✅ Created: {$folder} (User: {$user->name})");
                }
            }
        }
    }
    
    private function getUserFolderName($user): string
    {
        $folderName = strtolower(str_replace(' ', '-', $user->name));
        return preg_replace('/[^a-z0-9\-]/', '', $folderName);
    }
}