<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class PassportClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Personal Access Client
        $clientName = 'Laravel Personal Access Client'; // Nama default
        Artisan::call('passport:client', [
            '--personal' => true,
            '--name' => $clientName,
            '--no-interaction' => true,
        ]);

        // Dapatkan ID dan secret dari tabel oauth_clients
        $client = DB::table('oauth_clients')
            ->where('name', $clientName)
            ->orderBy('created_at', 'desc')
            ->first();

        // Tampilkan ID dan secret client
        if ($client) {
            echo "Client ID: {$client->id}\n";
            echo "Client Secret: {$client->secret}\n";
        } else {
            echo "Client creation failed.\n";
        }
    }
}
