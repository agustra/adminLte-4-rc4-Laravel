<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Traits\HandleErrors;
use App\Traits\HasDynamicPermissions;
use App\Traits\HasQueryBuilder;
use App\Traits\TableHelpers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    use ApiResponse, HandleErrors, HasDynamicPermissions, HasQueryBuilder, TableHelpers;
    use AuthorizesRequests;

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'path' => 'required|string',
            'parent' => 'nullable|string',
        ]);

        try {
            $folderPath = 'media/'.$request->path;

            // Create folder in public/media
            $publicPath = public_path($folderPath);

            if (! file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Folder created successfully',
                'data' => [
                    'name' => $request->name,
                    'path' => $request->path,
                    'parent' => $request->parent,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create folder: '.$e->getMessage(),
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $folders = $this->getAllFolders();

            return response()->json([
                'status' => 'success',
                'data' => $folders,
            ]);
        } catch (\Exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load folders',
            ], 500);
        }
    }

    /**
     * @return mixed[]
     */
    private function getAllFolders(string $basePath = '', string $prefix = ''): array
    {
        $fullPath = public_path('media/'.$basePath);
        $folders = [];

        if (! is_dir($fullPath)) {
            return $folders;
        }

        $items = scandir($fullPath);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..' || $item === '.DS_Store') {
                continue;
            }

            $itemPath = $fullPath.'/'.$item;
            if (is_dir($itemPath)) {
                $folderPath = $basePath !== '' && $basePath !== '0' ? $basePath.'/'.$item : $item;
                $count = $this->countFolderItems($itemPath);

                $folders[] = [
                    'name' => $item,
                    'path' => $folderPath,
                    'count' => $count,
                ];

                // Recursively get subfolders
                $subfolders = $this->getAllFolders($folderPath, $prefix.'  ');
                $folders = array_merge($folders, $subfolders);
            }
        }

        return $folders;
    }

    private function countFolderItems(string $folderPath): int
    {
        if (! is_dir($folderPath)) {
            return 0;
        }

        $items = scandir($folderPath);
        if (! $items) {
            return 0;
        }

        $count = 0;
        foreach ($items as $item) {
            if ($item !== '.' && $item !== '..' && $item !== '.DS_Store') {
                $count++;
            }
        }

        return $count;
    }

    public function move(Request $request)
    {
        $request->validate([
            'source' => 'required|string',
            'target' => 'nullable|string',
        ]);

        try {
            $sourcePath = public_path('media/'.$request->source);
            $targetPath = public_path('media/'.($request->target ? $request->target.'/' : '').basename($request->source));

            if (! is_dir($sourcePath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Source folder not found',
                ], 404);
            }

            // Create target directory if not exists
            $targetDir = dirname($targetPath);
            if (! is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Move folder
            if (rename($sourcePath, $targetPath)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Folder moved successfully',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to move folder',
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to move folder: '.$e->getMessage(),
            ], 500);
        }
    }

    public function rename(Request $request)
    {
        $request->validate([
            'oldPath' => 'required|string',
            'newName' => 'required|string|max:255',
        ]);

        try {
            $oldPath = public_path('media/'.$request->oldPath);
            $parentDir = dirname($oldPath);
            $newPath = $parentDir.'/'.$request->newName;

            if (! is_dir($oldPath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Folder not found',
                ], 404);
            }

            if (is_dir($newPath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Folder with this name already exists',
                ], 400);
            }

            if (rename($oldPath, $newPath)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Folder renamed successfully',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to rename folder',
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to rename folder: '.$e->getMessage(),
            ], 500);
        }
    }
}
