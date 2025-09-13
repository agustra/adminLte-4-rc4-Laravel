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

// MEDIA LIBRARY ROUTES (Universal - not admin specific)
Route::middleware(['auth'])->group(function () {
    // Main media management page
    Route::get('/media-library', [\App\Http\Controllers\MediaController::class, 'index'])
        ->name('media.index');

    // Media picker modal (used by settings, avatar, CKEditor)
    Route::get('/media-library/modal', [\App\Http\Controllers\MediaController::class, 'getModal'])
        ->name('media.modal');

    // Media detail/show (needed for edit functionality)
    Route::get('/media-library/{id}', [\App\Http\Controllers\MediaController::class, 'show'])
        ->name('media.show');

    // Media partials for reuse
    Route::get('/media-library/partials/{partial}', function ($partial) {
        $allowedPartials = [
            'container',
            'styles',
            'media-grid',
            'toolbar',
            'navigation',
            'upload-area',
            'modals',
        ];

        if (! in_array($partial, $allowedPartials)) {
            abort(404);
        }

        return view("media.partials.{$partial}");
    })->name('media.partials');
});

// Include file routes admin
require __DIR__.'/admin.php';
