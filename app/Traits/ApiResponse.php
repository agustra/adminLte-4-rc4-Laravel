<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

trait ApiResponse
{
    /**
     * Success response with data
     */
    protected function successResponse($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'meta' => [
                'api_version' => 'v1',
                'timestamp' => now()->toISOString(),
            ],
        ];

        // Add user permissions if available
        if (property_exists($this, 'authorizeAction') && Auth::check() && method_exists($this, 'generatePermissions')) {
            $response['meta']['permissions'] = $this->generatePermissions();
        }

        return response()->json($response, $code, [
            'Content-Type' => 'application/json',
            'X-API-Version' => 'v1',
        ]);
    }

    /**
     * Error response
     */
    protected function errorResponse(string $message, int $code = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
            'meta' => [
                'api_version' => 'v1',
                'timestamp' => now()->toISOString(),
            ],
        ], $code, [
            'Content-Type' => 'application/json',
            'X-API-Version' => 'v1',
        ]);
    }

    /**
     * Created response (201)
     */
    protected function createdResponse($data = null, string $message = 'Data berhasil dibuat'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Updated response (200)
     */
    protected function updatedResponse($data = null, string $message = 'Data berhasil diperbarui'): JsonResponse
    {
        return $this->successResponse($data, $message, 200);
    }

    /**
     * Deleted response (200)
     */
    protected function deletedResponse($data = null, string $message = 'Data berhasil dihapus'): JsonResponse
    {
        return $this->successResponse($data, $message, 200);
    }

    /**
     * Validation error response (422)
     */
    protected function validationErrorResponse($errors, string $message = 'Validation failed'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }

    /**
     * Not found response (404)
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Forbidden response (403)
     */
    protected function forbiddenResponse(string $message = 'You do not have permission to perform this action'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }
}
