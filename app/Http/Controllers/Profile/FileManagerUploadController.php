<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileManagerUploadController extends Controller
{
    /**
     * Handle user FileManager uploads - redirect to correct route
     */
    public function upload(Request $request)
    {
        $user = auth()->user();
        
        // Check permission
        if (!$user->can('create filemanager')) {
            abort(403, 'You do not have permission to upload files');
        }
        
        // Set working directory to user's private folder
        $request->merge(['working_dir' => $user->id]);
        
        // Forward to FileManager upload handler
        $lfmController = app(\UniSharp\LaravelFilemanager\Controllers\UploadController::class);
        return $lfmController->upload($request);
    }
}