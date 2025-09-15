<?php

namespace App\Http\Controllers\Api\V1\FileManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserFileManagerController extends Controller
{
    private $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('public');
    }

    /**
     * Get user's folder name
     */
    private function getUserFolder(): string
    {
        $user = auth()->user();
        $folderName = strtolower(str_replace(' ', '-', $user->name));
        return preg_replace('/[^a-z0-9\-]/', '', $folderName);
    }

    /**
     * Validate user can only access their own folder
     */
    private function validateUserPath(string $path): bool
    {
        $userFolder = $this->getUserFolder();
        $allowedPaths = [
            "filemanager/images/{$userFolder}",
            "filemanager/files/{$userFolder}",
        ];

        foreach ($allowedPaths as $allowedPath) {
            if (str_starts_with($path, $allowedPath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * List user's files and folders only
     */
    public function index(Request $request)
    {
        $userFolder = $this->getUserFolder();
        $type = $request->get('type', 'all'); // images, files, all
        $subPath = $request->get('path', ''); // subfolder within user's folder
        
        // Build paths for user's folders
        $paths = [];
        if ($type === 'all' || $type === 'images') {
            $paths[] = "filemanager/images/{$userFolder}" . ($subPath ? "/{$subPath}" : '');
        }
        if ($type === 'all' || $type === 'files') {
            $paths[] = "filemanager/files/{$userFolder}" . ($subPath ? "/{$subPath}" : '');
        }

        $items = [];
        
        foreach ($paths as $path) {
            if (!$this->disk->exists($path)) {
                // Create user folder if doesn't exist
                $this->disk->makeDirectory($path);
            }

            // Get folders
            $folders = $this->disk->directories($path);
            foreach ($folders as $folder) {
                $items[] = [
                    'type' => 'folder',
                    'name' => basename($folder),
                    'path' => $folder,
                    'url' => null,
                    'size' => null,
                    'modified' => $this->disk->lastModified($folder),
                    'category' => str_contains($folder, '/images/') ? 'images' : 'files',
                ];
            }
            
            // Get files
            $files = $this->disk->files($path);
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
                    'category' => str_contains($file, '/images/') ? 'images' : 'files',
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $items,
            'user_folder' => $userFolder,
            'available_paths' => [
                'images' => "filemanager/images/{$userFolder}",
                'files' => "filemanager/files/{$userFolder}",
            ],
        ]);
    }

    /**
     * Upload file to user's folder
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240', // 10MB
            'category' => 'required|in:images,files',
            'subfolder' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userFolder = $this->getUserFolder();
        $category = $request->get('category', 'files');
        $subfolder = $request->get('subfolder', '');
        
        $basePath = "filemanager/{$category}/{$userFolder}";
        $uploadPath = $basePath . ($subfolder ? "/{$subfolder}" : '');
        
        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs($uploadPath, $filename, 'public');
        
        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully',
            'data' => [
                'name' => $filename,
                'path' => $filePath,
                'url' => Storage::url($filePath),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'category' => $category,
            ]
        ]);
    }

    /**
     * Create folder in user's directory
     */
    public function createFolder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|in:images,files',
            'parent_path' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $userFolder = $this->getUserFolder();
        $category = $request->category;
        $parentPath = $request->get('parent_path', '');
        
        $basePath = "filemanager/{$category}/{$userFolder}";
        $folderPath = $basePath . ($parentPath ? "/{$parentPath}" : '') . "/{$request->name}";
        
        if ($this->disk->exists($folderPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Folder already exists'
            ], 409);
        }

        $this->disk->makeDirectory($folderPath);
        
        return response()->json([
            'success' => true,
            'message' => 'Folder created successfully',
            'data' => [
                'name' => $request->name,
                'path' => $folderPath,
                'category' => $category,
            ]
        ]);
    }

    /**
     * Delete file or folder (user's only)
     */
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'path' => 'required|string',
            'type' => 'required|in:file,folder',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate user can only delete their own files
        if (!$this->validateUserPath($request->path)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied: You can only delete your own files'
            ], 403);
        }

        if (!$this->disk->exists($request->path)) {
            return response()->json([
                'success' => false,
                'message' => 'File or folder not found'
            ], 404);
        }

        if ($request->type === 'folder') {
            $this->disk->deleteDirectory($request->path);
        } else {
            $this->disk->delete($request->path);
        }

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->type) . ' deleted successfully'
        ]);
    }

    /**
     * Rename file or folder (user's only)
     */
    public function rename(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_path' => 'required|string',
            'new_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate user can only rename their own files
        if (!$this->validateUserPath($request->old_path)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied: You can only rename your own files'
            ], 403);
        }

        if (!$this->disk->exists($request->old_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File or folder not found'
            ], 404);
        }

        $newPath = dirname($request->old_path) . '/' . $request->new_name;
        
        if ($this->disk->exists($newPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Name already exists'
            ], 409);
        }

        $this->disk->move($request->old_path, $newPath);

        return response()->json([
            'success' => true,
            'message' => 'Renamed successfully',
            'data' => [
                'old_path' => $request->old_path,
                'new_path' => $newPath,
            ]
        ]);
    }
}