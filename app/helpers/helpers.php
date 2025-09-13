<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

if (! function_exists('setting')) {
    function setting($key, $default = null)
    {
        return config('settings.'.$key, $default);
    }
}

if (! function_exists('format_rupiah')) {
    function format_rupiah($angka)
    {
        return 'Rp '.number_format($angka, 0, ',', '.');
    }
}

if (! function_exists('parse_rupiah')) {
    function parse_rupiah($rupiah)
    {
        return (int) str_replace(['Rp ', '.', ','], '', $rupiah);
    }
}

if (! function_exists('format_tanggal')) {
    function format_tanggal($tanggal)
    {
        return Carbon::parse($tanggal)->format('d-m-Y');
    }
}

// MobileDetect removed - using simple User-Agent check instead
if (! function_exists('isMobile')) {
    function isMobile()
    {
        $userAgent = request()->header('User-Agent');

        return preg_match('/(android|iphone|ipad|mobile)/i', $userAgent);
    }
}

function isTablet()
{
    $userAgent = request()->header('User-Agent');

    return preg_match('/(ipad|tablet)/i', $userAgent);
}

function isDesktop()
{
    return ! isMobile() && ! isTablet();
}

// addRouteIfNotSkipped function moved to app/Helpers/RouteHelper.php to avoid duplication

/**
 * Cek apakah user memiliki permission untuk controller dan method tertentu
 */
if (! function_exists('dynamicCan')) {
    function dynamicCan($controller, $method)
    {
        if (! auth()->check()) {
            return false;
        }

        $controllerPermission = \App\Models\ControllerPermission::where('controller', $controller)
            ->where('method', $method)
            ->where('is_active', true)
            ->first();

        if (! $controllerPermission || ! $controllerPermission->permissions) {
            return true; // Jika tidak ada mapping, izinkan akses
        }

        // Cek apakah user memiliki salah satu dari permissions yang diperlukan
        foreach ($controllerPermission->permissions as $permission) {
            if (auth()->user()->can($permission)) {
                return true;
            }
        }

        return false;
    }
}

/**
 * Get all permissions untuk controller tertentu
 */
if (! function_exists('getControllerPermissions')) {
    function getControllerPermissions($controller)
    {
        if (! auth()->check()) {
            return [];
        }

        $permissions = \App\Models\ControllerPermission::where('controller', $controller)
            ->where('is_active', true)
            ->get()
            ->mapWithKeys(function ($item) {
                $hasPermission = auth()->user()->can($item->permission);

                return [$item->method => $hasPermission];
            });

        return $permissions->toArray();
    }
}
