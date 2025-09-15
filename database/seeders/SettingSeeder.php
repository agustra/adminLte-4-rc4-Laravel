<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            // Application Settings
            ['key' => 'app_name', 'value' => 'AdminLTE Laravel'],
            ['key' => 'app_description', 'value' => 'Modern Admin Dashboard with Laravel & AdminLTE'],
            ['key' => 'app_logo', 'value' => 'filemanager/images/public/AdminLTELogo.png'],
            ['key' => 'app_version', 'value' => '1.0.0'],

            // Company Information
            ['key' => 'company_name', 'value' => 'Your Company Name'],
            ['key' => 'company_address', 'value' => 'Your Company Address\nCity, State 12345'],
            ['key' => 'contact_email', 'value' => 'admin@yourcompany.com'],
            ['key' => 'contact_phone', 'value' => '+1 (555) 123-4567'],
        ];

        foreach ($data as $value) {
            Setting::updateOrCreate([
                'key' => $value['key'],
            ], [
                'value' => $value['value'],
            ]);
        }
    }
}
