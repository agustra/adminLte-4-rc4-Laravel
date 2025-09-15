<?php

namespace App\Providers;

use App\Listeners\ClearMenuBadgeCache;
use App\Models\Menu;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // cara cek query terkena N+1
        // DB::listen(function ($query) {
        //     logger()->info($query->sql, $query->bindings);
        // });

        Carbon::setLocale(config('app.locale'));
        date_default_timezone_set(config('app.timezone'));

        // **PASSPORT CONFIGURATION**
        if (class_exists(Passport::class)) {
            Passport::tokensExpireIn(now()->addDays(15));
            Passport::refreshTokensExpireIn(now()->addDays(30));
            Passport::personalAccessTokensExpireIn(now()->addMonths(6));

            // Pastikan Passport membaca kunci dari storage/oauth
            Passport::loadKeysFrom(storage_path('oauth'));

            // Pastikan token dapat digunakan tanpa memeriksa scope
            Passport::tokensCan([
                'read' => 'Read access',
                'write' => 'Write access',
            ]);
        }

        // **CUSTOM RESET PASSWORD URL**
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        // **DYNAMIC CONFIG SETTINGS**
        if (! $this->app->runningInConsole()) {
            $settings = Setting::all('key', 'value')
                ->keyBy('key')
                ->transform(fn ($setting) => $setting->value)
                ->toArray();

            config(['settings' => $settings]);
            config(['app.name' => config('settings.app_name')]);

            // $activeTheme = Setting::where('key', 'app.active_theme')->value('value') ?? 'adminlte';
            // config(['app.active_theme' => $activeTheme]);
        }



        // **BLADE DIRECTIVES FOR DYNAMIC PERMISSIONS**
        \Illuminate\Support\Facades\Blade::directive('dynamiccan', function ($expression) {
            return "<?php if(dynamicCan($expression)): ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('enddynamiccan', function () {
            return '<?php endif; ?>';
        });

        \Illuminate\Support\Facades\Blade::directive('dynamiccannot', function ($expression) {
            return "<?php if(!dynamicCan($expression)): ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('enddynamiccannot', function () {
            return '<?php endif; ?>';
        });

        // **MENU BADGE CACHE CLEARING**
        // Listen to model events for badge cache clearing
        Event::listen('eloquent.created: App\Models\User', [ClearMenuBadgeCache::class, 'handle']);
        Event::listen('eloquent.deleted: App\Models\User', [ClearMenuBadgeCache::class, 'handle']);
        Event::listen('eloquent.created: Spatie\Permission\Models\Role', [ClearMenuBadgeCache::class, 'handle']);
        Event::listen('eloquent.deleted: Spatie\Permission\Models\Role', [ClearMenuBadgeCache::class, 'handle']);
        Event::listen('eloquent.created: Spatie\Permission\Models\Permission', [ClearMenuBadgeCache::class, 'handle']);
        Event::listen('eloquent.deleted: Spatie\Permission\Models\Permission', [ClearMenuBadgeCache::class, 'handle']);
        Event::listen('eloquent.created: App\Models\Menu', [ClearMenuBadgeCache::class, 'handle']);
        Event::listen('eloquent.deleted: App\Models\Menu', [ClearMenuBadgeCache::class, 'handle']);
    }
}
