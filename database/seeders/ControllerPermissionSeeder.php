<?php

namespace Database\Seeders;

use App\Models\ControllerPermission;
use Illuminate\Database\Seeder;

class ControllerPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $mappings = [
            // UserController
            ['controller' => 'UserController', 'method' => 'index', 'permissions' => ['read users']],
            ['controller' => 'UserController', 'method' => 'create', 'permissions' => ['create users']],
            ['controller' => 'UserController', 'method' => 'store', 'permissions' => ['create users']],
            ['controller' => 'UserController', 'method' => 'show', 'permissions' => ['show users']],
            ['controller' => 'UserController', 'method' => 'edit', 'permissions' => ['edit users']],
            ['controller' => 'UserController', 'method' => 'update', 'permissions' => ['edit users']],
            ['controller' => 'UserController', 'method' => 'destroy', 'permissions' => ['delete users']],

            // UsersApiController
            ['controller' => 'UsersApiController', 'method' => 'index', 'permissions' => ['read users']],
            ['controller' => 'UsersApiController', 'method' => 'store', 'permissions' => ['create users']],
            ['controller' => 'UsersApiController', 'method' => 'show', 'permissions' => ['show users']],
            ['controller' => 'UsersApiController', 'method' => 'update', 'permissions' => ['edit users']],
            ['controller' => 'UsersApiController', 'method' => 'destroy', 'permissions' => ['delete users']],

            // RoleController
            ['controller' => 'RoleController', 'method' => 'index', 'permissions' => ['read roles']],
            ['controller' => 'RoleController', 'method' => 'create', 'permissions' => ['create roles']],
            ['controller' => 'RoleController', 'method' => 'store', 'permissions' => ['create roles']],
            ['controller' => 'RoleController', 'method' => 'show', 'permissions' => ['show roles']],
            ['controller' => 'RoleController', 'method' => 'edit', 'permissions' => ['edit roles']],
            ['controller' => 'RoleController', 'method' => 'update', 'permissions' => ['edit roles']],
            ['controller' => 'RoleController', 'method' => 'destroy', 'permissions' => ['delete roles']],

            // RolesApiController
            ['controller' => 'RolesApiController', 'method' => 'index', 'permissions' => ['read roles']],
            ['controller' => 'RolesApiController', 'method' => 'store', 'permissions' => ['create roles']],
            ['controller' => 'RolesApiController', 'method' => 'show', 'permissions' => ['show roles']],
            ['controller' => 'RolesApiController', 'method' => 'update', 'permissions' => ['edit roles']],
            ['controller' => 'RolesApiController', 'method' => 'destroy', 'permissions' => ['delete roles']],

            // ControllerPermissionController
            ['controller' => 'ControllerPermissionController', 'method' => 'index', 'permissions' => ['read controller-permissions']],
            ['controller' => 'ControllerPermissionController', 'method' => 'create', 'permissions' => ['create controller-permissions']],
            ['controller' => 'ControllerPermissionController', 'method' => 'store', 'permissions' => ['create controller-permissions']],
            ['controller' => 'ControllerPermissionController', 'method' => 'show', 'permissions' => ['show controller-permissions']],
            ['controller' => 'ControllerPermissionController', 'method' => 'edit', 'permissions' => ['edit controller-permissions']],
            ['controller' => 'ControllerPermissionController', 'method' => 'update', 'permissions' => ['edit controller-permissions']],
            ['controller' => 'ControllerPermissionController', 'method' => 'destroy', 'permissions' => ['delete controller-permissions']],

            // PermissionController
            ['controller' => 'PermissionController', 'method' => 'index', 'permissions' => ['read permissions']],
            ['controller' => 'PermissionController', 'method' => 'create', 'permissions' => ['create permissions']],
            ['controller' => 'PermissionController', 'method' => 'store', 'permissions' => ['create permissions']],
            ['controller' => 'PermissionController', 'method' => 'show', 'permissions' => ['show permissions']],
            ['controller' => 'PermissionController', 'method' => 'edit', 'permissions' => ['edit permissions']],
            ['controller' => 'PermissionController', 'method' => 'update', 'permissions' => ['edit permissions']],
            ['controller' => 'PermissionController', 'method' => 'destroy', 'permissions' => ['delete permissions']],

            // PermissionsApiController
            ['controller' => 'PermissionsApiController', 'method' => 'index', 'permissions' => ['read permissions']],
            ['controller' => 'PermissionsApiController', 'method' => 'store', 'permissions' => ['create permissions']],
            ['controller' => 'PermissionsApiController', 'method' => 'show', 'permissions' => ['show permissions']],
            ['controller' => 'PermissionsApiController', 'method' => 'update', 'permissions' => ['edit permissions']],
            ['controller' => 'PermissionsApiController', 'method' => 'destroy', 'permissions' => ['delete permissions']],

            // MenuController
            ['controller' => 'MenuController', 'method' => 'index', 'permissions' => ['read menus']],
            ['controller' => 'MenuController', 'method' => 'create', 'permissions' => ['create menus']],
            ['controller' => 'MenuController', 'method' => 'store', 'permissions' => ['create menus']],
            ['controller' => 'MenuController', 'method' => 'show', 'permissions' => ['show menus']],
            ['controller' => 'MenuController', 'method' => 'edit', 'permissions' => ['edit menus']],
            ['controller' => 'MenuController', 'method' => 'update', 'permissions' => ['edit menus']],
            ['controller' => 'MenuController', 'method' => 'destroy', 'permissions' => ['delete menus']],

            // MenuApiController
            ['controller' => 'MenuApiController', 'method' => 'index', 'permissions' => ['read menus']],
            ['controller' => 'MenuApiController', 'method' => 'store', 'permissions' => ['create menus']],
            ['controller' => 'MenuApiController', 'method' => 'show', 'permissions' => ['show menus']],
            ['controller' => 'MenuApiController', 'method' => 'update', 'permissions' => ['edit menus']],
            ['controller' => 'MenuApiController', 'method' => 'destroy', 'permissions' => ['delete menus']],

            // SettingsController
            ['controller' => 'SettingsController', 'method' => 'index', 'permissions' => ['read settings']],
            ['controller' => 'SettingsController', 'method' => 'create', 'permissions' => ['create settings']],
            ['controller' => 'SettingsController', 'method' => 'store', 'permissions' => ['create settings']],
            ['controller' => 'SettingsController', 'method' => 'show', 'permissions' => ['show settings']],
            ['controller' => 'SettingsController', 'method' => 'edit', 'permissions' => ['edit settings']],
            ['controller' => 'SettingsController', 'method' => 'update', 'permissions' => ['edit settings']],
            ['controller' => 'SettingsController', 'method' => 'destroy', 'permissions' => ['delete settings']],

            // MediaController (Web)
            ['controller' => 'MediaController', 'method' => 'index', 'permissions' => ['read media']],
            ['controller' => 'MediaController', 'method' => 'show', 'permissions' => ['show media']],
            ['controller' => 'MediaController', 'method' => 'getModal', 'permissions' => ['read media']],

            // MediaController (API)
            ['controller' => 'MediaController', 'method' => 'uploadFile', 'permissions' => ['create media']],
            ['controller' => 'MediaController', 'method' => 'uploadAvatar', 'permissions' => ['create media']],
            ['controller' => 'MediaController', 'method' => 'deleteAvatar', 'permissions' => ['delete media']],

            // MediaManagementController
            ['controller' => 'MediaManagementController', 'method' => 'json', 'permissions' => ['read media']],
            ['controller' => 'MediaManagementController', 'method' => 'update', 'permissions' => ['edit media']],
            ['controller' => 'MediaManagementController', 'method' => 'destroy', 'permissions' => ['delete media']],
            ['controller' => 'MediaManagementController', 'method' => 'deleteMultiple', 'permissions' => ['delete media']],

            // MediaOperationsController
            ['controller' => 'MediaOperationsController', 'method' => 'copy', 'permissions' => ['edit media']],
            ['controller' => 'MediaOperationsController', 'method' => 'move', 'permissions' => ['edit media']],
            ['controller' => 'MediaOperationsController', 'method' => 'deleteFolder', 'permissions' => ['delete media']],

            // FolderController
            ['controller' => 'FolderController', 'method' => 'index', 'permissions' => ['read media']],
            ['controller' => 'FolderController', 'method' => 'store', 'permissions' => ['create media']],
            ['controller' => 'FolderController', 'method' => 'move', 'permissions' => ['edit media']],
            ['controller' => 'FolderController', 'method' => 'rename', 'permissions' => ['edit media']],

            // BackupController
            ['controller' => 'BackupController', 'method' => 'index', 'permissions' => ['read backup']],
            ['controller' => 'BackupController', 'method' => 'create', 'permissions' => ['create backup']],
            ['controller' => 'BackupController', 'method' => 'store', 'permissions' => ['create backup']],
            ['controller' => 'BackupController', 'method' => 'show', 'permissions' => ['show backup']],
            ['controller' => 'BackupController', 'method' => 'destroy', 'permissions' => ['delete backup']],

            // BackupApiController
            ['controller' => 'BackupApiController', 'method' => 'index', 'permissions' => ['read backup']],
            ['controller' => 'BackupApiController', 'method' => 'store', 'permissions' => ['create backup']],
            ['controller' => 'BackupApiController', 'method' => 'destroy', 'permissions' => ['delete backup']],
            ['controller' => 'BackupApiController', 'method' => 'download', 'permissions' => ['read backup']],
            ['controller' => 'BackupApiController', 'method' => 'counts', 'permissions' => ['read backup']],

            // MenuBadgeController
            ['controller' => 'MenuBadgeController', 'method' => 'getBadgeCount', 'permissions' => ['read badge']],
            ['controller' => 'MenuBadgeController', 'method' => 'getAllBadgeCounts', 'permissions' => ['read badge']],
            ['controller' => 'MenuBadgeController', 'method' => 'clearBadgeCache', 'permissions' => ['edit badge']],

            // MenuBadgeConfigController
            ['controller' => 'MenuBadgeConfigController', 'method' => 'index', 'permissions' => ['read badge']],
            ['controller' => 'MenuBadgeConfigController', 'method' => 'create', 'permissions' => ['create badge']],
            ['controller' => 'MenuBadgeConfigController', 'method' => 'store', 'permissions' => ['create badge']],
            ['controller' => 'MenuBadgeConfigController', 'method' => 'show', 'permissions' => ['show badge']],
            ['controller' => 'MenuBadgeConfigController', 'method' => 'edit', 'permissions' => ['edit badge']],
            ['controller' => 'MenuBadgeConfigController', 'method' => 'update', 'permissions' => ['edit badge']],
            ['controller' => 'MenuBadgeConfigController', 'method' => 'destroy', 'permissions' => ['delete badge']],

            // MenuBadgeConfigApiController
            ['controller' => 'MenuBadgeConfigApiController', 'method' => 'index', 'permissions' => ['read badge']],
            ['controller' => 'MenuBadgeConfigApiController', 'method' => 'store', 'permissions' => ['create badge']],
            ['controller' => 'MenuBadgeConfigApiController', 'method' => 'show', 'permissions' => ['show badge']],
            ['controller' => 'MenuBadgeConfigApiController', 'method' => 'update', 'permissions' => ['edit badge']],
            ['controller' => 'MenuBadgeConfigApiController', 'method' => 'destroy', 'permissions' => ['delete badge']],
            ['controller' => 'MenuBadgeConfigApiController', 'method' => 'deleteMultiple', 'permissions' => ['delete badge']],
            ['controller' => 'MenuBadgeConfigApiController', 'method' => 'json', 'permissions' => ['read badge']],
            ['controller' => 'MenuBadgeConfigApiController', 'method' => 'getAvailableModels', 'permissions' => ['read badge']],
            ['controller' => 'MenuBadgeConfigApiController', 'method' => 'getModelFields', 'permissions' => ['read badge']],
        ];

        foreach ($mappings as $mapping) {
            ControllerPermission::updateOrCreate(
                [
                    'controller' => $mapping['controller'],
                    'method' => $mapping['method'],
                ],
                [
                    'permissions' => $mapping['permissions'],
                    'is_active' => true,
                ]
            );
        }
    }
}
