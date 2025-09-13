<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Controller;
use App\Traits\HandleErrors;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    use AuthorizesRequests, HandleErrors;

    protected $authorizeAction = 'permissions';

    protected $model = Permission::class;

    public function index()
    {
        $this->authorize('read '.$this->authorizeAction, 'web');

        try {
            return view('admin.'.$this->authorizeAction.'.index');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function create()
    {
        try {
            $this->authorize('create '.$this->authorizeAction, 'web');
            $permission = new $this->model;
            $roles = Role::pluck('name', 'id');

            return view('admin.'.$this->authorizeAction.'.Form', compact('permission', 'roles'));
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function edit($id)
    {
        $this->authorize('edit '.$this->authorizeAction, 'web');
        try {
            $permission = $this->model::with(['roles'])->findOrFail($id);
            $roles = Role::pluck('name', 'id');

            return view('admin.'.$this->authorizeAction.'.Form', compact('permission', 'roles'));
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        $this->authorize('read '.$this->authorizeAction, 'web');
        try {
            $permission = $this->model::with(['roles'])->findOrFail($id);
            $roles = Role::get()->pluck('name', 'id');

            return view('admin.'.$this->authorizeAction.'.Show', compact('permission', 'roles'));
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
