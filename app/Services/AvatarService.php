<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AvatarService
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

    private const AVATAR_QUALITY = 85;

    public function processAvatar(string $sourcePath): ?string
    {
        try {
            Log::info('Starting avatar processing', ['source' => $sourcePath]);

            if (! $this->validateFile($sourcePath)) {
                Log::warning('Avatar validation failed', ['source' => $sourcePath]);

                return null;
            }

            Log::info('Avatar validation passed');

            $filename = $this->generateSecureFilename();
            $destinationPath = public_path('media/avatars/'.$filename);

            Log::info('Generated filename', ['filename' => $filename, 'destination' => $destinationPath]);

            $this->ensureDirectoryExists();
            $this->convertToWebP($sourcePath, $destinationPath);

            Log::info('Avatar processing completed successfully', ['filename' => $filename]);

            return $filename;
        } catch (\Exception $e) {
            Log::error('Avatar processing failed', [
                'source' => $sourcePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    private function validateFile(string $path): bool
    {
        if (! file_exists($path)) {
            return false;
        }

        $fileSize = filesize($path);
        if ($fileSize > self::MAX_FILE_SIZE) {
            Log::warning('Avatar file too large', ['size' => $fileSize]);

            return false;
        }

        $imageInfo = getimagesize($path);
        if (! $imageInfo) {
            return false;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, self::ALLOWED_EXTENSIONS);
    }

    private function generateSecureFilename(): string
    {
        return 'avatar_'.time().'_'.bin2hex(random_bytes(8)).'.webp';
    }

    private function ensureDirectoryExists(): void
    {
        $dir = public_path('media/avatars');
        if (! is_dir($dir)) {
            if (! mkdir($dir, 0755, true)) {
                throw new \Exception('Failed to create avatars directory');
            }
        }
    }

    private function convertToWebP(string $sourcePath, string $destinationPath): void
    {
        if (! extension_loaded('gd')) {
            throw new \Exception('GD extension not available');
        }

        $imageInfo = getimagesize($sourcePath);
        $image = $this->createImageResource($sourcePath, $imageInfo['mime']);

        if (! $image) {
            throw new \Exception('Failed to create image resource');
        }

        try {
            $image = $this->ensureTrueColor($image);

            if (! imagewebp($image, $destinationPath, self::AVATAR_QUALITY)) {
                throw new \Exception('Failed to save WebP image');
            }
        } finally {
            if ($image) {
                imagedestroy($image);
            }
        }
    }

    private function createImageResource(string $path, string $mimeType)
    {
        switch ($mimeType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($path);
            case 'image/png':
                return imagecreatefrompng($path);
            case 'image/gif':
                return imagecreatefromgif($path);
            case 'image/webp':
                return imagecreatefromwebp($path);
            default:
                return null;
        }
    }

    private function ensureTrueColor($image)
    {
        if (imageistruecolor($image)) {
            return $image;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $truecolor = imagecreatetruecolor($width, $height);

        if (! $truecolor) {
            throw new \Exception('Failed to create truecolor image');
        }

        imagealphablending($truecolor, false);
        imagesavealpha($truecolor, true);
        $transparent = imagecolorallocatealpha($truecolor, 255, 255, 255, 127);
        imagefill($truecolor, 0, 0, $transparent);
        imagecopy($truecolor, $image, 0, 0, 0, 0, $width, $height);
        imagedestroy($image);

        return $truecolor;
    }

    public function deleteAvatar(string $filename): bool
    {
        try {
            $path = public_path('media/avatars/'.$filename);
            if (file_exists($path)) {
                return unlink($path);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Avatar deletion failed', [
                'filename' => $filename,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
