<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearAllCache extends Command
{
    protected $signature = 'app:clear-all';

    protected $description = 'Clear all caches (config, route, view, optimize)';

    public function handle()
    {
        foreach (
            [
                'config:clear',
                'config:cache',
                'view:clear',
                'route:clear',
                'optimize:clear',
            ] as $cmd
        ) {
            Artisan::call($cmd);
            $this->info("Executed: $cmd");
        }

        $this->info('✅ Semua cache berhasil dibersihkan!');
    }
}
