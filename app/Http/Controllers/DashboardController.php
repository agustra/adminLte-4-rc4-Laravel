<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Menu;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'roles' => Role::count(),
            'permissions' => Permission::count(),
            'menus' => Menu::count(),
        ];

        return view('dashboard.index', compact('stats'));
    }
}