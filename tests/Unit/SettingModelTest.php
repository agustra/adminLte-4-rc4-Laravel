<?php

namespace Tests\Unit;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_setting_can_be_created(): void
    {
        $setting = Setting::create([
            'key' => 'app_name',
            'value' => 'Test Application',
        ]);

        $this->assertInstanceOf(Setting::class, $setting);
        $this->assertEquals('app_name', $setting->key);
        $this->assertEquals('Test Application', $setting->value);
    }

    public function test_setting_has_fillable_attributes(): void
    {
        $setting = new Setting;
        $fillable = $setting->getFillable();

        $expectedFillable = ['key', 'value'];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    public function test_setting_key_is_unique(): void
    {
        Setting::create([
            'key' => 'unique_key',
            'value' => 'First Value',
        ]);

        // Try to create another setting with the same key
        try {
            Setting::create([
                'key' => 'unique_key',
                'value' => 'Second Value',
            ]);

            // If no exception is thrown, check if it updates instead
            $settings = Setting::where('key', 'unique_key')->get();
            $this->assertLessThanOrEqual(1, $settings->count());
        } catch (\Exception $e) {
            // If exception is thrown due to unique constraint, that's expected
            $this->assertTrue(true);
        }
    }

    public function test_setting_can_store_json_values(): void
    {
        $jsonValue = json_encode(['theme' => 'dark', 'language' => 'en']);

        $setting = Setting::create([
            'key' => 'app_config',
            'value' => $jsonValue,
        ]);

        $this->assertEquals($jsonValue, $setting->value);

        // Test if it can be decoded back
        $decoded = json_decode($setting->value, true);
        $this->assertIsArray($decoded);
        $this->assertEquals('dark', $decoded['theme']);
    }

    public function test_setting_can_be_updated(): void
    {
        $setting = Setting::create([
            'key' => 'app_name',
            'value' => 'Old Name',
        ]);

        $setting->update(['value' => 'New Name']);

        $this->assertEquals('New Name', $setting->fresh()->value);
    }

    public function test_setting_can_be_deleted(): void
    {
        $setting = Setting::create([
            'key' => 'temp_setting',
            'value' => 'temp_value',
        ]);

        $settingId = $setting->id;
        $setting->delete();

        $this->assertDatabaseMissing('settings', ['id' => $settingId]);
    }

    public function test_setting_value_can_be_null(): void
    {
        $setting = Setting::create([
            'key' => 'nullable_setting',
            'value' => null,
        ]);

        $this->assertNull($setting->value);
    }

    public function test_setting_value_can_be_empty_string(): void
    {
        $setting = Setting::create([
            'key' => 'empty_setting',
            'value' => '',
        ]);

        $this->assertEquals('', $setting->value);
    }
}
