<?php

namespace App\Models;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaConversion
{
    public static function registerConversions(Media $media): void
    {
        // Convert images to WebP format
        if (str_starts_with($media->mime_type, 'image/')) {
            $media->addMediaConversion('webp')
                ->format('webp')
                ->quality(85)
                ->nonQueued(); // Process immediately
        }
    }
}
