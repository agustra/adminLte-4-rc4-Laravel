<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasDynamicPermissions
{
    /**
     * Generate user permissions for current action
     */
    protected function generatePermissions(): array
    {
        if (! property_exists($this, 'authorizeAction') || ! Auth::check()) {
            return [];
        }

        $user = Auth::user();

        return [
            'create' => $user->can('create '.$this->authorizeAction),
            'read' => $user->can('read '.$this->authorizeAction),
            'edit' => $user->can('edit '.$this->authorizeAction),
            'delete' => $user->can('delete '.$this->authorizeAction),
        ];
    }

    /**
     * Get permissions untuk controller ini (legacy)
     */
    public function getControllerPermissions()
    {
        $controllerName = class_basename(static::class);

        return getControllerPermissions($controllerName);
    }

    /**
     * Inject permissions ke response data
     */
    protected function withPermissions($data = [])
    {
        $permissions = $this->generatePermissions();

        if (request()->expectsJson()) {
            return array_merge($data, [
                'meta' => array_merge($data['meta'] ?? [], [
                    'permissions' => $permissions,
                ]),
            ]);
        }

        return $data;
    }

    /**
     * Share permissions ke view
     */
    protected function sharePermissionsToView()
    {
        $permissions = $this->generatePermissions();
        view()->share('controllerPermissions', $permissions);

        // Untuk JavaScript
        $jsPermissions = json_encode($permissions);
        view()->share('jsPermissions', $jsPermissions);
    }
}
