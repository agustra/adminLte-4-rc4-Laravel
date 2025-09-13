# 🔌 API Testing Guide - AdminLTE Laravel

## 📋 Quick Start

### Authentication
```bash
# Login to get access token
curl -X POST http://localhost:8001/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@mail.com", "password": "password"}'

# Response
{
  "user": {...},
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}

# Use token in subsequent requests
export TOKEN="your_access_token_here"
```

## 🔐 Authentication Endpoints

### Login
```bash
curl -X POST http://localhost:8001/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@mail.com",
    "password": "password"
  }'
```

### Logout
```bash
curl -X POST http://localhost:8001/api/logout \
  -H "Authorization: Bearer $TOKEN"
```

## 👥 User Management

### List Users
```bash
curl -X GET http://localhost:8001/api/users \
  -H "Authorization: Bearer $TOKEN"
```

### Create User
```bash
curl -X POST http://localhost:8001/api/users \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New User",
    "email": "newuser@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": [2]
  }'
```

### Update User
```bash
curl -X PUT http://localhost:8001/api/users/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated User",
    "email": "updated@example.com",
    "role": [1]
  }'
```

### Delete User
```bash
curl -X DELETE http://localhost:8001/api/users/1 \
  -H "Authorization: Bearer $TOKEN"
```

### Multiple Delete
```bash
curl -X POST http://localhost:8001/api/users/multiple/delete \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"ids": [1, 2, 3]}'
```

## 🔧 Role Management

### List Roles
```bash
curl -X GET http://localhost:8001/api/roles \
  -H "Authorization: Bearer $TOKEN"
```

### Create Role
```bash
curl -X POST http://localhost:8001/api/roles \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New Role",
    "permissions": ["read users", "create users"]
  }'
```

## 🔑 Permission Management

### List Permissions
```bash
curl -X GET http://localhost:8001/api/permissions \
  -H "Authorization: Bearer $TOKEN"
```

### Create Permission
```bash
curl -X POST http://localhost:8001/api/permissions \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "new permission",
    "guard_name": "web"
  }'
```

## 📝 Menu Management

### List Menus
```bash
curl -X GET http://localhost:8001/api/menus \
  -H "Authorization: Bearer $TOKEN"
```

### Create Menu
```bash
curl -X POST http://localhost:8001/api/menus \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New Menu",
    "url": "/new-menu",
    "icon": "fas fa-star",
    "badge_text": "NEW",
    "badge_color": "success",
    "permission": "menu new",
    "order": 10,
    "is_active": true
  }'
```

### Update Menu
```bash
curl -X PUT http://localhost:8001/api/menus/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Menu",
    "badge_text": "HOT",
    "badge_color": "danger"
  }'
```

### Delete Menu
```bash
curl -X DELETE http://localhost:8001/api/menus/1 \
  -H "Authorization: Bearer $TOKEN"
```

### Get Sidebar HTML
```bash
curl -X GET http://localhost:8001/admin/api/menus/sidebar \
  -H "Authorization: Bearer $TOKEN"
```

## 💾 Backup Management

### List Backups
```bash
curl -X GET http://localhost:8001/api/backup \
  -H "Authorization: Bearer $TOKEN"
```

### Delete Backup
```bash
curl -X DELETE http://localhost:8001/api/backup/filename.zip \
  -H "Authorization: Bearer $TOKEN"
```

## 🧪 Test Endpoints

### Test Google Drive
```bash
curl -X GET http://localhost:8001/test-gdrive
```

### Test Backup Creation
```bash
curl -X GET http://localhost:8001/test-backup-create
```

### Test Google Drive Upload
```bash
curl -X GET http://localhost:8001/test-gdrive-upload
```

## 📊 Response Examples

### Successful Response
```json
{
  "status": "success",
  "message": "Data berhasil ditambahkan",
  "results": {
    "id": 1,
    "name": "User Name",
    "email": "user@example.com",
    "roles": ["admin"],
    "permissions": ["read users", "create users"],
    "created_at": "16 Agustus 2025 13:59"
  }
}
```

### Error Response
```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "email": ["Email sudah terdaftar"]
  }
}
```

### Pagination Response
```json
{
  "data": [...],
  "meta": {
    "total": 15,
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

## 🔍 Query Parameters

### Pagination
```bash
# Page size
?size=25

# Page number
?page=2

# Offset
?offset=10
```

### Search
```bash
# Search users
?search=john

# Search with filters
?search=admin&role=1
```

### Sorting
```bash
# Sort by column
?sort_column=name&sort_dir=asc

# Multiple sorting
?sort_column=created_at&sort_dir=desc
```

## 🛡️ Error Codes

| Code | Description | Example |
|------|-------------|---------|
| 200 | Success | Data retrieved |
| 201 | Created | Resource created |
| 400 | Bad Request | Invalid data |
| 401 | Unauthorized | Invalid token |
| 403 | Forbidden | No permission |
| 404 | Not Found | Resource not found |
| 422 | Validation Error | Form validation failed |
| 500 | Server Error | Internal error |

## 🔧 Testing Tools

### Postman Collection
Import the following endpoints into Postman:

```json
{
  "info": {
    "name": "AdminLTE Laravel API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "auth": {
    "type": "bearer",
    "bearer": [
      {
        "key": "token",
        "value": "{{access_token}}",
        "type": "string"
      }
    ]
  }
}
```

### Environment Variables
```json
{
  "base_url": "http://localhost:8001",
  "access_token": "your_token_here",
  "admin_email": "admin@mail.com",
  "admin_password": "password"
}
```

## 📝 Testing Checklist

### Authentication
- [ ] Login with valid credentials
- [ ] Login with invalid credentials
- [ ] Logout with valid token
- [ ] Access protected route without token
- [ ] Access protected route with expired token

### CRUD Operations
- [ ] Create resource with valid data
- [ ] Create resource with invalid data
- [ ] Read single resource
- [ ] Read resource list with pagination
- [ ] Update resource with valid data
- [ ] Update resource with invalid data
- [ ] Delete existing resource
- [ ] Delete non-existent resource

### Permissions
- [ ] Access allowed endpoint with permission
- [ ] Access forbidden endpoint without permission
- [ ] Role-based access control
- [ ] Permission inheritance

### Data Validation
- [ ] Required fields validation
- [ ] Email format validation
- [ ] Password confirmation
- [ ] Unique constraints
- [ ] Data type validation

---

**Happy Testing! 🚀**