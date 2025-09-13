<?php

namespace Database\Seeders;

use App\Jobs\MediaSyncJob;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        // Jalankan command melalui Artisan facade
        // Artisan::call('media:ensure');

        // Get admin user
        // $admin = \App\Models\User::where('email', 'admin@mail.com')->first();

        // if (! $admin) {
        //     $this->command->warn('Admin user not found. Skipping media attachment.');

        //     return;
        // }

        $user = \App\Models\User::first();

        if (! $user) {
            $this->command->warn('Tidak ada user yang ditemukan. Skip attach media.');
            return;
        }

        $files = [
            ['path' => 'settings/AdminLTELogo.png', 'name' => 'AdminLTE Logo', 'collection' => 'settings'],
            ['path' => 'avatars/avatar-default.webp', 'name' => 'Default Avatar', 'collection' => 'avatars'],
        ];

        foreach ($files as $file) {
            $target = public_path('media/' . $file['path']);

            if (File::exists($target)) {
                // Clear existing media in this collection
                $user->clearMediaCollection($file['collection']);

                // Add new media
                $user->addMedia($target)
                    ->usingName($file['name'])
                    ->toMediaCollection($file['collection']);

                $this->command->info("Attached: {$file['path']} to user");
            }
        }

        // Jalankan command melalui Artisan facade
        Artisan::call('media:ensure');
        // sleep(2); // jeda 2 detik
        // Artisan::call('media:sync');

        // Cleanup unwanted files like .DS_Store
        // sleep(5); // jeda 2 detik
        // $this->cleanupUnwantedFiles();

        // Queue media sync job
        MediaSyncJob::dispatch()->delay(now()->addSeconds(2));

        // php artisan media:ensure 
        // php artisan media:sync 
    }

    // private function cleanupUnwantedFiles()
    // {
    //     // Hapus file .DS_Store dari database media
    //     \Spatie\MediaLibrary\MediaCollections\Models\Media::where('file_name', '.DS_Store')->delete();

    //     // Hapus file .DS_Store fisik dari folder media
    //     $dsStoreFiles = [
    //         public_path('media/.DS_Store'),
    //         public_path('media/settings/.DS_Store'),
    //         public_path('media/avatars/.DS_Store'),
    //         public_path('media/default/.DS_Store'),
    //     ];

    //     foreach ($dsStoreFiles as $file) {
    //         if (File::exists($file)) {
    //             File::delete($file);
    //             $this->command->info("Deleted: {$file}");
    //         }
    //     }
    // }
}
