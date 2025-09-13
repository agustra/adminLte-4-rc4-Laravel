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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
{
    use ApiResponse, HandleErrors, HasDynamicPermissions, HasQueryBuilder, TableHelpers;
    use AuthorizesRequests;

    public function __construct(protected \App\Services\MediaService $mediaService) {}

    /**
     * Upload avatar untuk user
     */
    public function uploadAvatar(Request $request, $userId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = User::findOrFail($userId);

            $media = $this->mediaService->uploadAvatar($user, $request->file('avatar'));

            return response()->json([
                'status' => 'success',
                'message' => 'Avatar berhasil diupload',
                'data' => [
                    'media_id' => $media->id,
                    'url' => $media->getUrl(),
                    'name' => $media->name,
                    'size' => $media->size,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal upload avatar: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus avatar user
     */
    public function deleteAvatar($userId): JsonResponse
    {
        try {
            $user = User::findOrFail($userId);

            $this->mediaService->deleteFromCollection($user, 'avatar');

            return response()->json([
                'status' => 'success',
                'message' => 'Avatar berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal hapus avatar: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload file ke collection tertentu
     */
    public function uploadFile(Request $request): JsonResponse
    {
        // Debug upload info
        // \Log::info('Upload attempt:', [
        //     'has_file' => $request->hasFile('file'),
        //     'file_valid' => $request->hasFile('file') ? $request->file('file')->isValid() : false,
        //     'file_size' => $request->hasFile('file') ? $request->file('file')->getSize() : 0,
        //     'max_upload' => ini_get('upload_max_filesize'),
        //     'max_post' => ini_get('post_max_size'),
        //     'content_length' => $request->header('Content-Length')
        // ]);

        // Check if file was uploaded
        if (! $request->hasFile('file')) {
            $maxSize = ini_get('upload_max_filesize');

            return response()->json([
                'status' => 'error',
                'message' => "File tidak dapat diupload. Pastikan ukuran file tidak lebih dari {$maxSize}.",
                'user_message' => "Ukuran file terlalu besar. Maksimal {$maxSize} per file.",
            ], 422);
        }

        if (! $request->file('file')->isValid()) {
            $maxSize = ini_get('upload_max_filesize');
            $errorCode = $request->file('file')->getError();

            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => "File terlalu besar. Maksimal {$maxSize} per file.",
                UPLOAD_ERR_FORM_SIZE => 'File terlalu besar untuk form ini.',
                UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian. Coba lagi.',
                UPLOAD_ERR_NO_FILE => 'Tidak ada file yang dipilih.',
                UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak tersedia.',
                UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk.',
                UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi PHP.',
            ];

            $message = $errorMessages[$errorCode] ?? "Upload gagal. Coba pilih file lain atau periksa ukuran file (max {$maxSize}).";

            return response()->json([
                'status' => 'error',
                'message' => $message,
                'user_message' => $message,
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240',
            'collection' => 'nullable|string',
            'folder' => 'nullable|string',
            'replace_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Use current authenticated user
            $authUser = Auth::user();

            if (! $authUser) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated',
                ], 401);
            }

            // Cast to User model to satisfy HasMedia interface
            $user = User::find($authUser->id);
            $collection = $request->collection ?: '';
            $folder = $request->folder;

            // Check if replacing existing media (for crop functionality)
            if ($request->replace_id) {
                $existingMedia = \Spatie\MediaLibrary\MediaCollections\Models\Media::find($request->replace_id);
                if ($existingMedia) {
                    // Replace the file in existing media record
                    $existingMedia->delete();

                    // Use same collection as existing media
                    $collection = $existingMedia->collection_name;
                }
            }

            // Upload new file
            if ($folder) {
                $media = $this->mediaService->uploadToFolder($user, $request->file('file'), $folder);
            } elseif ($collection) {
                $media = $this->mediaService->uploadToFolder($user, $request->file('file'), $collection);
            } else {
                $media = $this->mediaService->uploadToCollection($user, $request->file('file'), '');
            }

            return response()->json([
                'status' => 'success',
                'message' => 'File berhasil diupload',
                'data' => [
                    'media_id' => $media->id,
                    'url' => $media->getUrl(),
                    'name' => $media->name,
                    'size' => $media->size,
                    'collection' => $media->collection_name,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal upload file: '.$e->getMessage(),
            ], 500);
        }
    }
}
