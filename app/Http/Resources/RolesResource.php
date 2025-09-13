<?php

namespace App\Http\Resources;

use App\Traits\HasActionButtons;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class RolesResource extends JsonResource
{
    use HasActionButtons;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => ucfirst($this->name),
            'permissions' => $this->getAllPermissions()->pluck('name')->toArray(),
            'created_at' => $this->created_at->translatedFormat('d F Y H:i'),
            'updated_at' => $this->updated_at->translatedFormat('d F Y H:i'),
            'action' => $this->getActionButtons(),
        ];
        // 'action' => $this->getActionButtons() . $this->customActionButtons(), // Uncomment if you want to add custom action buttons
    }

    protected function getActionPermissions(): array
    {
        return [
            'show' => [
                'permission' => 'read roles',
                'guard' => 'web',
                'class' => 'btn btn-info btn-sm buttonShow',
                'icon' => 'fa fa-eye',
                'title' => 'View Role',
            ],
            'edit' => [
                'permission' => 'edit roles',
                'guard' => 'web',
                'class' => 'btn btn-warning btn-sm buttonUpdate',
                'icon' => 'fa fa-edit',
                'title' => 'Edit Role',
            ],
            'delete' => [
                'permission' => 'delete roles',
                'guard' => 'web',
                'class' => 'btn btn-danger btn-sm btn-delete',
                'icon' => 'fa fa-trash',
                'title' => 'Delete Role',
            ],
        ];
    }

    // ini untuk mengembalikan tombol aksi khusus untuk role uncomment jika diperlukan
    // private function customActionButtons()
    // {
    //     $buttons = [];

    //     if (Gate::allows('read roles', 'web')) {
    //         $buttons[] = '<button class="btn btn-success btn-sm btn-assign" data-id="' . $this->id . '" title="Assign Role">
    //                     <i class="fa fa-user-plus"></i>
    //                   </button>';
    //     }

    //     if (Gate::allows('read roles', 'web')) {
    //         $buttons[] = '<button class="btn btn-info btn-sm btn-duplicate" data-id="' . $this->id . '" title="Duplicate">
    //                     <i class="fa fa-copy"></i>
    //                   </button>';
    //     }

    //     return $buttons ? ' ' . implode(' ', $buttons) : '';
    // }

    // <button class="btn btn-secondary btn-sm-custom" onclick="assignOutlet(1)">
    //                     <i class="fas fa-building"></i> Assign
    //                 </button>
}
