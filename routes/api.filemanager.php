<?php

use App\Http\Controllers\Api\V1\FileManager\FileManagerApiController;
use App\Http\Controllers\Api\V1\FileManager\UserFileManagerController;
use App\Http\Controllers\Api\V1\FileManager\SystemFilesController;
use App\Http\Controllers\Api\V1\FileManager\UserMonitoringController;
use Illuminate\Support\Facades\Route;

// ===== ADMIN FILEMANAGER API (Full Access) =====
Route::middleware(['auth:api', 'permission:menu filemanager'])->prefix('filemanager')->name('api.filemanager.')->group(function () {
    
    // Browse files and folders (admin can access all)
    Route::get('/', [FileManagerApiController::class, 'index'])->middleware('permission:read filemanager')->name('index');
    
    // File operations
    Route::post('/upload', [FileManagerApiController::class, 'upload'])->middleware('permission:create filemanager')->name('upload');
    Route::delete('/delete', [FileManagerApiController::class, 'delete'])->middleware('permission:delete filemanager')->name('delete');
    Route::post('/rename', [FileManagerApiController::class, 'rename'])->middleware('permission:edit filemanager')->name('rename');
    
    // Folder operations
    Route::post('/folder', [FileManagerApiController::class, 'createFolder'])->middleware('permission:create filemanager')->name('folder.create');
    
    // Bulk operations
    Route::post('/bulk-delete', [FileManagerApiController::class, 'bulkDelete'])->middleware('permission:delete filemanager')->name('bulk.delete');
    Route::post('/bulk-move', [FileManagerApiController::class, 'bulkMove'])->middleware('permission:edit filemanager')->name('bulk.move');
});

// ===== USER FILEMANAGER API (Own Files Only) =====
Route::middleware(['auth:api', 'permission:read filemanager'])->prefix('filemanager/my-files')->name('api.filemanager.my-files.')->group(function () {
    Route::get('/', [UserFileManagerController::class, 'index'])->name('index');
    Route::post('/upload', [UserFileManagerController::class, 'upload'])->middleware('permission:create filemanager')->name('upload');
    Route::post('/folder', [UserFileManagerController::class, 'createFolder'])->middleware('permission:create filemanager')->name('folder');
    Route::delete('/delete', [UserFileManagerController::class, 'delete'])->middleware('permission:delete filemanager')->name('delete');
    Route::post('/rename', [UserFileManagerController::class, 'rename'])->middleware('permission:edit filemanager')->name('rename');
});

// ===== SYSTEM FILES API (Public Folder - Read Only) =====
Route::middleware(['auth:api', 'permission:read filemanager'])->prefix('filemanager/system')->name('api.filemanager.system.')->group(function () {
    Route::get('/', [SystemFilesController::class, 'index'])->name('index');
    Route::get('/images', [SystemFilesController::class, 'images'])->name('images');
    Route::get('/files', [SystemFilesController::class, 'files'])->name('files');
    Route::get('/show', [SystemFilesController::class, 'show'])->name('show');
});

// ===== USER MONITORING API (Admin Only) =====
Route::middleware(['auth:api', 'permission:menu filemanager'])->prefix('filemanager/monitoring')->name('api.filemanager.monitoring.')->group(function () {
    Route::get('/', [UserMonitoringController::class, 'index'])->name('index');
    Route::get('/storage-usage', [UserMonitoringController::class, 'storageUsage'])->name('storage-usage');
    Route::get('/users/{userId}/files', [UserMonitoringController::class, 'show'])->name('user-files');
    Route::delete('/delete-file', [UserMonitoringController::class, 'deleteFile'])->name('delete-file');
    Route::post('/cleanup', [UserMonitoringController::class, 'cleanup'])->name('cleanup');
});