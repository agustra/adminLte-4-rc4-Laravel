<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SyncMediaFiles extends Command
{
    protected $signature = 'media:sync';

    protected $description = 'Sync physical media files with database';

    public function handle()
    {
        $mediaPath = public_path('media');
        $user = User::first();

        if (! $user) {
            $this->error('No users found');

            return;
        }

        $this->syncDirectory($mediaPath, '', $user);
        $this->info('Media synced');
    }

    private function syncDirectory($path, $collection, $user)
    {
        if (! is_dir($path)) {
            return;
        }

        foreach (scandir($path) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $itemPath = $path.'/'.$item;

            if (is_dir($itemPath)) {
                $subCollection = $collection ? $collection.'/'.$item : $item;
                $this->syncDirectory($itemPath, $subCollection, $user);
            } else {
                $existing = Media::where('file_name', $item)->first();

                if (! $existing) {
                    $user->addMedia($itemPath)
                        ->usingName(pathinfo($item, PATHINFO_FILENAME))
                        ->usingFileName($item)
                        ->toMediaCollection($collection ?: 'default');

                    $this->info("Added: {$item}");
                }
            }
        }
    }
}
