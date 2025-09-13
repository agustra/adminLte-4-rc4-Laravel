<?php

use Illuminate\Support\Facades\Route;

if (! function_exists('addRouteIfNotSkipped')) {
    /**
     * Helper function for conditional routes
     *
     * @param  string  $routeType  Type of route (json, by-ids, bulkDelete)
     * @param  string  $name  Route name
     * @param  string  $controller  Controller class
     * @param  string  $method  Controller method
     * @param  array  $skipRoutes  Array of routes to skip
     */
    function addRouteIfNotSkipped($routeType, $name, $controller, $method, $skipRoutes)
    {
        if (! isset($skipRoutes[$name]) || ! in_array($routeType, $skipRoutes[$name])) {
            if ($routeType === 'json') {
                Route::get("$name/json", [$controller, $method])->name("$name.json");
            } elseif ($routeType === 'by-ids') {
                Route::get("$name/by-ids", [$controller, $method])->name("$name.byIds");
            } elseif ($routeType === 'bulkDelete') {
                Route::post("$name/multiple/delete", [$controller, $method])->name("$name.bulkDelete");
            }
        }
    }
}
