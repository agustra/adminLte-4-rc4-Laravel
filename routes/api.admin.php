<?php

use App\Http\Controllers\Api\V1\Admin\BackupApiController;
use App\Http\Controllers\Api\V1\Admin\MenuBadgeConfigApiController;
use App\Http\Controllers\Api\V1\Admin\MenuBadgeController;
use App\Http\Controllers\Api\V1\Admin\PermissionsApiController;
use App\Http\Controllers\Api\V1\Admin\RolesApiController;
use App\Http\Controllers\Api\V1\Admin\UsersApiController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('api.logout');

$skipRoutes = [
    // 'permissions' => ['json', 'by-ids'], // Aktifkan untuk TomSelect
    'backup' => ['json', 'by-ids'], // Skip json and by-ids for backup, use index route
    'menus' => ['json', 'by-ids'], // Skip json and by-ids for menus, use index route
    // bisa tambahkan resource lain yang ingin dilewati rutenya disini
];

// Resource API Routes users, permissions, roles, backup, menus, controller-permissions
Route::middleware('auth:api', 'dynamic.permission')->name('api.')->group(function () use ($skipRoutes) {
    $resources = [
        'users' => UsersApiController::class,
        'permissions' => PermissionsApiController::class,
        'roles' => RolesApiController::class,
        'backup' => BackupApiController::class,
        'menus' => \App\Http\Controllers\Api\V1\Admin\MenuApiController::class,
        'controller-permissions' => \App\Http\Controllers\Api\V1\ControllerPermissionApiController::class,
        'badge-configs' => MenuBadgeConfigApiController::class,
    ];

    foreach ($resources as $name => $controller) {

        // Route custom
        addRouteIfNotSkipped('json', $name, $controller, 'json', $skipRoutes);
        addRouteIfNotSkipped('by-ids', $name, $controller, 'getByIds', $skipRoutes);
        addRouteIfNotSkipped('bulkDelete', $name, $controller, 'bulkDelete', $skipRoutes);

        // Resource utama
        Route::apiResource($name, $controller)
            ->parameters([$name => 'id'])
            ->except(['create', 'edit']);


        // Special routes
        if ($name === 'backup') {
            Route::get("$name/counts", [$controller, 'counts'])->name("$name.counts");
            Route::get("$name/{filename}/download", [$controller, 'download'])->name("$name.download");
        }

        if ($name === 'users') {
            Route::get("$name/{id}/permissions/paginated", [$controller, 'getPermissionsPaginated'])->name("$name.permissions.paginated");
        }

        if ($name === 'roles') {
            Route::get("$name/{id}/permissions/paginated", [$controller, 'getPermissionsPaginated'])->name("$name.permissions.paginated");
        }
    }
});

Route::middleware('auth:api', 'dynamic.permission')->group(
    function () {

        // MENU BADGE
        Route::get('/menu/badge-count', [MenuBadgeController::class, 'getBadgeCount'])->name('menu.badge-count');
        Route::get('/menu/all-badge-counts', [MenuBadgeController::class, 'getAllBadgeCounts'])->name('menu.all-badge-counts');
        Route::post('/menu/clear-badge-cache', [MenuBadgeController::class, 'clearBadgeCache'])->name('menu.clear-badge-cache');

        // Badge Config Routes
        Route::get('/badge-configs/models', [MenuBadgeConfigApiController::class, 'getAvailableModels'])->name('badge-configs.models');
        Route::get('/badge-configs/model-fields', [MenuBadgeConfigApiController::class, 'getModelFields'])->name('badge-configs.model-fields');
    }
);
