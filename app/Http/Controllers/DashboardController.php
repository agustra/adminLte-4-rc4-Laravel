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
        $user = auth()->user();
        
        // Super Admin gets full dashboard
        if ($user->hasRole('Super Admin')) {
            return $this->superAdminDashboard();
        }
        
        // All other roles get simple dashboard
        return $this->simpleDashboard();
    }
    
    private function superAdminDashboard()
    {
        $stats = [
            'users' => User::count(),
            'roles' => Role::count(),
            'permissions' => Permission::count(),
            'menus' => Menu::count(),
        ];

        return view('dashboard.index', compact('stats'));
    }
    
    private function simpleDashboard()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first() ?? 'User';
        
        return view('dashboard.simple', compact('user', 'role'));
    }
}