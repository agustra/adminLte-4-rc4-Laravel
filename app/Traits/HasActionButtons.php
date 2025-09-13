<?php

namespace App\Traits;

use Illuminate\Support\Facades\Gate;

trait HasActionButtons
{
    private static $buttonTemplates = [
        'show' => '<button class="btn btn-info btn-sm buttonShow" data-id="{id}" title="View"><i class="fa fa-eye"></i></button>',
        'edit' => '<button class="btn btn-warning btn-sm buttonUpdate" data-id="{id}" title="Edit"><i class="fa fa-edit"></i></button>',
        'delete' => '<button class="btn btn-danger btn-sm btn-delete" data-id="{id}" title="Delete"><i class="fa fa-trash"></i></button>',
    ];

    private static $permissionCache = [];

    protected function getActionButtons()
    {
        $permissions = $this->getActionPermissions();
        $buttons = [];
        $id = $this->resource->id ?? $this->id;

        foreach ($permissions as $action => $config) {
            if ($this->hasPermission($config)) {
                $buttons[] = $this->buildButton($id, $action, $config);
            }
        }

        return implode(' ', $buttons);
    }

    private function buildButton($id, $action, $config)
    {
        // Support both old format and new extended format
        if (is_array($config) && isset($config['permission'])) {
            // New extended format
            $class = $config['class'] ?? self::$buttonTemplates[$action]['class'] ?? 'btn btn-secondary btn-sm';
            $icon = $config['icon'] ?? self::$buttonTemplates[$action]['icon'] ?? 'fa fa-cog';
            $title = $config['title'] ?? ucfirst($action);

            return '<button class="'.$class.'" data-id="'.$id.'" title="'.$title.'"><i class="'.$icon.'"></i></button>';
        } else {
            // Old format - use templates
            return str_replace('{id}', $id, self::$buttonTemplates[$action]);
        }
    }

    private function hasPermission($config)
    {
        // Handle different config formats
        if (is_array($config)) {
            if (isset($config['permission'])) {
                // New extended format
                $permissionName = $config['permission'];
                $guard = $config['guard'] ?? 'web';
            } else {
                // Old array format [permission, guard]
                [$permissionName, $guard] = $config;
            }
            $cacheKey = $permissionName.'|'.$guard;
        } else {
            // String format
            $permissionName = $config;
            $guard = 'web';
            $cacheKey = $config;
        }

        if (! isset(self::$permissionCache[$cacheKey])) {
            self::$permissionCache[$cacheKey] = Gate::allows($permissionName, $guard);
        }

        return self::$permissionCache[$cacheKey];
    }

    // Override this method in each Resource
    abstract protected function getActionPermissions(): array;
}
