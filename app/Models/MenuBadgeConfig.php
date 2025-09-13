<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuBadgeConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_url',
        'model_class',
        'date_field',
        'is_active',
        'description',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get available models for badge configuration (auto-discovery)
     */
    public static function getAvailableModels()
    {
        $models = [];

        // Auto-discover App\Models
        $appModelsPath = app_path('Models');
        if (is_dir($appModelsPath)) {
            $files = glob($appModelsPath.'/*.php');
            foreach ($files as $file) {
                $className = 'App\\Models\\'.basename($file, '.php');
                if (class_exists($className) && is_subclass_of($className, Model::class)) {
                    $modelName = basename($file, '.php');
                    $models[$className] = $modelName;
                }
            }
        }

        // Add Spatie Permission models
        if (class_exists('Spatie\\Permission\\Models\\Role')) {
            $models['Spatie\\Permission\\Models\\Role'] = 'Roles';
        }
        if (class_exists('Spatie\\Permission\\Models\\Permission')) {
            $models['Spatie\\Permission\\Models\\Permission'] = 'Permissions';
        }

        // Sort by model name
        asort($models);

        return $models;
    }
}
