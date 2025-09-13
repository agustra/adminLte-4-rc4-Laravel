<?php

namespace App\Traits;

use BadMethodCallException;
use Error;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

trait HandleErrors
{
    protected function handleException(\Throwable $e)
    {
        // Rollback hanya jika ada transaksi
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        // Log exception for monitoring
        Log::error('API Exception', [
            'exception' => $e::class,
            'message' => $e->getMessage(),
            'file' => basename($e->getFile()),
            'line' => $e->getLine(),
            'userId' => Auth::user() ? Auth::user()->id : null,
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'userAgent' => request()->userAgent(),
            'trace' => config('app.debug') ? $e->getTraceAsString() : null,
        ]);

        // Validation Error
        if ($e instanceof ValidationException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->validator->errors(),
                'meta' => [
                    'error_code' => 'VALIDATION_ERROR',
                    'api_version' => 'v1',
                    'timestamp' => now()->toISOString(),
                ],
            ], 422);
        }

        // Authorization Error
        if ($e instanceof AuthorizationException) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to perform this action',
                'errors' => null,
                'meta' => [
                    'error_code' => 'FORBIDDEN',
                    'api_version' => 'v1',
                    'timestamp' => now()->toISOString(),
                ],
            ], 403);
        }

        // Model Not Found
        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Resource not found',
                'errors' => null,
                'meta' => [
                    'error_code' => 'NOT_FOUND',
                    'api_version' => 'v1',
                    'timestamp' => now()->toISOString(),
                ],
            ], 404);
        }

        // Database Query Error
        if ($e instanceof QueryException) {
            $response = [
                'status' => 'error',
                'message' => $e->getMessage(),
                'error_code' => 'DATABASE_ERROR',
                'timestamp' => now()->toISOString(),
            ];

            if (config('app.debug')) {
                // Tampilkan detail query dan kode error SQL untuk debugging
                $response['debug'] = [
                    'sql' => $e->getSql(),
                    'bindings' => $e->getBindings(),
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                ];
            }

            return response()->json($response, 500);
        }

        // Bad Method Call
        if ($e instanceof BadMethodCallException) {
            $response = [
                'status' => 'error',
                'message' => 'Method not available',
                'error_code' => 'BAD_METHOD_CALL',
                'timestamp' => now()->toISOString(),
            ];

            if (config('app.debug')) {
                $response['debug'] = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ];
            }

            return response()->json($response, 500);
        }

        // Native PHP Error (Class not found, syntax error, dsb)
        if ($e instanceof Error) {
            $response = [
                'status' => 'error',
                'message' => config('app.debug')
                    ? $e->getMessage()
                    : 'Internal server error',
                'error_code' => 'PHP_ERROR',
                'timestamp' => now()->toISOString(),
            ];

            if (config('app.debug')) {
                $response['debug'] = [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ];
            }

            return response()->json($response, 500);
        }

        // Generic Throwable
        return response()->json([
            'status' => 'error',
            'message' => config('app.debug')
                ? $e->getMessage()
                : 'An error occurred',
            'errors' => null,
            'meta' => [
                'error_code' => 'INTERNAL_SERVER_ERROR',
                'api_version' => 'v1',
                'timestamp' => now()->toISOString(),
            ],
        ], 500);
    }
}
