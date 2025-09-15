<?php

namespace App\Http\Resources;

use App\Traits\HasActionButtons;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    use HasActionButtons;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => ucfirst($this->name),
            'email' => $this->email,
            'avatar_url' => $this->profile_photo_path
                ? (str_starts_with($this->profile_photo_path, 'http')
                    ? $this->profile_photo_path
                    : (str_starts_with($this->profile_photo_path, '/storage/')
                        ? url($this->profile_photo_path)
                        : asset('storage/'.$this->profile_photo_path)))
                : asset('storage/filemanager/images/public/avatar-default.webp'),
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->map(function ($role) {
                    return ['id' => $role->id, 'name' => $role->name];
                })->toArray();
            }, []),
            'permissions_count' => $this->getAllPermissions()->count(),
            'permissions' => $this->getAllPermissions()->pluck('name')->toArray(),
            'created_at' => $this->created_at?->translatedFormat('d F Y H:i'),
            'updated_at' => $this->updated_at?->translatedFormat('d F Y H:i'),
            'actions' => $this->getActionButtons(),
        ];
    }

    protected function getActionPermissions(): array
    {
        return [
            'show' => 'show users',
            'edit' => 'edit users',
            'delete' => 'delete users',
        ];
    }
}
