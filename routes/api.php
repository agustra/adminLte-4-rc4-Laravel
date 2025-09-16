<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;

use Illuminate\Support\Facades\Route;

// Helper functions loaded from app/Helpers/RouteHelper.php

// Rute untuk autentikasi
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Rute yang memerlukan autentikasi
Route::middleware('auth:api')->group(function () {
    // Other authenticated routes can be added here
});

// Include file routes admin
require __DIR__.'/api.admin.php';

// Demo endpoints for Modern Table
Route::get('/demo-users', [\App\Http\Controllers\Api\DemoController::class, 'users']);

// Include FileManager API routes
// Contains: Admin FileManager, User FileManager, System Files, User Monitoring APIs
// Endpoints: /api/filemanager/*, /api/filemanager/my-files/*, /api/filemanager/system/*, /api/filemanager/monitoring/*
// Purpose: Modular organization for OpenAPI documentation and better maintainability
require __DIR__.'/api.filemanager.php';
