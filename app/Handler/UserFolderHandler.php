<?php

namespace App\Handler;

use UniSharp\LaravelFilemanager\Handlers\ConfigHandler;
use App\Models\User;

class UserFolderHandler extends ConfigHandler
{
    /**
     * FileManager access control with user-friendly folder names
     */
    public function userField()
    {
        $user = auth()->user();
        
        // Check route type by URL path
        $path = request()->path();
        $isSystemRoute = str_contains($path, 'system-filemanager') || str_contains(request()->url(), 'system-filemanager');
        $isUserMonitorRoute = str_contains($path, 'user-monitor-filemanager') || str_contains(request()->url(), 'user-monitor-filemanager');
        $isAdminRoute = str_contains($path, 'admin/filemanager') || str_contains(request()->url(), 'admin/filemanager');
        
        // System routes: Only public folder
        if ($isSystemRoute) {
            return 'public';
        }
        
        // User monitor routes: All user folders but no public
        if ($isUserMonitorRoute) {
            if ($user && $user->can('menu filemanager')) {
                return null; // Shows all user folders
            }
            return $this->getUserFolderName($user);
        }
        
        // Admin routes: Full access for admin with menu permission
        if ($isAdminRoute) {
            if ($user && $user->can('menu filemanager')) {
                return null; // Shows shared + all user folders
            }
            return $this->getUserFolderName($user);
        }
        
        // User routes: Private folder only
        return $this->getUserFolderName($user);
    }
    
    /**
     * Generate clean folder name from user data
     */
    private function getUserFolderName($user)
    {
        // Create clean folder name: "auditor" instead of "9"
        $folderName = strtolower(str_replace(' ', '-', $user->name));
        
        // Remove special characters for filesystem safety
        $folderName = preg_replace('/[^a-z0-9\-]/', '', $folderName);
        
        return $folderName;
    }
    
    /**
     * Shared folder visibility - only admin can see shared folder
     */
    public function allowSharedFolder()
    {
        $user = auth()->user();
        
        // Check route type and parameters
        $path = request()->path();
        $hidePublic = request()->get('hide_public');
        $isSystemRoute = str_contains($path, 'system-filemanager') || str_contains(request()->url(), 'system-filemanager');
        $isUserMonitorRoute = str_contains($path, 'user-monitor-filemanager') || str_contains(request()->url(), 'user-monitor-filemanager');
        $isAdminRoute = str_contains($path, 'admin/filemanager') || str_contains(request()->url(), 'admin/filemanager');
        
        // System routes: No shared folder (only public folder)
        if ($isSystemRoute) {
            return false;
        }
        
        // User monitor routes OR hide_public parameter: No shared folder
        if ($isUserMonitorRoute || $hidePublic) {
            return false;
        }
        
        // Admin routes: Enable shared folder for admin only
        if ($isAdminRoute) {
            if ($user && $user->can('menu filemanager')) {
                return true;
            }
        }
        
        // User routes: completely disable shared folder
        return false;
    }
    
    /**
     * Override upload permission check
     */
    public function canUpload()
    {
        $user = auth()->user();
        
        // Admin routes: Need menu permission for shared folder upload
        if (request()->is('admin/filemanager*')) {
            return $user && $user->can('menu filemanager');
        }
        
        // User routes: Can upload to private folder if has create permission
        return $user && $user->can('create filemanager');
    }
}