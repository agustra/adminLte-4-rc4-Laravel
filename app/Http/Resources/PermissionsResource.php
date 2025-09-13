<?php

namespace App\Http\Resources;

use App\Traits\HasActionButtons;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionsResource extends JsonResource
{
    use HasActionButtons;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'guard_name' => $this->guard_name,
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->pluck('name')->join(', ');
            }, 'No roles assigned'),
            'roles_count' => $this->when(isset($this->roles_count), $this->roles_count),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'action' => $this->getActionButtons(),
        ];
    }

    protected function getActionPermissions(): array
    {
        return [
            'show' => 'show permissions',
            'edit' => 'edit permissions',
            'delete' => 'delete permissions',
        ];
    }
}
