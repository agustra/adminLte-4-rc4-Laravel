<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use App\Traits\HandleErrors;
use App\Traits\HasDynamicPermissions;
use App\Traits\HasQueryBuilder;
use App\Traits\TableHelpers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaOperationsController extends Controller
{
    use ApiResponse, HandleErrors, HasDynamicPermissions, HasQueryBuilder, TableHelpers;
    use AuthorizesRequests;

    public function copy(Request $request)
    {
        $request->validate([
            'media_id' => 'required|integer|exists:media,id',
            'target_folder' => 'nullable|string',
        ]);

        try {
            $media = Media::findOrFail($request->media_id);
            $user = $media->model ?: User::first();

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No user found',
                ], 500);
            }

            // Get original file path
            $originalPath = $media->getPath();

            if (! file_exists($originalPath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Original file not found at: '.$originalPath,
                ], 404);
            }

            // Copy file to target folder
            $targetCollection = $request->target_folder ?: '';
            $fileName = $media->file_name;

            // Calculate target path
            $targetPath = $targetCollection
                ? public_path('media/'.$targetCollection.'/'.$fileName)
                : public_path('media/'.$fileName);

            // Create target directory if needed
            $targetDir = dirname($targetPath);
            if (! is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // If file already exists, generate unique name
            if (file_exists($targetPath)) {
                $fileName = $this->generateUniqueFileName($media->file_name);
                $targetPath = $targetCollection
                    ? public_path('media/'.$targetCollection.'/'.$fileName)
                    : public_path('media/'.$fileName);
            }

            // Copy physical file
            if (! copy($originalPath, $targetPath)) {
                throw new \Exception('Failed to copy file from '.$originalPath.' to '.$targetPath);
            }

            // Create new media record
            $newMedia = new \Spatie\MediaLibrary\MediaCollections\Models\Media;
            $newMedia->model_type = $user::class;
            $newMedia->model_id = $user->id;
            $newMedia->collection_name = $targetCollection;
            $newMedia->name = $media->name.' (Copy)';
            $newMedia->file_name = $fileName;
            $newMedia->mime_type = $media->mime_type;
            $newMedia->disk = 'media';
            $newMedia->size = filesize($targetPath);
            $newMedia->manipulations = [];
            $newMedia->custom_properties = $media->custom_properties ?? [];
            $newMedia->generated_conversions = [];
            $newMedia->responsive_images = [];
            $newMedia->uuid = \Illuminate\Support\Str::uuid();
            $newMedia->order_column = \Spatie\MediaLibrary\MediaCollections\Models\Media::max('order_column') + 1;
            $newMedia->save();

            \Log::info('Media copied successfully', [
                'original_id' => $media->id,
                'new_id' => $newMedia->id,
                'from' => $originalPath,
                'to' => $targetPath,
                'target_collection' => $targetCollection,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Media copied successfully',
                'data' => $newMedia,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to copy media: '.$e->getMessage(),
            ], 500);
        }
    }

    public function move(Request $request)
    {
        $request->validate([
            'media_id' => 'required|integer|exists:media,id',
            'target_folder' => 'nullable|string',
        ]);

        try {
            $media = Media::findOrFail($request->media_id);
            $targetFolder = $request->target_folder ?: '';

            // Get current file path
            $currentPath = $media->getPath();

            if (! file_exists($currentPath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File not found at: '.$currentPath,
                ], 404);
            }

            // Calculate new path
            $fileName = $media->file_name;
            $newPath = $targetFolder
                ? public_path('media/'.$targetFolder.'/'.$fileName)
                : public_path('media/'.$fileName);

            // Create target directory if not exists
            $targetDir = dirname($newPath);
            if (! is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Calculate new path
            $fileName = $media->file_name;
            $newPath = $targetFolder
                ? public_path('media/'.$targetFolder.'/'.$fileName)
                : public_path('media/'.$fileName);

            // Create target directory if not exists
            $targetDir = dirname($newPath);
            if (! is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Move physical file
            if (rename($currentPath, $newPath)) {
                // Update database record
                $media->collection_name = $targetFolder;
                $media->save();

                \Log::info('Media moved successfully', [
                    'media_id' => $media->id,
                    'from' => $currentPath,
                    'to' => $newPath,
                    'new_collection' => $media->collection_name,
                ]);
            } else {
                throw new \Exception('Failed to move physical file from '.$currentPath.' to '.$newPath);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Media moved successfully',
                'data' => [
                    'old_path' => $currentPath,
                    'new_path' => $newPath,
                    'collection' => $targetFolder,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to move media: '.$e->getMessage(),
            ], 500);
        }
    }

    public function deleteFolder(Request $request)
    {
        $request->validate([
            'folder_path' => 'required|string',
        ]);

        try {
            $folderPath = $request->folder_path;
            $physicalPath = public_path('media/'.$folderPath);

            // Delete all media records in this folder
            $mediaInFolder = Media::where('collection_name', $folderPath)->get();
            foreach ($mediaInFolder as $media) {
                $media->delete();
            }

            // Delete physical folder and contents
            if (is_dir($physicalPath)) {
                $this->deleteDirectory($physicalPath);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Folder deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete folder: '.$e->getMessage(),
            ], 500);
        }
    }

    private function deleteDirectory(string $dir)
    {
        if (! is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir.'/'.$file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        return rmdir($dir);
    }

    private function generateUniqueFileName($originalName)
    {
        $pathInfo = pathinfo((string) $originalName);
        $name = $pathInfo['filename'];
        $extension = $pathInfo['extension'] ?? '';

        $counter = 1;
        $newName = $originalName;

        while (Media::where('file_name', $newName)->exists()) {
            $newName = $name.'_'.$counter.($extension ? '.'.$extension : '');
            $counter++;
        }

        return $newName;
    }
}
