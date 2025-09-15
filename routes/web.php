<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/clear/cache', function () {
    Artisan::call('app:clear-all');

    return redirect()->back()->with('success', 'Cache berhasil dihapus!');
})->name('clear.cache');




Route::get('/test', function () {
    return view('home');
});




Route::middleware(['auth'])->group(function () {
    // Arahkan root "/" langsung ke dashboard
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Alias "/dashboard" menuju route "dashboard"
    Route::get('/dashboard', fn () => redirect()->route('dashboard'));
});



// USER PROFILE & FILE MANAGER ROUTES
Route::middleware(['auth'])->group(function () {
    // Profile routes
    Route::get('/profile', [\App\Http\Controllers\Profile\ProfileController::class, 'index'])
        ->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\Profile\ProfileController::class, 'update'])
        ->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\Profile\ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');
    
    // File Manager routes
    Route::get('/my-files', [\App\Http\Controllers\Profile\FileManagerController::class, 'index'])
        ->name('user.filemanager.index');
    Route::get('/my-files/popup', [\App\Http\Controllers\Profile\FileManagerController::class, 'popup'])
        ->name('user.filemanager.popup');
        
    // Custom upload route for user FileManager (must be before Laravel FileManager routes)
    Route::any('user-filemanager/upload', [\App\Http\Controllers\Profile\FileManagerUploadController::class, 'upload'])
        ->middleware(['web', 'auth', 'permission:read filemanager'])
        ->name('user.lfm.upload');
    
    // User-specific FileManager routes with private folders only (no shared access)
    Route::group(['prefix' => 'user-filemanager', 'middleware' => ['web', 'auth', 'permission:read filemanager'], 'as' => 'user.lfm.'], function () {
        // Other FileManager routes
        \UniSharp\LaravelFilemanager\Lfm::routes();
    });
});

// Include file routes admin
require __DIR__.'/admin.php';
