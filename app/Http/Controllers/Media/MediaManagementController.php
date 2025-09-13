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
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagementController extends Controller
{
    use ApiResponse, HandleErrors, HasDynamicPermissions, HasQueryBuilder, TableHelpers;
    use AuthorizesRequests;

    public function json(Request $request)
    {
        try {
            // $this->authorize('read media'); // Temporary disable for testing

            $search = $request->get('search', '');
            $folder = $request->get('folder', '');
            $size = max(1, (int) $request->get('size', 10));
            $offset = max(0, (int) $request->get('offset', 0));

            $query = Media::with('model')
                ->when($folder, function ($q) use ($folder): void {
                    $q->where('collection_name', $folder);
                }, function ($q): void {
                    // If no folder specified, show only files without collection (root)
                    $q->where(function ($query): void {
                        $query->whereNull('collection_name')
                            ->orWhere('collection_name', '');
                    });
                })
                ->when($search, function ($q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('file_name', 'like', "%{$search}%")
                        ->orWhere('collection_name', 'like', "%{$search}%");
                })
                ->orderBy('created_at', 'desc');

            $total = $query->count();
            $data = $query->skip($offset)->take($size)->get();

            $formattedData = $data->map(fn ($media): array => [
                'id' => $media->id,
                'name' => $media->name,
                'file_name' => $media->file_name,
                'collection' => $media->collection_name,
                'mime_type' => $media->mime_type,
                'size' => $this->formatFileSize($media->size),
                'model_type' => class_basename($media->model_type),
                'model_id' => $media->model_id,
                'url' => $media->getUrl(),
                'created_at' => $media->created_at->format('d M Y H:i'),
                'action' => $this->getActionButtons($media),
            ]);

            // Get folders for current directory
            $folders = $this->getFolders($folder);

            // Get all media data for global search (only when in root folder)
            $allMediaData = [];
            if (! $folder) {
                $allMedia = Media::with('model')
                    ->orderBy('created_at', 'desc')
                    ->get();

                $allMediaData = $allMedia->map(fn ($media): array => [
                    'id' => $media->id,
                    'name' => $media->name,
                    'file_name' => $media->file_name,
                    'collection' => $media->collection_name,
                    'mime_type' => $media->mime_type,
                    'size' => $this->formatFileSize($media->size),
                    'model_type' => class_basename($media->model_type),
                    'model_id' => $media->model_id,
                    'url' => $media->getUrl(),
                    'created_at' => $media->created_at->format('d M Y H:i'),
                    'action' => $this->getActionButtons($media),
                ]);
            }

            return response()->json([
                'data' => $formattedData,
                'folders' => $folders,
                'all_data' => $allMediaData, // For global search
                'meta' => [
                    'total' => $total,
                    'size' => $size,
                    'currentPage' => (int) floor($offset / $size) + 1,
                    'offset' => $offset,
                    'sort' => [
                        'column' => 'created_at',
                        'dir' => 'desc',
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Media Management API Error: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
                'data' => [],
                'meta' => ['total' => 0, 'size' => $size, 'offset' => $offset],
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // $this->authorize('delete media'); // Temporary disable for testing
            
            $media = Media::findOrFail($id);
            
            // Check if media is being used by a model
            $model = $media->model;
            if ($model && $model->exists) {
                // For avatar collection, check if this is the currently active avatar
                if ($media->collection_name === 'avatars') {
                    // Check if this media is the current avatar (most recent in avatars collection for this user)
                    $currentAvatar = $model->getFirstMedia('avatars');
                    if ($currentAvatar && $currentAvatar->id === $media->id) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Cannot delete the current active avatar. Please upload a new avatar first before deleting this one.'
                        ], 400);
                    }
                    // If it's not the current avatar, allow deletion (old avatar)
                }
                
                // For default collection, always protect
                if ($media->collection_name === 'default') {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Cannot delete default media files.'
                    ], 400);
                }
                
                // For other collections, check if model still exists and is using this media
                // You can add more specific checks here based on your needs
            }
            
            // Try to delete the media
            $deleted = $media->delete();
            
            if (!$deleted) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to delete media. It may be protected or in use.'
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Media berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Media delete error', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // $this->authorize('edit media'); // Temporary disable for testing

            // Check if media exists first
            $media = Media::find($id);
            if (! $media) {
                Log::warning('Media not found for update', [
                    'requested_id' => $id,
                    'existing_media_ids' => Media::pluck('id')->toArray(),
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => "Media dengan ID {$id} tidak ditemukan. Media mungkin sudah dihapus.",
                ], 404);
            }

            // Log original data
            Log::info('Media update - Original data:', [
                'id' => $media->id,
                'name' => $media->name,
                'collection_name' => $media->collection_name,
                'custom_properties' => $media->custom_properties,
            ]);

            // Log request data
            Log::info('Media update - Request data:', $request->all());

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'collection_name' => 'sometimes|nullable|string|max:255',
                'custom_properties' => 'sometimes|nullable|array',
                'custom_properties.alt' => 'sometimes|nullable|string|max:500',
                'custom_properties.description' => 'sometimes|nullable|string|max:1000',
            ]);

            // Update name
            if (isset($validated['name'])) {
                $media->name = $validated['name'];
            }

            // Update collection - IMPORTANT: Don't change collection_name unless explicitly requested
            // The frontend sends collection_name but we should keep the original collection
            // Only update if it's different and intentional

            // Update custom properties
            if (isset($validated['custom_properties'])) {
                $currentProperties = $media->custom_properties ?? [];
                $newProperties = $validated['custom_properties'];

                // Merge properties, allowing null values to clear fields
                $media->custom_properties = array_merge($currentProperties, $newProperties);
            }

            $media->save();

            // Log updated data
            Log::info('Media update - Updated data:', [
                'id' => $media->id,
                'name' => $media->name,
                'collection_name' => $media->collection_name,
                'custom_properties' => $media->custom_properties,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Media berhasil diupdate',
                'data' => [
                    'id' => $media->id,
                    'name' => $media->name,
                    'collection_name' => $media->collection_name,
                    'custom_properties' => $media->custom_properties,
                    'updated_at' => $media->updated_at,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Media update error: '.$e->getMessage(), [
                'media_id' => $id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengupdate media: '.$e->getMessage(),
            ], 500);
        }
    }

    public function deleteMultiple(Request $request)
    {
        try {
            // $this->authorize('delete media'); // Temporary disable for testing

            $ids = array_filter($request->ids, 'is_numeric');
            if ($ids === []) {
                throw new \Exception('Tidak ada data yang valid untuk dihapus.');
            }

            Media::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => 'success',
                'message' => count($ids).' media berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    private function formatFileSize($bytes): string
    {
        if ($bytes === 0) {
            return '0 B';
        }
        $k = 1024;
        $sizes = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));

        return round($bytes / $k ** $i, 2).' '.$sizes[$i];
    }

    private function getActionButtons(Media $media): string
    {
        $buttons = [];

        $buttons[] = '<a href="'.$media->getUrl().'" target="_blank" class="btn btn-default btn-xs" title="Lihat File">
                        <i class="fa fa-eye text-info"></i>
                      </a>';

        if (Gate::allows('delete media')) {
            $buttons[] = '<button type="button" class="btn btn-default btn-xs btn-delete" title="Hapus Media" data-id="'.$media->id.'">
                            <i class="fa fa-trash-alt text-danger"></i>
                          </button>';
        }

        return implode(' ', $buttons);
    }

    private function getFolders(?string $currentFolder = '')
    {
        try {
            $basePath = public_path('media/'.$currentFolder);
            $folders = [];

            if (is_dir($basePath)) {
                $items = scandir($basePath);

                foreach ($items as $item) {
                    if ($item === '.' || $item === '..') {
                        continue;
                    }

                    $itemPath = $basePath.'/'.$item;

                    if (is_dir($itemPath)) {
                        $folderPath = $currentFolder ? $currentFolder.'/'.$item : $item;

                        // Count media files in database for this collection
                        $count = Media::where('collection_name', $folderPath)->count();

                        $folders[] = [
                            'name' => $item,
                            'path' => $folderPath,
                            'count' => $count,
                        ];
                    }
                }
            }

            return $folders;
        } catch (\Exception) {
            return [];
        }
    }
}
