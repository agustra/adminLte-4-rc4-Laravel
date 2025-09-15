<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasPermissions, HasRoles, Notifiable;

    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        // 'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get avatar URL - FileManager only
     */
    public function getAvatarUrlAttribute(): string
    {
        // Check profile_photo_path column
        if ($this->profile_photo_path) {
            // Check if it's already a full URL
            if (filter_var($this->profile_photo_path, FILTER_VALIDATE_URL)) {
                return $this->profile_photo_path;
            }

            // Handle paths that already start with /storage/
            if (str_starts_with($this->profile_photo_path, '/storage/')) {
                return url($this->profile_photo_path);
            }

            // Use storage path for FileManager integration
            return asset('storage/' . $this->profile_photo_path);
        }

        // Finally fallback to default
        return asset('storage/filemanager/images/public/avatar-default.webp');
    }

    /**
     * Get avatar attribute - alias for avatar_url
     */
    public function getAvatarAttribute(): string
    {
        return $this->avatar_url;
    }
}
