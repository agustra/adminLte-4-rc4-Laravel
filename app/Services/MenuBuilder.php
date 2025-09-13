<?php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Facades\Cache; // Bisa dihapus jika tidak digunakan lagi

class MenuBuilder
{
    public static function build()
    {
        // Langsung return hasil build dari database TANPA caching
        return self::buildFromDatabase();
    }

    private static function buildFromDatabase()
    {
        $menus = Menu::with('children')
            ->whereNull('parent_id')
            ->where('is_active', 'aktif')
            ->orderBy('order')
            ->get();

        $html = '<ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation" aria-label="Main navigation" data-accordion="false" id="navigation">';

        foreach ($menus as $menu) {
            $html .= self::renderMenuItem($menu);
        }

        $html .= '</ul>';

        return $html;
    }

    private static function renderMenuItem($menu)
    {
        // Check authentication from multiple guards (web, api)
        $user = auth('web')->user() ?? auth('api')->user();

        // If no user is authenticated, show all menus (for development/testing)
        if (! $user) {
            // For development: show all menus when not authenticated
            // In production, you might want to return '' here
        } else {

            // Permission check for authenticated users
            if ($menu->permission) {
                try {
                    // Admin can see all menus
                    if ($user->hasRole('admin') || $user->hasRole('Admin')) {
                        // Admin can see everything
                    } elseif (! $user->can($menu->permission)) {
                        return '';
                    }
                } catch (\Exception $e) {
                    // If permission check fails, allow for admin users only
                    if (! $user->hasRole('admin') && ! $user->hasRole('Admin')) {
                        return '';
                    }
                }
            }

            // Admin role check for admin URLs
            if (str_contains($menu->url, '/admin/')) {
                if (! $user->hasRole('admin') && ! $user->hasRole('Admin')) {
                    return '';
                }
            }
        }

        $hasChildren = $menu->children->where('is_active', 'aktif')->count() > 0;
        $isActive = self::isMenuActive($menu);

        $html = '<li class="nav-item'.($hasChildren ? ' has-treeview' : '').($isActive ? ' menu-open' : '').'">';

        if ($hasChildren) {
            // Parent menu with children
            $html .= '<a href="#" class="nav-link'.($isActive ? ' active' : '').'">';
            $html .= '<i class="nav-icon '.($menu->icon ?: 'bi bi-folder').'"></i>';
            $html .= '<p class="d-flex justify-content-between align-items-center w-100">';
            $html .= '<span>'.$menu->name.'</span>';

            // Dynamic badge berdasarkan perubahan data hari ini
            $badgeCount = MenuBadgeService::getBadgeCount($menu->url);
            if ($badgeCount > 0) {
                $badgeColor = MenuBadgeService::getBadgeColor($badgeCount);
                $html .= '<span class="badge bg-'.$badgeColor.' ms-auto me-2">'.$badgeCount.'</span>';
            }
            $html .= '<i class="nav-arrow bi bi-chevron-right"></i>';
            $html .= '</p>';
            $html .= '</p>';
            $html .= '</a>';
            $html .= '<ul class="nav nav-treeview" role="navigation" aria-label="Navigation">';

            foreach ($menu->children->where('is_active', 'aktif')->sortBy('order') as $child) {
                $html .= self::renderMenuItem($child);
            }

            $html .= '</ul>';
        } else {
            // Single menu item
            $activeClass = request()->is(ltrim($menu->url, '/')) || request()->is(ltrim($menu->url, '/').'/*') ? ' active' : '';
            if ($menu->url === '/dashboard') {
                $activeClass .= request()->is('dashboard') ? ' slant' : ' slant';
            }

            $html .= '<a href="'.url($menu->url).'" class="nav-link'.$activeClass.'">';
            $html .= '<i class="nav-icon '.($menu->parent_id ? 'bi bi-circle' : ($menu->icon ?: 'bi bi-link')).'"></i>';
            $html .= '<p class="d-flex justify-content-between align-items-center w-100">';
            $html .= '<span>'.$menu->name.'</span>';

            // Dynamic badge berdasarkan perubahan data hari ini
            $badgeCount = MenuBadgeService::getBadgeCount($menu->url);
            if ($badgeCount > 0) {
                $badgeColor = MenuBadgeService::getBadgeColor($badgeCount);
                $html .= '<span class="badge bg-'.$badgeColor.' ms-auto">'.$badgeCount.'</span>';
            }
            $html .= '</p>';
            $html .= '</a>';
        }

        $html .= '</li>';

        return $html;
    }

    private static function isMenuActive($menu)
    {
        if ($menu->children->where('is_active', 'aktif')->count() === 0) {
            return false;
        }

        foreach ($menu->children->where('is_active', 'aktif') as $child) {
            if (request()->is(ltrim($child->url, '/')) || request()->is(ltrim($child->url, '/').'/*')) {
                return true;
            }
        }

        return false;
    }

    // Method clearCache() bisa dihapus karena sudah tidak diperlukan
    // public static function clearCache()
    // {
    //     Cache::forget('admin_menu_' . auth()->id());
    // }
}
