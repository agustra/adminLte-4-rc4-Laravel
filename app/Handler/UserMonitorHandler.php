<?php

namespace App\Handler;

use UniSharp\LaravelFilemanager\Handlers\ConfigHandler;

class UserMonitorHandler extends ConfigHandler
{
    /**
     * User monitoring handler - shows all user folders but excludes public
     */
    public function userField()
    {
        // Return null to show all folders, but allowSharedFolder() will exclude public
        return null;
    }
    
    /**
     * Disable shared folder for user monitoring (no public folder)
     */
    public function allowSharedFolder()
    {
        return false;
    }
}