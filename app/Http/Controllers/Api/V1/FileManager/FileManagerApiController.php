<?php

namespace App\Http\Controllers\Api\V1\FileManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileManagerApiController extends Controller
{
    private $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('public');
    }

    /**
     * List files and folders
     */
    public function index(Request $request)
    {
        $path = $request->get('path', 'filemanager');
        $type = $request->get('type', 'all'); // images, files, all
        
        $items = [];
        
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
            ];
        }
        
        // Get files
        $files = $this->disk->files($path);
        foreach ($files as $file) {
            $mimeType = $this->disk->mimeType($file);
            
            // Filter by type if specified
            if ($type === 'images' && !str_starts_with($mimeType, 'image/')) continue;
            if ($type === 'files' && str_starts_with($mimeType, 'image/')) continue;
            
            $items[] = [
                'type' => 'file',
                'name' => basename($file),
                'path' => $file,
                'url' => Storage::url($file),
                'size' => $this->disk->size($file),
                'mime_type' => $mimeType,
                'modified' => $this->disk->lastModified($file),
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => $items,
            'current_path' => $path,
        ]);
    }

    /**
     * Upload file
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240', // 10MB
            'path' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $file = $request->file('file');
        $path = $request->get('path', 'filemanager/files/' . auth()->user()->username);
        
        $filename = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs($path, $filename, 'public');
        
        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully',
            'data' => [
                'name' => $filename,
                'path' => $filePath,
                'url' => Storage::url($filePath),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]
        ]);
    }

    /**
     * Create folder
     */
    public function createFolder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'path' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $folderPath = $request->path . '/' . $request->name;
        
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
            ]
        ]);
    }

    /**
     * Delete file or folder
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
     * Rename file or folder
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