<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Controller;
use App\Models\ControllerPermission;
use App\Traits\HasDynamicPermissions;
use Spatie\Permission\Models\Permission;

class ControllerPermissionController extends Controller
{
    use HasDynamicPermissions;

    protected $model = ControllerPermission::class;

    protected $authorizeAction = 'controller-permissions';

    public function index()
    {
        $this->sharePermissionsToView();

        return view('admin.controller-permissions.index');
    }

    public function create()
    {
        $results = new $this->model;
        $permissions = Permission::all();

        return view('admin.controller-permissions.form', compact('results', 'permissions'));
    }

    public function edit($id)
    {
        $results = $this->model::findOrFail($id);
        $permissions = Permission::all();

        return view('admin.controller-permissions.form', compact('results', 'permissions'));
    }

    public function show($id)
    {
        $results = $this->model::findOrFail($id);
        $permissions = Permission::all();

        return view('admin.controller-permissions.form', compact('results', 'permissions'));
    }
}
