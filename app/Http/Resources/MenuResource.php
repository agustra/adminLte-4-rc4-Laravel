<?php

namespace App\Http\Resources;

use App\Traits\HasActionButtons;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    use HasActionButtons;

    public function toArray($request)
    {
        // Get permission name if permission exists
        $permissionName = '-';
        if ($this->permission) {
            $permission = \Spatie\Permission\Models\Permission::where('name', $this->permission)
                ->orWhere('id', $this->permission)
                ->first();
            $permissionName = $permission ? $permission->name : $this->permission;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'icon' => $this->icon,
            'badge' => $this->badge_text ? '<span class="badge badge-'.($this->badge_color ?: 'danger').'">'.$this->badge_text.'</span>' : '-',
            'permission' => $permissionName,
            'parent' => $this->parent ? $this->parent->name : '-',
            'order' => $this->order,
            'is_active' => $this->is_active ? 'Active' : 'Inactive',
            'action' => $this->getActionButtons(),
        ];
    }

    protected function getActionPermissions(): array
    {
        return [
            'show' => [
                'permission' => 'read menus',
                'guard' => 'web',
                'class' => 'btn btn-default btn-sm buttonShow',
                'icon' => 'fa fa-eye text-info',
                'title' => 'View Menu',
            ],
            'edit' => [
                'permission' => 'edit menus',
                'guard' => 'web',
                'class' => 'btn btn-default btn-sm buttonUpdate',
                'icon' => 'fas fa-edit text-primary',
                'title' => 'Edit Menu',
            ],
            'delete' => [
                'permission' => 'delete menus',
                'guard' => 'web',
                'class' => 'btn btn-default btn-sm btn-delete',
                'icon' => 'fa fa-trash-alt text-danger',
                'title' => 'Delete Menu',
            ],
        ];
    }
}
