<?php

namespace App\Http\Controllers\Admin\V1\FileManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserMonitorController extends Controller
{
    /**
     * User Monitor FileManager - excludes public folder
     */
    public function index(Request $request)
    {
        // Override config to use custom handler and disable shared folder
        config(['lfm.private_folder_name' => \App\Handler\UserMonitorHandler::class]);
        config(['lfm.allow_shared_folder' => false]);
        
        // Forward to FileManager
        $lfmController = app(\UniSharp\LaravelFilemanager\Controllers\LfmController::class);
        return $lfmController->show($request);
    }
    
    /**
     * Handle AJAX requests for folder listing
     */
    public function jsonItems(Request $request)
    {
        // Override config
        config(['lfm.private_folder_name' => \App\Handler\UserMonitorHandler::class]);
        config(['lfm.allow_shared_folder' => false]);
        
        // Get FileManager response
        $lfmController = app(\UniSharp\LaravelFilemanager\Controllers\ItemsController::class);
        $response = $lfmController->getItems($request);
        
        // Filter out public folder from response
        $data = $response->getData(true);
        if (isset($data['items'])) {
            $data['items'] = array_filter($data['items'], function($item) {
                return $item['name'] !== 'public';
            });
            $data['items'] = array_values($data['items']); // Re-index array
        }
        
        return response()->json($data);
    }
}