<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupFileManagerFolders extends Command
{
    protected $signature = 'filemanager:cleanup {--dry-run : Show what would be deleted without actually deleting}';
    protected $description = 'Cleanup unused FileManager folders (orphaned folders from deleted users)';

    public function handle()
    {
        $this->info('🧹 FileManager Cleanup Tool');
        $this->newLine();
        
        $disk = Storage::disk('public');
        $users = User::all();
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No files will be deleted');
            $this->newLine();
        }
        
        // Get valid folder names
        $validFolders = ['public']; // Always keep public folder
        foreach ($users as $user) {
            $folderName = strtolower(str_replace(' ', '-', $user->name));
            $folderName = preg_replace('/[^a-z0-9\-]/', '', $folderName);
            $validFolders[] = $folderName;
        }
        
        $this->info('Valid folders: ' . implode(', ', $validFolders));
        $this->newLine();
        
        $deletedCount = 0;
        
        // Check images folders
        if ($disk->exists('filemanager/images')) {
            $imageFolders = $disk->directories('filemanager/images');
            foreach ($imageFolders as $folder) {
                $folderName = basename($folder);
                if (!in_array($folderName, $validFolders)) {
                    $fileCount = count($disk->allFiles($folder));
                    
                    if ($dryRun) {
                        $this->line("  🗂️  Would delete images/{$folderName}/ ({$fileCount} files)");
                    } else {
                        $this->warn("  🗑️  Deleting images/{$folderName}/ ({$fileCount} files)");
                        $disk->deleteDirectory($folder);
                        $deletedCount++;
                    }
                }
            }
        }
        
        // Check files folders
        if ($disk->exists('filemanager/files')) {
            $fileFolders = $disk->directories('filemanager/files');
            foreach ($fileFolders as $folder) {
                $folderName = basename($folder);
                if (!in_array($folderName, $validFolders)) {
                    $fileCount = count($disk->allFiles($folder));
                    
                    if ($dryRun) {
                        $this->line("  🗂️  Would delete files/{$folderName}/ ({$fileCount} files)");
                    } else {
                        $this->warn("  🗑️  Deleting files/{$folderName}/ ({$fileCount} files)");
                        $disk->deleteDirectory($folder);
                        $deletedCount++;
                    }
                }
            }
        }
        
        $this->newLine();
        
        if ($dryRun) {
            $this->info('✅ Dry run completed! Use without --dry-run to actually delete folders.');
        } else {
            if ($deletedCount > 0) {
                $this->info("✅ Cleanup completed! Deleted {$deletedCount} unused folders.");
            } else {
                $this->info('✅ No unused folders found. FileManager is clean!');
            }
        }
        
        // Show current structure
        $this->newLine();
        $this->info('Current folder structure:');
        $this->showCurrentStructure($disk);
        
        return 0;
    }
    
    private function showCurrentStructure($disk)
    {
        $this->line('📁 filemanager/');
        
        if ($disk->exists('filemanager/images')) {
            $this->line('  ├── images/');
            $imageFolders = $disk->directories('filemanager/images');
            foreach ($imageFolders as $folder) {
                $folderName = basename($folder);
                $fileCount = count($disk->allFiles($folder));
                $this->line("    ├── {$folderName}/ ({$fileCount} files)");
            }
        }
        
        if ($disk->exists('filemanager/files')) {
            $this->line('  └── files/');
            $fileFolders = $disk->directories('filemanager/files');
            foreach ($fileFolders as $folder) {
                $folderName = basename($folder);
                $fileCount = count($disk->allFiles($folder));
                $this->line("    ├── {$folderName}/ ({$fileCount} files)");
            }
        }
    }
}