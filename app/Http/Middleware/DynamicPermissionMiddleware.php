<?php

namespace App\Http\Middleware;

use App\Models\ControllerPermission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DynamicPermissionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $route = $request->route();
        if (! $route) {
            return $next($request);
        }

        $action = $route->getAction();
        if (! isset($action['controller'])) {
            return $next($request);
        }

        // Extract controller and method
        [$controller, $method] = explode('@', $action['controller']);
        $controllerName = class_basename($controller);

        // Get required permissions from database
        $controllerPermission = ControllerPermission::where('controller', $controllerName)
            ->where('method', $method)
            ->where('is_active', true)
            ->first();

        if ($controllerPermission && $controllerPermission->permissions) {
            $hasPermission = false;

            // Cek apakah user memiliki salah satu dari permissions yang diperlukan
            foreach ($controllerPermission->permissions as $permission) {
                if (Auth::user()->can($permission)) {
                    $hasPermission = true;
                    break;
                }
            }

            if (! $hasPermission) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Anda tidak memiliki izin untuk mengakses resource ini.',
                        'required_permissions' => $controllerPermission->permissions,
                    ], 403);
                }

                abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
            }
        }

        return $next($request);
    }
}
