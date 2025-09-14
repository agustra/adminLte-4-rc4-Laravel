<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\MenuBadgeService;
use App\Traits\ApiResponse;
use App\Traits\HandleErrors;
use App\Traits\HasDynamicPermissions;
use App\Traits\HasQueryBuilder;
use App\Traits\TableHelpers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class MenuBadgeController extends Controller
{
    use ApiResponse, HandleErrors, HasDynamicPermissions, HasQueryBuilder, TableHelpers;
    use AuthorizesRequests;

    /**
     * Get badge count for specific menu URL
     */
    public function getBadgeCount(Request $request)
    {
        $menuUrl = $request->get('url');

        if (! $menuUrl) {
            return response()->json([
                'success' => false,
                'message' => 'Menu URL is required',
            ], 400);
        }

        $count = MenuBadgeService::getBadgeCount($menuUrl);
        $color = MenuBadgeService::getBadgeColor($count);

        return response()->json([
            'success' => true,
            'count' => $count,
            'color' => $color,
        ]);
    }

    /**
     * Get all badge counts for all configured menus (active and inactive)
     */
    public function getAllBadgeCounts()
    {
        // Get ALL configured menu URLs (not just active ones)
        $menuUrls = \App\Models\MenuBadgeConfig::pluck('menu_url')->toArray();

        $badges = [];

        foreach ($menuUrls as $menuUrl) {
            $count = MenuBadgeService::getBadgeCount($menuUrl);
            $color = MenuBadgeService::getBadgeColor($count);

            $badges[$menuUrl] = [
                'count' => $count,
                'color' => $color,
            ];
        }

        return response()->json([
            'success' => true,
            'badges' => $badges,
        ]);
    }

    public function clearBadgeCache(Request $request)
    {
        $menuUrl = $request->get('url');

        if ($menuUrl) {
            MenuBadgeService::clearBadgeCache($menuUrl);
        } else {
            MenuBadgeService::clearAllBadgeCache();
        }

        return response()->json([
            'success' => true,
            'message' => 'Badge cache cleared',
        ]);
    }
}
