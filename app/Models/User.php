<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, HasPermissions, HasRoles, InteractsWithMedia, Notifiable;

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
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    /**
     * Get avatar URL - supports both profile_photo_path and Media Library
     */
    public function getAvatarUrlAttribute(): string
    {
        // First check Media Library
        $media = $this->getFirstMedia('avatar');
        if ($media) {
            return $media->getUrl();
        }

        // Then check profile_photo_path column
        if ($this->profile_photo_path) {
            // Check if it's already a full URL
            if (filter_var($this->profile_photo_path, FILTER_VALIDATE_URL)) {
                return $this->profile_photo_path;
            }

            $path = $this->normalizeAvatarPath($this->profile_photo_path);

            return url('/'.$path);
        }

        // Finally fallback to default
        return url('/media/avatars/avatar-default.webp');
    }

    /**
     * Normalize avatar path for consistent URL generation
     */
    private function normalizeAvatarPath(string $path): string
    {
        // Remove leading slash
        $path = ltrim($path, '/');

        // If path doesn't start with 'media/', add it
        if (! str_starts_with($path, 'media/')) {
            $path = 'media/'.$path;
        }

        return $path;
    }

    /**
     * Get avatar attribute - alias for avatar_url
     */
    public function getAvatarAttribute(): string
    {
        return $this->avatar_url;
    }

    /**
     * Get avatar media
     */
    public function getAvatarMedia()
    {
        return $this->getFirstMedia('avatar');
    }
}
