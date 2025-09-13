# Dynamic Permission System

Sistem Dynamic Permission memungkinkan mapping controller dan method ke permission disimpan di database, sehingga lebih fleksibel dan mudah dikelola.

## 🚀 Fitur Utama

- **Database-driven Permission Mapping** - Mapping controller/method ke permission disimpan di database
- **Automatic Middleware** - Middleware otomatis mengecek permission berdasarkan route
- **Blade Directives** - Custom blade directives untuk conditional rendering
- **JavaScript Integration** - Permission data tersedia di frontend
- **Caching** - Built-in caching untuk performa optimal

## 📋 Struktur Database

### Tabel `controller_permissions`

```sql
id | controller         | method   | permission      | is_active
---|-------------------|----------|-----------------|----------
1  | PinjamanController | index    | read pinjaman   | 1
2  | PinjamanController | create   | create pinjaman | 1
3  | PinjamanController | edit     | edit pinjaman   | 1
4  | PinjamanController | destroy  | delete pinjaman | 1
```

## 🛠️ Cara Penggunaan

### 1. Blade Templates

Gunakan custom blade directives:

```blade
@dynamiccan('PinjamanController', 'create')
    <button class="btn btn-primary">Tambah</button>
@enddynamiccan

@dynamiccannot('PinjamanController', 'delete')
    <p>Anda tidak memiliki izin hapus</p>
@enddynamiccannot
```

### 2. Controller Setup

Gunakan trait `HasDynamicPermissions`:

```php
<?php

namespace App\Http\Controllers\Admin\v1;

use App\Http\Controllers\Controller;
use App\Traits\HasDynamicPermissions;

class PinjamanController extends Controller
{
    use HasDynamicPermissions;

    public function __construct()
    {
        $this->middleware('dynamic.permission');
    }

    public function index()
    {
        $this->sharePermissionsToView();
        return view('pinjaman.index');
    }
}
```

### 3. API Controller

Untuk API controller, gunakan `withPermissions()`:

```php
public function index(Request $request)
{
    $data = [
        'data' => $results,
        'meta' => ['total' => $total]
    ];
    
    return response()->json($this->withPermissions($data));
}
```

### 4. JavaScript Usage

Permission tersedia di `window.meta.permissions`:

```javascript
// Cek permission di JavaScript
if (window.meta?.permissions?.create) {
    showCreateButton();
}

// Atau gunakan dari response API
function handlePermissionButtons(permissions) {
    const createBtn = document.querySelector('#btnCreate');
    if (permissions?.create) {
        createBtn.style.display = 'block';
    } else {
        createBtn.remove();
    }
}
```

### 5. Helper Functions

```php
// Cek permission secara manual
if (dynamicCan('PinjamanController', 'create')) {
    // User dapat create
}

// Get semua permissions untuk controller
$permissions = getControllerPermissions('PinjamanController');
// Returns: ['index' => true, 'create' => false, ...]
```

## 🔧 Konfigurasi

### Menambah Mapping Baru

```php
use App\Models\ControllerPermission;

ControllerPermission::create([
    'controller' => 'UserController',
    'method' => 'index',
    'permission' => 'read users',
    'is_active' => true
]);
```

### Update Seeder

Tambahkan mapping baru di `ControllerPermissionSeeder`:

```php
$mappings = [
    ['controller' => 'UserController', 'method' => 'index', 'permission' => 'read users'],
    ['controller' => 'UserController', 'method' => 'create', 'permission' => 'create users'],
    // ... mapping lainnya
];
```

## 🎯 Contoh Implementasi Lengkap

### Controller

```php
<?php

namespace App\Http\Controllers\Admin\v1;

use App\Http\Controllers\Controller;
use App\Traits\HasDynamicPermissions;

class UserController extends Controller
{
    use HasDynamicPermissions;

    public function __construct()
    {
        $this->middleware('dynamic.permission');
    }

    public function index()
    {
        $this->sharePermissionsToView();
        return view('admin.users.index');
    }
}
```

### Blade Template

```blade
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3>Data Users</h3>
        @dynamiccan('UserController', 'create')
            <button class="btn btn-primary" id="btnCreate">
                <i class="fas fa-plus"></i> Tambah User
            </button>
        @enddynamiccan
    </div>
    <div class="card-body">
        @dynamiccan('UserController', 'destroy')
            <button class="btn btn-danger" id="btnDeleteSelected" style="display: none;">
                <i class="fas fa-trash"></i> Hapus Terpilih
            </button>
        @enddynamiccan
        
        <table id="users-table" class="table">
            <!-- Table content -->
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.meta = window.meta || {};
    window.meta.permissions = @json($controllerPermissions ?? []);
</script>
<script src="{{ asset('js/users.js') }}"></script>
@endpush
```

### JavaScript

```javascript
document.addEventListener('DOMContentLoaded', function() {
    // Handle permissions dari API response
    function handleApiResponse(data) {
        const permissions = data.meta?.permissions || {};
        handlePermissionButtons(permissions);
    }
    
    function handlePermissionButtons(permissions) {
        const createBtn = document.querySelector('#btnCreate');
        const deleteBtn = document.querySelector('#btnDeleteSelected');
        
        if (permissions.create && createBtn) {
            createBtn.style.display = 'block';
        }
        
        if (permissions.delete && deleteBtn) {
            deleteBtn.style.display = 'none'; // Show when rows selected
        }
        
        // Store globally
        window.currentPermissions = permissions;
    }
    
    // Initialize dengan permissions dari window.meta
    if (window.meta?.permissions) {
        handlePermissionButtons(window.meta.permissions);
    }
});
```

## 🔍 Troubleshooting

### Permission Tidak Bekerja

1. **Cek Middleware**: Pastikan `dynamic.permission` middleware terdaftar
2. **Cek Mapping**: Pastikan ada mapping di tabel `controller_permissions`
3. **Cek User Permission**: Pastikan user memiliki permission yang diperlukan
4. **Clear Cache**: Jalankan `php artisan cache:clear`

### Blade Directive Error

1. **Cek Helper**: Pastikan helper `dynamicCan()` ter-load
2. **Cek Syntax**: Pastikan syntax blade directive benar
3. **Cek Auth**: Pastikan user sudah login

### JavaScript Permission Undefined

1. **Cek View Share**: Pastikan `sharePermissionsToView()` dipanggil di controller
2. **Cek JSON**: Pastikan `@json($controllerPermissions)` tidak error
3. **Cek API Response**: Pastikan API mengembalikan permissions di meta

## 📊 Performance Tips

1. **Caching**: Permission mapping di-cache otomatis
2. **Eager Loading**: Load permission sekali per request
3. **Minimal Queries**: Gunakan `getControllerPermissions()` untuk batch check

## 🔐 Security Notes

1. **Validation**: Selalu validasi permission di backend
2. **Fallback**: Jika tidak ada mapping, default allow/deny sesuai kebutuhan
3. **Audit**: Log akses permission untuk audit trail

---

**Status: ✅ Production Ready**

Sistem Dynamic Permission telah ditest dan siap untuk production use.