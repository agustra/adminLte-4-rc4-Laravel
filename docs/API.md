# 📚 Updated API Documentation (Post-Refactoring)

## 🔄 Response Structure Changes

After the major refactoring, all API endpoints now follow this **consistent structure**:

### ✅ Current Response Format
```json
{
    "data": [...],           // Main data array
    "meta": {                // Metadata object
        "total": 100,        // Total records
        "size": 10,          // Page size
        "current_page": 1,   // ⚠️ CHANGED: Snake case (was currentPage)
        "offset": 0,         // Offset for pagination
        "sort": {            // Sort configuration
            "column": "id",
            "dir": "desc"
        },
        "filter": {          // Filter state
            "date": null,
            "year": null,
            "month": null
        },
        "permissions": {     // User permissions for CRUD
            "create": true,
            "read": true,
            "edit": true,
            "delete": true
        },
        "api_version": "v1", // ⚠️ NEW: API version
        "timestamp": "2025-09-07T00:39:16.941303Z" // ⚠️ NEW: Response timestamp
    }
}
```

### ❌ Old Response Format (Deprecated)
```json
{
    "data": [...],
    "meta": {
        "currentPage": 1,    // ❌ Camel case - NO LONGER USED
        "permissions": {...}
    },
    "api_version": "v1",     // ❌ Wrong location
    "timestamp": "..."       // ❌ Wrong location
}
```

## 🔧 Breaking Changes Summary

| Field | Old Location | New Location | Notes |
|-------|-------------|-------------|-------|
| `currentPage` | `meta.currentPage` | `meta.current_page` | ⚠️ **Snake case now** |
| `api_version` | Root level | `meta.api_version` | ⚠️ **Moved to meta** |
| `timestamp` | Root level | `meta.timestamp` | ⚠️ **Moved to meta** |
| `offset` | Not present | `meta.offset` | ✅ **New field** |
| `sort` | Not present | `meta.sort` | ✅ **New field** |
| `filter` | Not present | `meta.filter` | ✅ **New field** |

## 📋 Updated API Endpoints

### 🔐 Authentication

#### Login
```http
POST /api/login
Content-Type: application/json

{
    "email": "admin@mail.com",
    "password": "password"
}
```

**Response:**
```json
{
    "data": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@mail.com"
    },
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
    "message": "Login berhasil"
}
```

### 👥 Users API

#### List Users
```http
GET /api/users
Authorization: Bearer {access_token}
```

**Query Parameters:**
- `page` (int): Page number (default: 1)
- `size` (int): Items per page (10, 25, 50, 100)
- `search` (string): Search term
- `sort_column` (string): Column to sort by
- `sort_dir` (string): Sort direction (asc, desc)

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Admin User",
            "email": "admin@mail.com",
            "profile_photo_path": null,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "roles": [{"name": "Admin"}],
            "permissions": [{"id": 1, "name": "read users"}],
            "avatar_url": "http://localhost/media/avatars/avatar-default.webp"
        }
    ],
    "meta": {
        "total": 1,
        "size": 10,
        "current_page": 1,
        "offset": 0,
        "sort": {
            "column": "id",
            "dir": "desc"
        },
        "filter": {
            "date": null,
            "year": null,
            "month": null,
            "start_date": null,
            "end_date": null
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

#### Create User
```http
POST /api/users
Authorization: Bearer {access_token}
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "roles": [1, 2]
}
```

#### Update User
```http
PUT /api/users/{id}
Authorization: Bearer {access_token}
Content-Type: application/json

{
    "name": "John Doe Updated",
    "email": "john.updated@example.com",
    "roles": [1, 2],
    "old_password": "current_password",  // Required if updating password
    "password": "new_password123"
}
```

#### Delete User
```http
DELETE /api/users/{id}
Authorization: Bearer {access_token}
```

### 🛡️ Roles API

#### List Roles
```http
GET /api/roles
Authorization: Bearer {access_token}
```

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Admin",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "permissions_count": 36
        }
    ],
    "meta": {
        "total": 8,
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

#### Create Role
```http
POST /api/roles
Authorization: Bearer {access_token}
Content-Type: application/json

{
    "name": "Manager",
    "permissions": [1, 2, 3, 4]
}
```

### 🔑 Permissions API

#### List Permissions
```http
GET /api/permissions
Authorization: Bearer {access_token}
```

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "read users",
            "guard_name": "web",
            "created_at": "2024-01-01T00:00:00.000000Z"
        }
    ],
    "meta": {
        "total": 36,
        "size": 10,
        "current_page": 1,
        "offset": 0,
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

### 📁 Media Library API

#### List Media Files
```http
GET /api/media-management/json
Authorization: Bearer {access_token}
```

**Query Parameters:**
- `folder` (string): Filter by folder path
- `search` (string): Search term

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "App Logo",
            "file_name": "logo.webp",
            "collection": "settings",
            "mime_type": "image/webp",
            "size": "12.34 KB",
            "model_type": "User",
            "model_id": 1,
            "url": "http://localhost/media/settings/logo.webp",
            "created_at": "01 Jan 2024 00:00",
            "action": "<button>...</button>"
        }
    ],
    "folders": [
        {
            "name": "settings",
            "path": "settings",
            "count": 2
        }
    ],
    "all_data": [...], // For global search
    "meta": {
        "total": 1,
        "size": 10,
        "current_page": 1,
        "offset": 0,
        "sort": {
            "column": "created_at",
            "dir": "desc"
        }
    }
}
```

## 🚨 Important Notes for Developers

### Frontend JavaScript Compatibility
- **EasyDataTable.js**: ✅ Compatible - Uses internal state, not API response fields
- **Custom JavaScript**: ⚠️ Update any code expecting `currentPage` to use internal pagination logic
- **New Meta Fields**: ✅ Additional fields are safe to ignore

### API Route Changes
- **Show Routes**: ❌ `GET /api/{resource}/{id}` routes are **EXCLUDED**
- **Available Routes**: ✅ `index`, `store`, `update`, `destroy` only
- **Custom Routes**: ✅ `json`, `by-ids`, `bulkDelete` available for most resources

### Error Responses (Unchanged)
```json
{
    "success": false,
    "status": "error",
    "message": "Error message",
    "error_code": "ERROR_CODE"
}
```

### Validation Errors (Unchanged)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "field": ["Error message"]
    }
}
```

## 🔄 Migration Guide

### For Frontend Developers:
1. ✅ **No changes needed** for EasyDataTable.js users
2. ⚠️ **Update custom pagination** code if using `currentPage` from API
3. ✅ **New meta fields** can be safely ignored or utilized

### For API Consumers:
1. ⚠️ **Update field references**: `currentPage` → `current_page`
2. ⚠️ **Update field locations**: Root `api_version` → `meta.api_version`
3. ✅ **New fields available**: `offset`, `sort`, `filter` in meta
4. ❌ **Remove show route calls**: Use list endpoints instead

### For Mobile Apps:
1. ⚠️ **Update response parsing** for new meta structure
2. ✅ **Backward compatibility**: Old fields still work in most cases
3. ⚠️ **Test thoroughly** before production deployment

---

**Last Updated**: Post-refactoring (September 2025)  
**API Version**: v1  
**Compatibility**: Laravel 12 + AdminLTE 4