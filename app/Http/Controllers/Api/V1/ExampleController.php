<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Traits\HandleErrors;
use App\Traits\HasDynamicPermissions;
use App\Traits\HasQueryBuilder;
use App\Traits\TableHelpers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    use ApiResponse, HandleErrors, HasDynamicPermissions, HasQueryBuilder, TableHelpers;
    use AuthorizesRequests;

    protected $authorizeAction = 'example';

    // Contoh penggunaan untuk tabel sederhana tanpa join
    public function simpleIndex(Request $request)
    {
        return $this->handleTableData($request, [
            'table' => 'users',
            'select' => ['id', 'name', 'email', 'created_at'],
            'searchable' => ['name', 'email'],
            'sortable' => [
                'id' => 'id',
                'name' => 'name',
                'email' => 'email',
                'created_at' => 'created_at',
            ],
            'action_routes' => [
                'edit' => 'users.edit',
                'delete' => 'api.users.destroy',
                'show' => 'users.show',
            ],
        ]);
    }

    // Contoh penggunaan untuk tabel dengan multiple joins
    public function complexIndex(Request $request)
    {
        return $this->handleTableData($request, [
            'table' => 'orders',
            'alias' => 'o',
            'joins' => [
                [
                    'type' => 'leftJoin',
                    'table' => 'customers as c',
                    'first' => 'c.id',
                    'second' => 'o.customer_id',
                ],
                [
                    'type' => 'leftJoin',
                    'table' => 'products as p',
                    'first' => 'p.id',
                    'second' => 'o.product_id',
                ],
            ],
            'select' => [
                'o.id',
                'c.name as customer',
                'p.name as product',
                'o.quantity',
                'o.total',
                'o.created_at',
            ],
            'searchable' => [
                'c.name',
                'p.name',
                'o.quantity',
            ],
            'sortable' => [
                'id' => 'o.id',
                'customer' => 'c.name',
                'product' => 'p.name',
                'quantity' => 'o.quantity',
                'total' => 'o.total',
                'created_at' => 'o.created_at',
            ],
            'filterable' => [
                'date' => 'o.created_at',
                'customer_id' => 'o.customer_id',
                'status' => 'o.status',
            ],
            'default_sort' => 'created_at',
            'default_size' => 25,
            'actions' => false, // Nonaktifkan actions jika tidak diperlukan
        ]);
    }
}
