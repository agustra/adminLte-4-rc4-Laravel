<?php

namespace App\Http\Controllers\Admin\V1\FileManager;

use App\Http\Controllers\Controller;

class SystemFileManagerController extends Controller
{
    /**
     * System FileManager - shows only public folder
     */
    public function index()
    {
        // Temporarily override config to use system handler
        config(['lfm.private_folder_name' => \App\Handler\SystemFolderHandler::class]);
        config(['lfm.allow_shared_folder' => false]);
        
        // Forward to FileManager
        $lfmController = app(\UniSharp\LaravelFilemanager\Controllers\LfmController::class);
        return $lfmController->show(request());
    }
}