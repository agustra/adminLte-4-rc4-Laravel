<?php

namespace App\Services;

use App\Models\MenuBadgeConfig;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MenuBadgeService
{
    /**
     * Get badge count for menu based on today's changes
     */
    public static function getBadgeCount($menuUrl)
    {
        $cacheKey = 'menu_badge_' . hash('sha256', $menuUrl) . '_' . Carbon::today()->format('Y-m-d');

        return Cache::remember($cacheKey, 300, function () use ($menuUrl) { // Cache 5 menit
            return self::calculateBadgeCount($menuUrl);
        });
    }

    /**
     * Calculate badge count based on menu URL
     */
    private static function calculateBadgeCount($menuUrl)
    {
        $today = Carbon::today();

        // Check database configuration first
        $config = MenuBadgeConfig::where('menu_url', $menuUrl)
            ->where('is_active', true)
            ->first();

        if ($config) {
            try {
                $modelClass = $config->model_class;
                $dateField = $config->date_field;

                if (class_exists($modelClass)) {
                    $dateField = $config->date_field;

                    // Check if multiple fields (comma-separated)
                    if (str_contains($dateField, ',')) {
                        // Multiple fields
                        $dateFields = array_map('trim', explode(',', $dateField));
                        $totalCount = 0;
                        $countedIds = [];

                        foreach ($dateFields as $field) {
                            // Get records for this date field
                            $records = $modelClass::whereDate($field, $today)
                                ->whereNotIn('id', $countedIds)
                                ->get(['id']);

                            $totalCount += $records->count();

                            // Track counted IDs to avoid duplicates
                            $countedIds = array_merge($countedIds, $records->pluck('id')->toArray());
                        }

                        return $totalCount;
                    } else {
                        // Single field
                        return $modelClass::whereDate($dateField, $today)->count();
                    }
                }
            } catch (\Exception $e) {
                Log::error('Badge calculation error', [
                    'url' => $menuUrl,
                    'model' => $config->model_class,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // No configuration found, return 0
        return 0;
    }

    /**
     * Clear badge cache for specific menu
     */
    public static function clearBadgeCache($menuUrl)
    {
        $cacheKey = 'menu_badge_' . hash('sha256', $menuUrl) . '_' . Carbon::today()->format('Y-m-d');
        Cache::forget($cacheKey);
    }

    /**
     * Clear all badge caches for today
     */
    public static function clearAllBadgeCache()
    {
        $today = Carbon::today()->format('Y-m-d');

        // Get all configured menu URLs (active and inactive)
        $configuredMenus = MenuBadgeConfig::pluck('menu_url')->toArray();

        foreach ($configuredMenus as $menuUrl) {
            $cacheKey = 'menu_badge_' . hash('sha256', $menuUrl) . '_' . $today;
            Cache::forget($cacheKey);
        }
    }

    /**
     * Get badge color based on count
     */
    public static function getBadgeColor($count)
    {
        if ($count == 0) {
            return null;
        } // No badge
        if ($count <= 2) {
            return 'primary';
        } // Biru - kontras baik dengan putih
        if ($count <= 5) {
            return 'success';
        } // Hijau - kontras baik dengan putih

        return 'danger'; // Merah - kontras baik dengan putih
    }
}
