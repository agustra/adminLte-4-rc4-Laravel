<?php

namespace App\Http\Controllers\Api\V1\FileManager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserMonitoringController extends Controller
{
    private $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('public');
    }

    /**
     * Get all users with their file statistics
     */
    public function index(Request $request)
    {
        $users = User::select('id', 'name', 'email', 'created_at')->get();
        $userStats = [];

        foreach ($users as $user) {
            $folderName = $this->getUserFolderName($user);
            $stats = $this->getUserFileStats($folderName);
            
            $userStats[] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'folder_name' => $folderName,
                'created_at' => $user->created_at,
                'files' => $stats,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $userStats,
            'summary' => $this->getGlobalStats($userStats),
        ]);
    }

    /**
     * Get specific user's files
     */
    public function show(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $folderName = $this->getUserFolderName($user);
        $type = $request->get('type', 'all'); // images, files, all
        $path = $request->get('path', ''); // subfolder

        $items = [];
        $paths = [];

        // Build paths
        if ($type === 'all' || $type === 'images') {
            $paths[] = [
                'category' => 'images',
                'path' => "filemanager/images/{$folderName}" . ($path ? "/{$path}" : '')
            ];
        }
        if ($type === 'all' || $type === 'files') {
            $paths[] = [
                'category' => 'files', 
                'path' => "filemanager/files/{$folderName}" . ($path ? "/{$path}" : '')
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
                        'size' => null,
                        'modified' => $this->disk->lastModified($folder),
                    ];
                }
                
                // Get files
                $files = $this->disk->files($pathInfo['path']);
                foreach ($files as $file) {
                    $items[] = [
                        'type' => 'file',
                        'name' => basename($file),
                        'path' => $file,
                        'url' => Storage::url($file),
                        'category' => $pathInfo['category'],
                        'size' => $this->disk->size($file),
                        'mime_type' => $this->disk->mimeType($file),
                        'modified' => $this->disk->lastModified($file),
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $items,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'folder_name' => $folderName,
            ],
            'current_path' => $path,
        ]);
    }

    /**
     * Delete user's file (admin action)
     */
    public function deleteFile(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'file_path' => 'required|string',
            'type' => 'required|in:file,folder',
        ]);

        $user = User::findOrFail($request->user_id);
        $folderName = $this->getUserFolderName($user);
        
        // Validate path belongs to user
        if (!$this->validateUserPath($request->file_path, $folderName)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file path for this user'
            ], 403);
        }

        if (!$this->disk->exists($request->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        if ($request->type === 'folder') {
            $this->disk->deleteDirectory($request->file_path);
        } else {
            $this->disk->delete($request->file_path);
        }

        return response()->json([
            'success' => true,
            'message' => 'File deleted successfully',
            'user' => $user->name,
        ]);
    }

    /**
     * Get storage usage by user
     */
    public function storageUsage()
    {
        $users = User::select('id', 'name', 'email')->get();
        $usage = [];

        foreach ($users as $user) {
            $folderName = $this->getUserFolderName($user);
            $stats = $this->getUserFileStats($folderName);
            
            $usage[] = [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'total_size' => $stats['total_size'],
                'total_files' => $stats['total_files'],
                'images_count' => $stats['images']['count'],
                'images_size' => $stats['images']['size'],
                'files_count' => $stats['files']['count'],
                'files_size' => $stats['files']['size'],
            ];
        }

        // Sort by total size descending
        usort($usage, fn($a, $b) => $b['total_size'] <=> $a['total_size']);

        return response()->json([
            'success' => true,
            'data' => $usage,
            'total_users' => count($usage),
            'total_storage' => array_sum(array_column($usage, 'total_size')),
        ]);
    }

    /**
     * Clean up empty folders
     */
    public function cleanup()
    {
        $cleaned = [];
        $users = User::all();

        foreach ($users as $user) {
            $folderName = $this->getUserFolderName($user);
            
            $folders = [
                "filemanager/images/{$folderName}",
                "filemanager/files/{$folderName}",
            ];

            foreach ($folders as $folder) {
                if ($this->disk->exists($folder)) {
                    $this->cleanEmptyFolders($folder, $cleaned);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cleanup completed',
            'cleaned_folders' => $cleaned,
        ]);
    }

    private function getUserFolderName($user): string
    {
        $folderName = strtolower(str_replace(' ', '-', $user->name));
        return preg_replace('/[^a-z0-9\-]/', '', $folderName);
    }

    private function getUserFileStats($folderName): array
    {
        $stats = [
            'images' => ['count' => 0, 'size' => 0],
            'files' => ['count' => 0, 'size' => 0],
            'total_files' => 0,
            'total_size' => 0,
        ];

        $categories = ['images', 'files'];
        
        foreach ($categories as $category) {
            $path = "filemanager/{$category}/{$folderName}";
            
            if ($this->disk->exists($path)) {
                $files = $this->disk->allFiles($path);
                $stats[$category]['count'] = count($files);
                
                foreach ($files as $file) {
                    $size = $this->disk->size($file);
                    $stats[$category]['size'] += $size;
                }
            }
        }

        $stats['total_files'] = $stats['images']['count'] + $stats['files']['count'];
        $stats['total_size'] = $stats['images']['size'] + $stats['files']['size'];

        return $stats;
    }

    private function validateUserPath($path, $folderName): bool
    {
        return str_starts_with($path, "filemanager/images/{$folderName}") ||
               str_starts_with($path, "filemanager/files/{$folderName}");
    }

    private function getGlobalStats($userStats): array
    {
        $totalUsers = count($userStats);
        $totalFiles = array_sum(array_column(array_column($userStats, 'files'), 'total_files'));
        $totalSize = array_sum(array_column(array_column($userStats, 'files'), 'total_size'));

        return [
            'total_users' => $totalUsers,
            'total_files' => $totalFiles,
            'total_size' => $totalSize,
            'average_files_per_user' => $totalUsers > 0 ? round($totalFiles / $totalUsers, 2) : 0,
            'average_size_per_user' => $totalUsers > 0 ? round($totalSize / $totalUsers, 2) : 0,
        ];
    }

    private function cleanEmptyFolders($path, &$cleaned): void
    {
        $directories = $this->disk->directories($path);
        
        foreach ($directories as $dir) {
            $this->cleanEmptyFolders($dir, $cleaned);
            
            // Check if directory is empty after recursive cleanup
            if (empty($this->disk->files($dir)) && empty($this->disk->directories($dir))) {
                $this->disk->deleteDirectory($dir);
                $cleaned[] = $dir;
            }
        }
    }
}