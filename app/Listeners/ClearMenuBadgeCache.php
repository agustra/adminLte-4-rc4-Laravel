<?php

namespace App\Listeners;

use App\Services\MenuBadgeService;
use Illuminate\Database\Eloquent\Model;

class ClearMenuBadgeCache
{
    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        $model = $event->model ?? $event;

        if (! $model instanceof Model) {
            return;
        }

        // Clear cache berdasarkan model yang berubah
        $menuUrl = $this->getMenuUrlFromModel($model);

        if ($menuUrl) {
            MenuBadgeService::clearBadgeCache($menuUrl);
        }
    }

    /**
     * Get menu URL based on model class
     */
    private function getMenuUrlFromModel($model): ?string
    {
        $modelClass = get_class($model);

        return match ($modelClass) {
            'App\Models\User' => '/admin/users',
            'Spatie\Permission\Models\Role' => '/admin/roles',
            'Spatie\Permission\Models\Permission' => '/admin/permissions',
            'App\Models\Menu' => '/admin/menus',
            default => null,
        };
    }
}
