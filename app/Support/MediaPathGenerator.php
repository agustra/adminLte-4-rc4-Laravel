<?php

namespace App\Support;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class MediaPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        $basePath = $this->getBasePath($media);

        return $basePath !== '' && $basePath !== '0' ? $basePath.'/' : '';
    }

    public function getPathForFile(Media $media, string $fileName): string
    {
        $basePath = $this->getBasePath($media);

        return $basePath !== '' && $basePath !== '0' ? $basePath.'/'.$fileName : $fileName;
    }

    public function getPathForConversions(Media $media): string
    {
        $basePath = $this->getBasePath($media);

        return $basePath !== '' && $basePath !== '0' ? $basePath.'/conversions/' : 'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        $basePath = $this->getBasePath($media);

        return $basePath !== '' && $basePath !== '0' ? $basePath.'/responsive/' : 'responsive/';
    }

    protected function getBasePath(Media $media): string
    {
        $collection = $media->collection_name;

        // If no collection, store directly in root (public/media)
        // If has collection, store in subfolder
        return $collection ?: '';
    }
}
