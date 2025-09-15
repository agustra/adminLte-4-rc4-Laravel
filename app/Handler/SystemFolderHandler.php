<?php

namespace App\Handler;

use UniSharp\LaravelFilemanager\Handlers\ConfigHandler;

class SystemFolderHandler extends ConfigHandler
{
    /**
     * System files handler - only shows public folder
     */
    public function userField()
    {
        // Always return 'public' to show only system files
        return 'public';
    }
    
    /**
     * Disable shared folder for system view
     */
    public function allowSharedFolder()
    {
        return false;
    }
}