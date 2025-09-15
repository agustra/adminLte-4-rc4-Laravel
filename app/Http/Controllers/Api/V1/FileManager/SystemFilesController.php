<?php

namespace App\Http\Controllers\Api\V1\FileManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SystemFilesController extends Controller
{
    private $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('public');
    }

    /**
     * Get all system files (public folder)
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'all'); // images, files, all
        $path = $request->get('path', ''); // subfolder within public
        
        $items = [];
        $paths = [];

        // Build paths for public system folders
        if ($type === 'all' || $type === 'images') {
            $paths[] = [
                'category' => 'images',
                'path' => 'filemanager/images/public' . ($path ? "/{$path}" : '')
            ];
        }
        if ($type === 'all' || $type === 'files') {
            $paths[] = [
                'category' => 'files',
                'path' => 'filemanager/files/public' . ($path ? "/{$path}" : '')
            ];
        }

        foreach ($paths as $pathInfo) {
            if ($this->disk->exists($pathInfo['path'])) {
                // Get folders
                $folders = $this->disk->directories($pathInfo['path']);
                foreach ($folders as $folder) {
                    $items[] = [
                        'type' => 'folder',
                        'name' => basename($folder),
                        'path' => $folder,
                        'category' => $pathInfo['category'],
                        'url' => null,
                        'size' => null,
                        'modified' => $this->disk->lastModified($folder),
                        'is_system' => true,
                    ];
                }
                
                // Get files
                $files = $this->disk->files($pathInfo['path']);
                foreach ($files as $file) {
                    $mimeType = $this->disk->mimeType($file);
                    
                    $items[] = [
                        'type' => 'file',
                        'name' => basename($file),
                        'path' => $file,
                        'url' => Storage::url($file),
                        'category' => $pathInfo['category'],
                        'size' => $this->disk->size($file),
                        'mime_type' => $mimeType,
                        'modified' => $this->disk->lastModified($file),
                        'is_system' => true,
                    ];
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $items,
            'current_path' => $path,
            'available_paths' => [
                'images' => 'filemanager/images/public',
                'files' => 'filemanager/files/public',
            ],
            'type' => 'system_files',
        ]);
    }

    /**
     * Get system images only
     */
    public function images(Request $request)
    {
        $path = $request->get('path', '');
        $fullPath = 'filemanager/images/public' . ($path ? "/{$path}" : '');
        
        $items = [];
        
        if ($this->disk->exists($fullPath)) {
            // Get folders
            $folders = $this->disk->directories($fullPath);
            foreach ($folders as $folder) {
                $items[] = [
                    'type' => 'folder',
                    'name' => basename($folder),
                    'path' => $folder,
                    'url' => null,
                    'size' => null,
                    'modified' => $this->disk->lastModified($folder),
                ];
            }
            
            // Get image files only
            $files = $this->disk->files($fullPath);
            foreach ($files as $file) {
                $mimeType = $this->disk->mimeType($file);
                
                // Only include image files
                if (str_starts_with($mimeType, 'image/')) {
                    $items[] = [
                        'type' => 'file',
                        'name' => basename($file),
                        'path' => $file,
                        'url' => Storage::url($file),
                        'size' => $this->disk->size($file),
                        'mime_type' => $mimeType,
                        'modified' => $this->disk->lastModified($file),
                        'is_image' => true,
                    ];
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $items,
            'current_path' => $path,
            'base_path' => 'filemanager/images/public',
            'type' => 'system_images',
        ]);
    }

    /**
     * Get system files only (non-images)
     */
    public function files(Request $request)
    {
        $path = $request->get('path', '');
        $fullPath = 'filemanager/files/public' . ($path ? "/{$path}" : '');
        
        $items = [];
        
        if ($this->disk->exists($fullPath)) {
            // Get folders
            $folders = $this->disk->directories($fullPath);
            foreach ($folders as $folder) {
                $items[] = [
                    'type' => 'folder',
                    'name' => basename($folder),
                    'path' => $folder,
                    'url' => null,
                    'size' => null,
                    'modified' => $this->disk->lastModified($folder),
                ];
            }
            
            // Get all files
            $files = $this->disk->files($fullPath);
            foreach ($files as $file) {
                $mimeType = $this->disk->mimeType($file);
                
                $items[] = [
                    'type' => 'file',
                    'name' => basename($file),
                    'path' => $file,
                    'url' => Storage::url($file),
                    'size' => $this->disk->size($file),
                    'mime_type' => $mimeType,
                    'modified' => $this->disk->lastModified($file),
                    'is_document' => !str_starts_with($mimeType, 'image/'),
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $items,
            'current_path' => $path,
            'base_path' => 'filemanager/files/public',
            'type' => 'system_files',
        ]);
    }

    /**
     * Get system file info by path
     */
    public function show(Request $request)
    {
        $path = $request->get('path');
        
        if (!$path || !$this->isSystemPath($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid system file path'
            ], 400);
        }

        if (!$this->disk->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        $mimeType = $this->disk->mimeType($path);
        
        return response()->json([
            'success' => true,
            'data' => [
                'name' => basename($path),
                'path' => $path,
                'url' => Storage::url($path),
                'size' => $this->disk->size($path),
                'mime_type' => $mimeType,
                'modified' => $this->disk->lastModified($path),
                'is_image' => str_starts_with($mimeType, 'image/'),
                'is_system' => true,
            ]
        ]);
    }

    /**
     * Check if path is within system (public) folders
     */
    private function isSystemPath(string $path): bool
    {
        return str_starts_with($path, 'filemanager/images/public/') ||
               str_starts_with($path, 'filemanager/files/public/');
    }
}