<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Menu\Laravel\Menu;

class MenuServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Menu::macro('adminLte', function () {
            return Menu::new()
                ->addClass('nav sidebar-menu flex-column')
                ->setAttribute('data-lte-toggle', 'treeview')
                ->setAttribute('role', 'navigation')
                ->setAttribute('aria-label', 'Main navigation')
                ->setAttribute('data-accordion', 'false')
                ->setAttribute('id', 'navigation');
        });
    }
}
