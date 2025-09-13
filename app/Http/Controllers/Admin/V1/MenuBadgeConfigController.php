<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Controller;
use App\Models\MenuBadgeConfig;
use App\Traits\HandleErrors;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Permission\Models\Permission;

class MenuBadgeConfigController extends Controller
{
    use AuthorizesRequests, HandleErrors;

    protected $authorizeAction = 'badge-configs';

    public function index()
    {
        try {
            $this->authorize('read ' . $this->authorizeAction, 'web');

            return view('admin.badge-configs.index');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function create()
    {
        try {
            $this->authorize('create ' . $this->authorizeAction, 'web');

            $config = new MenuBadgeConfig;
            $availableModels = MenuBadgeConfig::getAvailableModels();
            $permissions = Permission::all();

            return view('admin.badge-configs.Form', compact('config', 'availableModels', 'permissions'));
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function edit($id)
    {
        try {
            $this->authorize('edit ' . $this->authorizeAction, 'web');

            $config = MenuBadgeConfig::findOrFail($id);
            $availableModels = MenuBadgeConfig::getAvailableModels();
            $permissions = Permission::all();

            return view('admin.badge-configs.Form', compact('config', 'availableModels', 'permissions'));
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $this->authorize('show ' . $this->authorizeAction, 'web');

            $config = MenuBadgeConfig::findOrFail($id);

            return view('admin.badge-configs.Show', compact('config'));
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }
}
