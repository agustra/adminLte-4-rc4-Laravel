<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Media\FolderController;
use App\Http\Controllers\Media\MediaController;
use App\Http\Controllers\Media\MediaManagementController;
use App\Http\Controllers\Media\MediaOperationsController;
use Illuminate\Support\Facades\Route;

// Helper functions loaded from app/Helpers/RouteHelper.php

// Rute untuk autentikasi
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Rute yang memerlukan autentikasi
Route::middleware('auth:api')->group(function () {

    // ===== MEDIA LIBRARY ROUTES =====
    Route::prefix('media')->name('api.media.')->group(function () {
        // File Upload
        Route::post('upload/file', [MediaController::class, 'uploadFile'])->name('upload.file');

        // Avatar Management
        Route::delete('avatar/{userId}', [MediaController::class, 'deleteAvatar'])->name('delete.avatar');

        // Folder Management
        Route::prefix('folders')->name('folders.')->group(function () {
            Route::get('/', [FolderController::class, 'index'])->name('index');
            Route::post('/', [FolderController::class, 'store'])->name('store');
            Route::post('/move', [FolderController::class, 'move'])->name('move');
            Route::post('/rename', [FolderController::class, 'rename'])->name('rename');
            Route::delete('/', [MediaOperationsController::class, 'deleteFolder'])->name('delete');
        });

        // File Operations
        Route::post('copy', [MediaOperationsController::class, 'copy'])->name('copy');
        Route::post('move', [MediaOperationsController::class, 'move'])->name('move');
    });

    // Media Management (CRUD)
    Route::prefix('media-management')->name('api.media-management.')->group(function () {
        Route::get('json', [MediaManagementController::class, 'json'])->name('json');
        Route::put('{id}', [MediaManagementController::class, 'update'])->name('update');
        Route::delete('{id}', [MediaManagementController::class, 'destroy'])->name('destroy');
        Route::post('multiple/delete', [MediaManagementController::class, 'deleteMultiple'])->name('deleteMultiple');
    });
});

// Include file routes admin
require __DIR__.'/api.admin.php';
