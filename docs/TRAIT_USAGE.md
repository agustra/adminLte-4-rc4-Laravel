# HasActionButtons Trait - Panduan Penggunaan

Trait `HasActionButtons` menyediakan cara yang efisien dan konsisten untuk menampilkan tombol action di Resource classes.

## 🚀 Keuntungan Trait

- ✅ **Performance Tinggi** - String concatenation, bukan Blade render
- ✅ **Memory Efficient** - Dengan permission caching
- ✅ **Konsisten** - Template yang sama di semua modul
- ✅ **Maintainable** - Perubahan styling di satu tempat
- ✅ **Flexible** - Setiap Resource bisa custom permissions

## 📝 Cara Penggunaan

### 1. Import Trait di Resource Class

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\HasActionButtons;

class YourResource extends JsonResource
{
    use HasActionButtons;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            // ... other fields
            'action' => $this->getActionButtons(),
        ];
    }

    protected function getActionPermissions(): array
    {
        return [
            'show' => 'read items',
            'edit' => 'edit items',
            'delete' => 'delete items'
        ];
    }
}
```

### 2. Implementasi yang Sudah Ada

#### UserResource
```php
protected function getActionPermissions(): array
{
    return [
        'show' => 'show users',
        'edit' => 'edit users',
        'delete' => 'delete users'
    ];
}
```

#### RolesResource
```php
protected function getActionPermissions(): array
{
    return [
        'show' => 'read roles',
        'edit' => 'edit roles',
        'delete' => 'delete roles'
    ];
}
```

#### PermissionsResource
```php
protected function getActionPermissions(): array
{
    return [
        'show' => 'show permissions',
        'edit' => 'edit permissions',
        'delete' => 'delete permissions'
    ];
}
```

## 🎨 Customization

### Hanya Tombol Tertentu
```php
protected function getActionPermissions(): array
{
    return [
        'show' => 'read items',
        'edit' => 'edit items',
        // Tidak ada 'delete' = tombol delete tidak muncul
    ];
}
```

### Custom Permission Names
```php
protected function getActionPermissions(): array
{
    return [
        'show' => 'view-item-details',
        'edit' => 'modify-items',
        'delete' => 'remove-items'
    ];
}
```

## 🔧 Template Customization

Jika perlu mengubah template button, edit file `app/Traits/HasActionButtons.php`:

```php
private static $buttonTemplates = [
    'show' => '<button class="btn btn-info btn-sm buttonShow" data-id="{id}" title="View"><i class="fa fa-eye"></i></button>',
    'edit' => '<button class="btn btn-warning btn-sm buttonUpdate" data-id="{id}" title="Edit"><i class="fa fa-edit"></i></button>',
    'delete' => '<button class="btn btn-danger btn-sm btn-delete" data-id="{id}" title="Delete"><i class="fa fa-trash"></i></button>'
];
```

## 📊 Performance Features

### Permission Caching
Trait menggunakan static caching untuk permission checks:
```php
private static $permissionCache = [];
```

### Template Reuse
Template HTML disimpan sebagai static property untuk efisiensi memory.

## 🔍 JavaScript Integration

Pastikan JavaScript menggunakan class selector yang benar:

```javascript
// Show button
document.body.addEventListener("click", function (e) {
    const button = e.target.closest(".buttonShow");
    if (button) {
        const dataId = button.dataset.id;
        // Handle show action
    }
});

// Edit button  
document.body.addEventListener("click", function (e) {
    const button = e.target.closest(".buttonUpdate");
    if (button) {
        const dataId = button.dataset.id;
        // Handle edit action
    }
});

// Delete button
initializeDeleteComponent({
    buttonSelector: ".btn-delete",
    deleteUrl: "/api/your-endpoint/",
    // ... other options
});
```

## 🆕 Menambah Modul Baru

Untuk modul baru, cukup:

1. **Import trait** di Resource class
2. **Implement method** `getActionPermissions()`
3. **Tambahkan** `'action' => $this->getActionButtons()` di `toArray()`

Contoh untuk MenuResource:
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\HasActionButtons;

class MenuResource extends JsonResource
{
    use HasActionButtons;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'icon' => $this->icon,
            'action' => $this->getActionButtons(),
        ];
    }

    protected function getActionPermissions(): array
    {
        return [
            'show' => 'read menus',
            'edit' => 'edit menus',
            'delete' => 'delete menus'
        ];
    }
}
```

## 🎯 Best Practices

1. **Konsisten Permission Naming** - Gunakan format yang sama di semua modul
2. **Cache Awareness** - Trait sudah handle caching, tidak perlu tambahan
3. **Template Consistency** - Jangan override template kecuali benar-benar perlu
4. **JavaScript Compatibility** - Pastikan class selector sesuai dengan JavaScript

## 🔄 Migration dari Approach Lama

Jika ada Resource yang masih menggunakan approach lama:

```php
// ❌ Hapus method lama
private function getActionButtons()
{
    return view('components.action', [...])->render();
}

// ✅ Ganti dengan trait
use HasActionButtons;

protected function getActionPermissions(): array
{
    return [
        'show' => 'permission-name',
        'edit' => 'permission-name',
        'delete' => 'permission-name'
    ];
}
```

Trait ini memberikan solusi yang optimal antara performance, maintainability, dan flexibility! 🚀