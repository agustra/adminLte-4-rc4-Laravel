<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define gates for user permissions
        Gate::define('read users', function (User $user) {
            return $user->hasPermissionTo('read users');
        });

        Gate::define('create users', function (User $user) {
            return $user->hasPermissionTo('create users');
        });

        Gate::define('edit users', function (User $user) {
            return $user->hasPermissionTo('edit users');
        });

        Gate::define('show users', function (User $user) {
            return $user->hasPermissionTo('show users');
        });

        Gate::define('delete users', function (User $user) {
            return $user->hasPermissionTo('delete users');
        });

        // Define gates for role permissions
        Gate::define('read roles', function (User $user) {
            return $user->hasPermissionTo('read roles');
        });

        Gate::define('create roles', function (User $user) {
            return $user->hasPermissionTo('create roles');
        });

        Gate::define('edit roles', function (User $user) {
            return $user->hasPermissionTo('edit roles');
        });

        Gate::define('show roles', function (User $user) {
            return $user->hasPermissionTo('show roles');
        });

        Gate::define('delete roles', function (User $user) {
            return $user->hasPermissionTo('delete roles');
        });

        // Define gates for permission permissions
        Gate::define('read permissions', function (User $user) {
            return $user->hasPermissionTo('read permissions');
        });

        Gate::define('create permissions', function (User $user) {
            return $user->hasPermissionTo('create permissions');
        });

        Gate::define('edit permissions', function (User $user) {
            return $user->hasPermissionTo('edit permissions');
        });

        Gate::define('show permissions', function (User $user) {
            return $user->hasPermissionTo('show permissions');
        });

        Gate::define('delete permissions', function (User $user) {
            return $user->hasPermissionTo('delete permissions');
        });
    }
}
