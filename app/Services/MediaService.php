<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaService
{
    /**
     * Upload file ke media collection
     */
    public function uploadToCollection(HasMedia $model, UploadedFile $file, string $collection = ''): Media
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // Check if it's an image and convert to WebP
        if (str_starts_with((string) $file->getMimeType(), 'image/')) {
            // Convert image to WebP
            $webpPath = $this->convertToWebP($file);

            return $model->addMedia($webpPath)
                ->usingName($originalName)
                ->usingFileName($this->generateUniqueFileName($originalName.'.webp'))
                ->toMediaCollection($collection);
        }

        dd($originalName);

        // For non-images, keep original format
        return $model->addMedia($file)
            ->usingName($originalName)
            ->usingFileName($this->generateUniqueFileName($file->getClientOriginalName()))
            ->toMediaCollection($collection);
    }

    /**
     * Convert image to WebP format
     */
    private function convertToWebP(UploadedFile $file): string
    {
        $tempPath = sys_get_temp_dir().'/'.uniqid().'.webp';

        // Create image resource from uploaded file
        $image = null;
        switch ($file->getMimeType()) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($file->getPathname());
                break;
            case 'image/png':
                $image = imagecreatefrompng($file->getPathname());
                break;
            case 'image/gif':
                $image = imagecreatefromgif($file->getPathname());
                break;
            default:
                // If not supported, copy original file
                copy($file->getPathname(), $tempPath);

                return $tempPath;
        }

        if ($image) {
            // Convert palette to truecolor if needed
            if (! imageistruecolor($image)) {
                $width = imagesx($image);
                $height = imagesy($image);
                $truecolor = imagecreatetruecolor($width, $height);

                // Preserve transparency for PNG/GIF
                imagealphablending($truecolor, false);
                imagesavealpha($truecolor, true);
                $transparent = imagecolorallocatealpha($truecolor, 255, 255, 255, 127);
                imagefill($truecolor, 0, 0, $transparent);

                imagecopy($truecolor, $image, 0, 0, 0, 0, $width, $height);
                imagedestroy($image);
                $image = $truecolor;
            }

            // Convert to WebP with 85% quality
            imagewebp($image, $tempPath, 85);
            imagedestroy($image);
        }

        return $tempPath;
    }

    /**
     * Upload avatar untuk user
     */
    public function uploadAvatar(HasMedia $model, UploadedFile $file): Media
    {
        // Hapus avatar lama jika ada
        $model->clearMediaCollection('avatar');

        return $model->addMedia($file)
            ->usingName($model->name.' Avatar')
            ->usingFileName($this->generateFileName($file, 'avatar-'.$model->id))
            ->toMediaCollection('avatar');
    }

    /**
     * Generate nama file unik
     */
    private function generateFileName(UploadedFile $file, string $prefix = ''): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('YmdHis');

        return $prefix !== '' && $prefix !== '0' ? "{$prefix}-{$timestamp}.{$extension}" : "{$timestamp}.{$extension}";
    }

    /**
     * Generate unique filename to prevent conflicts
     */
    private function generateUniqueFileName(string $originalName): string
    {
        $pathInfo = pathinfo($originalName);
        $name = $pathInfo['filename'];
        $extension = $pathInfo['extension'] ?? '';

        $timestamp = now()->format('YmdHis');
        $random = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 6);

        return $name.'_'.$timestamp.'_'.$random.($extension ? '.'.$extension : '');
    }

    /**
     * Hapus media dari collection
     */
    public function deleteFromCollection(HasMedia $model, string $collection = 'default'): bool
    {
        $model->clearMediaCollection($collection);

        return true;
    }

    /**
     * Upload file ke folder tertentu di public/media
     */
    public function uploadToFolder(HasMedia $model, UploadedFile $file, string $folder): Media
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // Ensure folder exists
        $folderPath = public_path('media/'.$folder);
        if (! is_dir($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        // Check if it's an image and convert to WebP
        if (str_starts_with((string) $file->getMimeType(), 'image/')) {
            // Convert image to WebP
            $webpPath = $this->convertToWebP($file);

            return $model->addMedia($webpPath)
                ->usingName($originalName)
                ->usingFileName($this->generateUniqueFileName($originalName.'.webp'))
                ->toMediaCollection($folder, 'media');
        }

        // For non-images, keep original format
        return $model->addMedia($file)
            ->usingName($originalName)
            ->usingFileName($this->generateUniqueFileName($file->getClientOriginalName()))
            ->toMediaCollection($folder, 'media');
    }

    /**
     * Get media URL dengan fallback
     */
    public function getMediaUrl(HasMedia $model, string $collection = 'default', ?string $fallback = null): string
    {
        $media = $model->getFirstMedia($collection);

        if ($media) {
            return $media->getUrl();
        }

        return $fallback ?: asset('img/no-image-612x612.png');
    }
}
