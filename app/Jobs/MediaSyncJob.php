<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class MediaSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes

    public $tries = 3;

    public function handle(): void
    {
        try {
            Log::info('Starting media sync job');
            Artisan::call('media:Sync');
            Log::info('Media sync job completed successfully');
        } catch (\Exception $e) {
            Log::error('Media sync job failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Media sync job failed permanently', [
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);
    }
}
