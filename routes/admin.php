<?php

use App\Http\Controllers\Admin\V1\BackupController;
use App\Http\Controllers\Admin\V1\ControllerPermissionController;
use App\Http\Controllers\Admin\V1\MenuBadgeConfigController;
use App\Http\Controllers\Admin\V1\MenuController;
use App\Http\Controllers\Admin\V1\PermissionController;
use App\Http\Controllers\Admin\V1\RoleController;
use App\Http\Controllers\Admin\V1\SettingController;
use App\Http\Controllers\Admin\V1\UserController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Route::middleware('guest')->group(function () {
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout');
// });

// API endpoint untuk sidebar refresh - accessible without strict auth
Route::get('/admin/api/menus/sidebar', [\App\Http\Controllers\Api\V1\Admin\MenuApiController::class, 'getSidebarMenu'])->name('api.menus.sidebar');

$skipRoutes = [
    // 'permissions' => ['json', 'by-ids'],
];

// Resource WEB Routes users, permissions, roles
Route::prefix('admin')->middleware('auth:web', 'dynamic.permission')->group(function () {
    $resources = [
        'users' => UserController::class,
        'permissions' => PermissionController::class,
        'roles' => RoleController::class,
        'backup' => BackupController::class,
        'menus' => MenuController::class,
        'badge-configs' => MenuBadgeConfigController::class,
        'controller-permissions' => ControllerPermissionController::class,
    ];

    foreach ($resources as $name => $controller) {

        // Resource utama
        Route::resource($name, $controller)
            ->parameters([$name => 'id'])
            ->except(['store', 'update', 'destroy']);

        // Route custom
        // addRouteIfNotSkipped('bulkDelete', $name, $controller, 'bulkDelete', $skipRoutes);

        // Special routes
        if ($name === 'backup') {
            Route::get("$name/create", [$controller, 'create'])->name("$name.create");
        }
    }
});

Route::prefix('admin')->middleware('auth', 'dynamic.permission')->group(function () {
    // -------------------------- Setting ----------------------------------------------//
    Route::get('/settings', [SettingController::class, 'index'])->middleware('permission:read settings')->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->middleware('permission:create settings')->name('settings.store');
});
