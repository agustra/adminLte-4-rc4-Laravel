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
            
            // Handle status value for TomSelect
            $statusValue = '';
            
            // Handle date fields for select
            $currentFields = old('date_fields', ['created_at']);
            $dateFieldOptions = [
                'created_at' => 'created_at',
                'updated_at' => 'updated_at', 
                'deleted_at' => 'deleted_at',
                'published_at' => 'published_at',
                'start_date' => 'start_date',
                'end_date' => 'end_date',
                'date' => 'date',
                'birth_date' => 'birth_date',
                'hire_date' => 'hire_date'
            ];
            
            // Handle model class for TomSelect
            $modelClassValue = old('model_class', '');
            $modelClassOptions = [];
            foreach ($availableModels as $class => $name) {
                $modelClassOptions[$class] = "$name ($class)";
            }

            return view('admin.badge-configs.Form', compact('config', 'availableModels', 'permissions', 'statusValue', 'currentFields', 'dateFieldOptions', 'modelClassValue', 'modelClassOptions'));
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
            
            // Handle status value for TomSelect
            $statusValue = old('is_active');
            if ($statusValue === null && isset($config)) {
                $statusValue = $config->is_active ? 1 : 0;
            }
            $statusValue = $statusValue ?? 1;
            
            // Handle date fields for select
            $currentFields = old(
                'date_fields',
                $config->date_field
                    ? (is_array($config->date_field)
                        ? $config->date_field
                        : explode(',', $config->date_field))
                    : ['created_at']
            );
            $dateFieldOptions = [
                'created_at' => 'created_at',
                'updated_at' => 'updated_at', 
                'deleted_at' => 'deleted_at',
                'published_at' => 'published_at',
                'start_date' => 'start_date',
                'end_date' => 'end_date',
                'date' => 'date',
                'birth_date' => 'birth_date',
                'hire_date' => 'hire_date'
            ];
            
            // Handle model class for TomSelect
            $modelClassValue = old('model_class', $config->model_class ?? '');
            $modelClassOptions = [];
            foreach ($availableModels as $class => $name) {
                $modelClassOptions[$class] = "$name ($class)";
            }

            return view('admin.badge-configs.Form', compact('config', 'availableModels', 'permissions', 'statusValue', 'currentFields', 'dateFieldOptions', 'modelClassValue', 'modelClassOptions'));
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
