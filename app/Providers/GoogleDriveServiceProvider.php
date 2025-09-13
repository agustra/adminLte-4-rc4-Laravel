<?php

namespace App\Providers;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;

class GoogleDriveServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Storage::extend('google', function ($app, $config) {
            try {
                $client = new Client;
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);

                $service = new Drive($client);
                $adapter = new GoogleDriveAdapter($service, $config['folderId'] ?? null);

                return new Filesystem($adapter);
            } catch (\Exception $e) {
                // Return null if Google Drive setup fails
                return null;
            }
        });
    }
}
