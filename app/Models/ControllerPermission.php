<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ControllerPermission extends Model
{
    protected $fillable = [
        'controller',
        'method',
        'permissions',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'permissions' => 'array',
    ];

    // Method ini sudah tidak digunakan karena sekarang menggunakan multiple permissions

    public static function clearCache(): void
    {
        Cache::forget('controller_permissions_all');
        // Clear individual cache entries if needed
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }
}
