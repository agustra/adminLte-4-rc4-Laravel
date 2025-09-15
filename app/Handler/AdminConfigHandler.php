<?php

namespace App\Handler;

use UniSharp\LaravelFilemanager\Handlers\ConfigHandler;

class AdminConfigHandler extends ConfigHandler
{
    /**
     * Admin sees both shared and all user private folders
     * Users see their private folders only
     */
    public function userField()
    {
        // For admin routes, return null to show both shared and all private folders
        if (request()->is('admin/filemanager*')) {
            // Admin can see shared folder + all user folders (1, 2, 3, etc)
            return null;
        }
        
        // For user routes, return user ID for private folders only
        if (request()->is('user-filemanager*')) {
            return auth()->id();
        }
        
        // Default: return user ID for private folders
        return auth()->id();
    }
}