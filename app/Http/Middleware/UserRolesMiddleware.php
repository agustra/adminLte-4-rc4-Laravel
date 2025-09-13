<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserRolesMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // if (Auth::check()) {
        //     $user = Auth::user();
        //     $userRoles = $user->roles ? $user->roles->pluck('name')->toArray() : [];

        //     // Cache daftar role untuk mengurangi query database
        //     $collectionOfRoles = cache()->rememberForever('roles_list', function () {
        //         return \Spatie\Permission\Models\Role::pluck('name')->toArray();
        //     });

        //     // Bagikan ke semua view
        //     view()->share(compact('userRoles', 'collectionOfRoles'));
        // }

        return $next($request);
    }
}
