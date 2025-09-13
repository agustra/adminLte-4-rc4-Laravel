# API Standards & Guidelines

## 📋 Response Format Standardization

### ✅ Success Response Format

```json
{
    "status": "success",
    "message": "Operation completed successfully",
    "data": {
        // Response data here
    },
    "meta": {
        "total": 100,
        "size": 10,
        "current_page": 1,
        "offset": 0,
        "permissions": {
            "create": true,
            "read": true,
            "edit": true,
            "delete": false
        },
        "api_version": "v1",
        "timestamp": "2024-01-01T00:00:00.000000Z"
    }
}
```

### ❌ Error Response Format

```json
{
    "status": "error",
    "message": "Error description",
    "errors": {
        // Validation errors or null
    },
    "meta": {
        "error_code": "VALIDATION_ERROR",
        "api_version": "v1",
        "timestamp": "2024-01-01T00:00:00.000000Z"
    }
}
```

## 🔧 Implementation

### 1. Use ApiResponse Trait

```php
use App\Traits\ApiResponse;

class YourController extends Controller
{
    use ApiResponse;
    
    public function store(Request $request)
    {
        // Success responses
        return $this->createdResponse($data, 'Data berhasil dibuat');
        return $this->successResponse($data, 'Success message');
        return $this->updatedResponse($data, 'Data berhasil diperbarui');
        return $this->deletedResponse($data, 'Data berhasil dihapus');
        
        // Error responses
        return $this->errorResponse('Error message', 400);
        return $this->validationErrorResponse($errors);
        return $this->notFoundResponse();
        return $this->forbiddenResponse();
    }
}
```

### 2. HTTP Status Codes

| Operation | Success Code | Error Codes |
|-----------|-------------|-------------|
| GET | 200 | 404, 403, 500 |
| POST | 201 | 422, 403, 500 |
| PUT/PATCH | 200 | 422, 404, 403, 500 |
| DELETE | 200 | 404, 403, 500 |

### 3. Form Request Validation

```php
// Create Form Request
php artisan make:request YourModelRequest

// Use in Controller
public function store(YourModelRequest $request)
{
    $validated = $request->validated();
    // Process data
}
```

### 4. Error Handling

All controllers should use `HandleErrors` trait:

```php
use App\Traits\HandleErrors;

try {
    // Your logic here
} catch (\Throwable $e) {
    return $this->handleException($e);
}
```

## 📊 Pagination Format

```json
{
    "data": [...],
    "meta": {
        "total": 100,
        "size": 10,
        "current_page": 1,
        "offset": 0,
        "sort": {
            "column": "id",
            "dir": "desc"
        },
        "permissions": {
            "create": true,
            "read": true,
            "edit": true,
            "delete": true
        },
        "api_version": "v1",
        "timestamp": "2025-09-07T00:39:16.941303Z"
    }
}
```

## 🔐 Authentication Headers

```
Authorization: Bearer {token}
Content-Type: application/json
X-API-Version: v1
```

## 📝 Naming Conventions

### Endpoints
- Use kebab-case: `/api/device-services`
- Use plural nouns: `/api/users`, `/api/roles`
- Use consistent patterns:
  - `GET /api/resource/json` - DataTable data
  - `POST /api/resource/multiple/delete` - Bulk delete
  - `GET /api/resource/by-ids` - Get by IDs

### Response Fields
- Use snake_case: `created_at`, `user_id`
- Be consistent with field names across endpoints
- Use descriptive names: `deleted_count` instead of `count`

## ✅ Checklist for New APIs

- [ ] Uses ApiResponse trait
- [ ] Has Form Request validation
- [ ] Uses HandleErrors trait
- [ ] Follows HTTP status code standards
- [ ] Has consistent response format
- [ ] Includes proper authorization
- [ ] Has API documentation
- [ ] Tested with Postman/similar tool

## 🚀 Migration Guide

### Before (Inconsistent)
```php
return response()->json([
    'success' => 'success',  // ❌ Wrong field name
    'message' => 'Created',
    'results' => $data       // ❌ Inconsistent field name
], 201);
```

### After (Standardized)
```php
return $this->createdResponse($data, 'Data berhasil dibuat');
```

## 📋 Testing

Test all endpoints with:
1. Valid data (success case)
2. Invalid data (validation errors)
3. Unauthorized access (403)
4. Non-existent resources (404)
5. Server errors (500)

Ensure all responses follow the standard format.