<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateUserFolders extends Command
{
    protected $signature = 'filemanager:migrate-folders {--cleanup : Also cleanup old unused folders}';
    protected $description = 'Migrate user folders from ID-based to username-based naming';

    public function handle()
    {
        $this->info('Starting FileManager folder migration...');
        
        $users = User::all();
        $disk = Storage::disk('public');
        
        foreach ($users as $user) {
            $oldFolderName = (string) $user->id;
            $newFolderName = strtolower(str_replace(' ', '-', $user->name));
            $newFolderName = preg_replace('/[^a-z0-9\-]/', '', $newFolderName);
            
            // Skip if same name
            if ($oldFolderName === $newFolderName) {
                continue;
            }
            
            $this->info("Migrating user: {$user->name} (ID: {$user->id})");
            
            // Migrate images folder
            $oldImagesPath = "filemanager/images/{$oldFolderName}";
            $newImagesPath = "filemanager/images/{$newFolderName}";
            
            if ($disk->exists($oldImagesPath)) {
                if (!$disk->exists($newImagesPath)) {
                    $disk->move($oldImagesPath, $newImagesPath);
                    $this->line("  ✅ Moved images: {$oldImagesPath} → {$newImagesPath}");
                } else {
                    $this->warn("  ⚠️  Target images folder already exists: {$newImagesPath}");
                }
            }
            
            // Migrate files folder
            $oldFilesPath = "filemanager/files/{$oldFolderName}";
            $newFilesPath = "filemanager/files/{$newFolderName}";
            
            if ($disk->exists($oldFilesPath)) {
                if (!$disk->exists($newFilesPath)) {
                    $disk->move($oldFilesPath, $newFilesPath);
                    $this->line("  ✅ Moved files: {$oldFilesPath} → {$newFilesPath}");
                } else {
                    $this->warn("  ⚠️  Target files folder already exists: {$newFilesPath}");
                }
            }
        }
        
        // Cleanup old unused folders if requested
        if ($this->option('cleanup')) {
            $this->newLine();
            $this->info('🧹 Starting cleanup of unused folders...');
            $this->cleanupUnusedFolders($disk, $users);
        }
        
        $this->info('✅ FileManager folder migration completed!');
        $this->newLine();
        $this->info('Current folder structure:');
        $this->showCurrentStructure($disk);
        
        return 0;
    }
    
    private function cleanupUnusedFolders($disk, $users)
    {
        $validFolders = ['public']; // Always keep public folder
        
        // Get all valid user folder names
        foreach ($users as $user) {
            $folderName = strtolower(str_replace(' ', '-', $user->name));
            $folderName = preg_replace('/[^a-z0-9\-]/', '', $folderName);
            $validFolders[] = $folderName;
        }
        
        // Check images folders
        if ($disk->exists('filemanager/images')) {
            $imageFolders = $disk->directories('filemanager/images');
            foreach ($imageFolders as $folder) {
                $folderName = basename($folder);
                if (!in_array($folderName, $validFolders)) {
                    $this->warn("  🗑️  Removing unused images folder: {$folderName}");
                    $disk->deleteDirectory($folder);
                }
            }
        }
        
        // Check files folders
        if ($disk->exists('filemanager/files')) {
            $fileFolders = $disk->directories('filemanager/files');
            foreach ($fileFolders as $folder) {
                $folderName = basename($folder);
                if (!in_array($folderName, $validFolders)) {
                    $this->warn("  🗑️  Removing unused files folder: {$folderName}");
                    $disk->deleteDirectory($folder);
                }
            }
        }
        
        $this->info('✅ Cleanup completed!');
    }
    
    private function showCurrentStructure($disk)
    {
        $this->line('📁 filemanager/');
        
        if ($disk->exists('filemanager/images')) {
            $this->line('  ├── images/');
            $imageFolders = $disk->directories('filemanager/images');
            foreach ($imageFolders as $folder) {
                $folderName = basename($folder);
                $this->line("    ├── {$folderName}/");
            }
        }
        
        if ($disk->exists('filemanager/files')) {
            $this->line('  └── files/');
            $fileFolders = $disk->directories('filemanager/files');
            foreach ($fileFolders as $folder) {
                $folderName = basename($folder);
                $this->line("    ├── {$folderName}/");
            }
        }
    }
}