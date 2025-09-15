<?php

namespace App\Handler;

use UniSharp\LaravelFilemanager\Handlers\ConfigHandler;

class AdminOnlyHandler extends ConfigHandler
{
    /**
     * Admin-only access - can see shared folder and all user folders
     */
    public function userField()
    {
        $user = auth()->user();
        
        // Only admin/super admin can use this handler
        if ($user && $user->hasRole(['admin', 'super admin'])) {
            return null; // Shows shared folder + all user folders
        }
        
        // Non-admin gets redirected or blocked
        abort(403, 'Admin access required');
    }
}