<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FileManagerPicsumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting FileManager Picsum Photos data generation...');
        
        // Create directories
        $this->createDirectories();
        
        // Download real images from Picsum
        $this->downloadPicsumImages();
        
        $this->command->info('✅ FileManager Picsum Photos data generated successfully!');
    }
    
    /**
     * Create directory structure
     */
    private function createDirectories(): void
    {
        $basePath = storage_path('app/public/filemanager');
        
        $directories = [
            'images/shares',
            'images/shares/thumbs',
            'images/shares/landscape',
            'images/shares/landscape/thumbs',
            'images/shares/portrait',
            'images/shares/portrait/thumbs',
            'images/shares/square',
            'images/shares/square/thumbs',
            'images/shares/nature',
            'images/shares/nature/thumbs',
            'images/shares/architecture',
            'images/shares/architecture/thumbs',
        ];
        
        foreach ($directories as $dir) {
            $fullPath = $basePath . '/' . $dir;
            if (!File::exists($fullPath)) {
                File::makeDirectory($fullPath, 0755, true);
                $this->command->info("📁 Created directory: {$dir}");
            }
        }
    }
    
    /**
     * Download images from Picsum Photos
     */
    private function downloadPicsumImages(): void
    {
        $categories = [
            'landscape' => ['count' => 40, 'sizes' => [
                ['width' => 1200, 'height' => 800],
                ['width' => 1000, 'height' => 600],
                ['width' => 800, 'height' => 500],
            ]],
            'portrait' => ['count' => 30, 'sizes' => [
                ['width' => 600, 'height' => 900],
                ['width' => 500, 'height' => 800],
                ['width' => 400, 'height' => 600],
            ]],
            'square' => ['count' => 30, 'sizes' => [
                ['width' => 600, 'height' => 600],
                ['width' => 500, 'height' => 500],
                ['width' => 400, 'height' => 400],
            ]],
            'nature' => ['count' => 50, 'sizes' => [
                ['width' => 1200, 'height' => 800],
                ['width' => 800, 'height' => 600],
                ['width' => 600, 'height' => 400],
            ]],
            'architecture' => ['count' => 50, 'sizes' => [
                ['width' => 1000, 'height' => 700],
                ['width' => 800, 'height' => 600],
                ['width' => 600, 'height' => 800],
            ]],
        ];
        
        foreach ($categories as $category => $config) {
            $this->command->info("🖼️  Downloading {$config['count']} images for {$category}...");
            
            for ($i = 1; $i <= $config['count']; $i++) {
                $size = $config['sizes'][array_rand($config['sizes'])];
                $this->downloadPicsumImage($category, $i, $size['width'], $size['height']);
                
                // Show progress
                if ($i % 10 == 0) {
                    $this->command->info("   📊 Progress: {$i}/{$config['count']} images downloaded for {$category}");
                }
                
                // Small delay to be respectful to the API
                usleep(200000); // 0.2 seconds
            }
        }
    }
    
    /**
     * Download single image from Picsum
     */
    private function downloadPicsumImage(string $category, int $index, int $width, int $height): void
    {
        try {
            // Generate random seed for consistent but varied images
            $seed = rand(1, 1000);
            
            // Picsum Photos URL with specific size
            $url = "https://picsum.photos/seed/{$category}{$seed}/{$width}/{$height}.jpg";
            
            $response = Http::timeout(30)->get($url);
            
            if ($response->successful()) {
                $filename = $this->generateFilename($category, $index, $width, $height);
                $imagePath = storage_path("app/public/filemanager/images/shares/{$category}/{$filename}");
                
                // Create category directory if not exists
                $categoryDir = dirname($imagePath);
                if (!File::exists($categoryDir)) {
                    File::makeDirectory($categoryDir, 0755, true);
                }
                
                File::put($imagePath, $response->body());
                
                // Generate thumbnail
                $this->generateThumbnail($imagePath, $category, $filename);
                
                $this->command->info("   ✅ Downloaded: {$filename} ({$width}x{$height})");
                
            } else {
                $this->command->warn("⚠️  Failed to download image {$index} for {$category}");
            }
            
        } catch (\Exception $e) {
            $this->command->error("❌ Error downloading image {$index} for {$category}: " . $e->getMessage());
        }
    }
    
    /**
     * Generate filename
     */
    private function generateFilename(string $category, int $index, int $width, int $height): string
    {
        $suffix = str_pad($index, 3, '0', STR_PAD_LEFT);
        return "{$category}_{$width}x{$height}_{$suffix}.jpg";
    }
    
    /**
     * Generate thumbnail
     */
    private function generateThumbnail(string $imagePath, string $category, string $filename): void
    {
        try {
            if (!File::exists($imagePath)) {
                return;
            }
            
            // Get image info
            $imageInfo = getimagesize($imagePath);
            if (!$imageInfo) {
                return;
            }
            
            // Create image resource based on type
            switch ($imageInfo[2]) {
                case IMAGETYPE_JPEG:
                    $sourceImage = imagecreatefromjpeg($imagePath);
                    break;
                case IMAGETYPE_PNG:
                    $sourceImage = imagecreatefrompng($imagePath);
                    break;
                case IMAGETYPE_GIF:
                    $sourceImage = imagecreatefromgif($imagePath);
                    break;
                default:
                    return;
            }
            
            if (!$sourceImage) {
                return;
            }
            
            $sourceWidth = imagesx($sourceImage);
            $sourceHeight = imagesy($sourceImage);
            
            // Thumbnail size
            $thumbWidth = 200;
            $thumbHeight = 200;
            
            // Calculate aspect ratio
            $ratio = min($thumbWidth / $sourceWidth, $thumbHeight / $sourceHeight);
            $newWidth = (int)($sourceWidth * $ratio);
            $newHeight = (int)($sourceHeight * $ratio);
            
            // Create thumbnail
            $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);
            $white = imagecolorallocate($thumbnail, 245, 245, 245);
            imagefill($thumbnail, 0, 0, $white);
            
            // Center the image
            $x = (int)(($thumbWidth - $newWidth) / 2);
            $y = (int)(($thumbHeight - $newHeight) / 2);
            
            imagecopyresampled(
                $thumbnail, $sourceImage,
                $x, $y, 0, 0,
                $newWidth, $newHeight,
                $sourceWidth, $sourceHeight
            );
            
            // Save thumbnail
            $thumbPath = storage_path("app/public/filemanager/images/shares/{$category}/thumbs/{$filename}");
            $thumbDir = dirname($thumbPath);
            
            if (!File::exists($thumbDir)) {
                File::makeDirectory($thumbDir, 0755, true);
            }
            
            imagejpeg($thumbnail, $thumbPath, 85);
            
            // Cleanup
            imagedestroy($sourceImage);
            imagedestroy($thumbnail);
            
        } catch (\Exception $e) {
            $this->command->warn("⚠️  Failed to generate thumbnail for {$filename}: " . $e->getMessage());
        }
    }
}