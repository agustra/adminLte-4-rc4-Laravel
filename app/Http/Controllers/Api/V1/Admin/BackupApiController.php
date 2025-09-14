<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use App\Traits\ApiResponse;
use App\Traits\HandleErrors;
use App\Traits\HasDynamicPermissions;
use App\Traits\HasQueryBuilder;
use App\Traits\ModernTableHelper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class BackupApiController extends Controller
{
    use ApiResponse, AuthorizesRequests, HandleErrors, HasDynamicPermissions, HasQueryBuilder, ModernTableHelper;

    protected $backupService;

    protected $authorizeAction = 'backup';

    protected $tableName = 'backups'; // For trait compatibility (not used for actual DB operations)

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    public function index(Request $request)
    {
        try {
            $this->authorize('read ' . $this->authorizeAction, 'web');

            $type = $request->get('type', 'local');

            // Get filter parameters
            $filters = [
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
                'month' => $request->get('month'),
                'year' => $request->get('year'),
            ];

            // Get backup data with filters
            $backups = $this->getBackupData($type, $filters);

            // Format response similar to TableHelpers
            $size = (int) $request->input('size', 10);
            $page = (int) $request->input('page', 1);
            $offset = ($page - 1) * $size;

            // Apply pagination
            $paginatedData = $backups->slice($offset, $size)->values();

            $responseTable = [
                'data' => $paginatedData,
                'meta' => [
                    'api_version' => 'v1',
                    'timestamp' => now()->toISOString(),
                    'per_page' => $size,
                    'page' => $page,
                    'total' => $backups->count(),
                    'size' => $size,
                    'current_page' => $page,
                    'offset' => $offset,
                    'permissions' => $this->generatePermissions(),
                    'filter' => [
                        'start_date' => $filters['start_date'] ?? null,
                        'end_date' => $filters['end_date'] ?? null,
                        'month' => $filters['month'] ?? null,
                        'year' => $filters['year'] ?? null,
                    ],
                    'sort' => [
                        'column' => 'created_at',
                        'dir' => 'desc',
                    ],
                    'timestamp' => now()->toISOString(),
                ],
            ];

            return response()->json($responseTable, 200, [
                'Content-Type' => 'application/json',
                'X-API-Version' => 'v1',
            ]);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    private function getBackupData($type, $filters = [])
    {
        return $this->backupService->getBackups($type, $filters);
    }

    public function store(Request $request)
    {
        try {
            $this->authorize('create ' . $this->authorizeAction, 'web');

            $saveToLocal = $request->input('save_to_local') === 'true' || $request->input('save_to_local') === true;
            $saveToGoogle = $request->input('save_to_google') === 'true' || $request->input('save_to_google') === true;

            $result = $this->backupService->create($saveToLocal, $saveToGoogle);

            return $this->createdResponse($result, $result['message']);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function show()
    {
        return false;
    }

    public function destroy($filename, Request $request)
    {
        $this->authorize('delete ' . $this->authorizeAction, 'web');

        try {
            $type = $request->get('type', 'local');
            $result = $this->backupService->deleteBackup($filename, $type);

            $statusCode = $result['status'] === 'success' ? 200 : 404;

            return response()->json($result, $statusCode);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $this->authorize('delete ' . $this->authorizeAction, 'web');

            $filenames = $request->input('ids', []);
            $type = $request->input('type', 'local');

            $result = $this->backupService->bulkDeleteBackups($filenames, $type);

            $statusCode = $result['status'] === 'success' ? 200 : 400;

            return response()->json($result, $statusCode);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function counts()
    {
        try {
            $this->authorize('read ' . $this->authorizeAction, 'web');

            $result = $this->backupService->getCounts();

            return response()->json($result, 200, [
                'Content-Type' => 'application/json'
            ]);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function download($filename, Request $request)
    {
        try {
            // Determine type from filename prefix or request
            if (str_starts_with($filename, 'local_')) {
                $type = 'local';
            } elseif (str_starts_with($filename, 'google_')) {
                $type = 'google';
            } else {
                $type = $request->get('type', 'local');
            }

            return $this->backupService->download($filename, $type);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }
}
