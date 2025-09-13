<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuBadgeConfig;
use App\Traits\ApiResponse;
use App\Traits\HandleErrors;
use App\Traits\HasApiJson;
use App\Traits\HasDynamicPermissions;
use App\Traits\HasQueryBuilder;
use App\Traits\ModernTableHelper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuBadgeConfigApiController extends Controller
{
    use ApiResponse, HandleErrors, HasApiJson, HasDynamicPermissions, HasQueryBuilder, ModernTableHelper;
    use AuthorizesRequests;

    protected $authorizeAction = 'badge-configs';

    protected $tableName = 'menu_badge_configs';

    protected array $tableSearchable = ['mbc.menu_url', 'mbc.model_class', 'mbc.description'];

    protected array $shortDefaultTable = ['mbc.created_at', 'desc'];

    // Untuk HasApiJson trait
    protected array $jsonColumns = ['id', 'menu_url', 'model_class', 'date_field'];

    protected array $jsonSearchable = ['menu_url', 'model_class', 'description', 'date_field'];

    protected function getValidationRules($id = null)
    {
        return [
            'menu_url' => 'required|string|max:255|unique:menu_badge_configs,menu_url,' . $id,
            'model_class' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) {
                if (! class_exists($value)) {
                    $fail('Model class does not exist: ' . $value);
                }
                if (! is_subclass_of($value, \Illuminate\Database\Eloquent\Model::class)) {
                    $fail('Class must be an Eloquent Model: ' . $value);
                }
            }],
            'date_fields' => 'required|array|min:1',
            'date_fields.*' => ['required', 'string', 'regex:/^[a-zA-Z_][a-zA-Z0-9_]*$/'],
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function index(Request $request)
    {
        try {
            $this->authorize('read ' . $this->authorizeAction, 'web');

            return $this->handleModernTableRequest($request, [
                'table' => $this->tableName,
                'alias' => 'mbc',
                'select' => [
                    'mbc.id',
                    'mbc.menu_url',
                    'mbc.model_class',
                    'mbc.date_field',
                    'mbc.is_active',
                    'mbc.description',
                    'mbc.created_at',
                ],
                'searchable' => $this->tableSearchable,
                'sortable' => [
                    'id' => 'mbc.id',
                    'menu_url' => 'mbc.menu_url',
                    'model_class' => 'mbc.model_class',
                    'date_field' => 'mbc.date_field',
                    'is_active' => 'mbc.is_active',
                    'created_at' => 'mbc.created_at',
                ],
                'default_sort' => 'mbc.id',
                'default_dir' => 'desc',
                'filterable' => [
                    'date' => 'mbc.created_at',
                    'year' => 'mbc.created_at',
                    'month' => 'mbc.created_at',
                    'start_date' => function ($query, $value) {
                        $query->whereDate('mbc.created_at', '>=', $value);
                    },
                    'end_date' => function ($query, $value) {
                        $query->whereDate('mbc.created_at', '<=', $value);
                    },
                ],
                'actions' => true,
                'action_permissions' => [
                    'edit' => 'edit',
                    'delete' => 'delete',
                    'show' => 'show',
                ],
                'action_routes' => [
                    'edit' => 'badge-configs.edit',
                    'delete' => 'api.badge-configs.destroy',
                    'show' => 'badge-configs.show',
                ],
                'meta' => [
                    'permissions' => $this->generatePermissions(),
                    'api_version' => 'v1',
                    'timestamp' => now()->toISOString(),
                ]
            ]);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $this->authorize('create ' . $this->authorizeAction, 'web');

            // Debug log
            \Log::info('Badge Config Store Request:', [
                'all_data' => $request->all(),
                'date_fields' => $request->get('date_fields'),
                'date_fields_type' => gettype($request->get('date_fields'))
            ]);

            $validated = $request->validate($this->getValidationRules());

            // Convert date_fields array to comma-separated string for storage
            if (isset($validated['date_fields'])) {
                $validated['date_field'] = implode(',', $validated['date_fields']);
                unset($validated['date_fields']);
            }

            DB::beginTransaction();
            $config = MenuBadgeConfig::create($validated);
            DB::commit();

            return $this->createdResponse($config, 'Badge configuration berhasil dibuat');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $config = MenuBadgeConfig::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'results' => $config,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->authorize('edit ' . $this->authorizeAction, 'web');

            $config = MenuBadgeConfig::findOrFail($id);
            $validated = $request->validate($this->getValidationRules($id));

            // Convert date_fields array to comma-separated string for storage
            if (isset($validated['date_fields'])) {
                $validated['date_field'] = implode(',', $validated['date_fields']);
                unset($validated['date_fields']);
            }

            DB::beginTransaction();
            $config->update($validated);
            DB::commit();

            return $this->updatedResponse($config, 'Badge configuration berhasil diperbarui');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        $this->authorize('delete ' . $this->authorizeAction, 'web');

        return $this->singleDelete($id);
    }

    public function getAvailableModels()
    {
        return response()->json([
            'status' => 'success',
            'models' => MenuBadgeConfig::getAvailableModels(),
        ]);
    }

    public function getModelFields(Request $request)
    {
        $modelClass = $request->get('model');

        if (! $modelClass || ! class_exists($modelClass)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid model class',
            ], 400);
        }

        try {
            $model = new $modelClass;
            $table = $model->getTable();
            $fields = \Schema::getColumnListing($table);

            // Filter to date/timestamp fields
            $dateFields = array_filter($fields, function ($field) {
                return in_array($field, ['created_at', 'updated_at']) ||
                    str_contains($field, 'date') ||
                    str_contains($field, 'time');
            });

            return response()->json([
                'status' => 'success',
                'fields' => array_values($dateFields),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error getting model fields: ' . $e->getMessage(),
            ], 500);
        }
    }
}
