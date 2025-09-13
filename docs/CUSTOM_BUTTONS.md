# Custom Buttons - Panduan Penggunaan

Trait `HasActionButtons` mendukung penambahan custom buttons selain tombol standard (show, edit, delete).

## 🎯 Cara Menambahkan Custom Buttons

### 1. Override Method `getCustomButtons()`

```php
protected function getCustomButtons(): array
{
    return [
        [
            'permission' => 'assign roles',
            'variant' => 'success',
            'class' => 'btn-assign',
            'title' => 'Assign Role',
            'content' => '<i class="fa fa-user-plus"></i>'
        ],
        [
            'permission' => 'duplicate roles',
            'variant' => 'info', 
            'class' => 'btn-duplicate',
            'title' => 'Duplicate Role',
            'content' => '<i class="fa fa-copy"></i>'
        ]
    ];
}
```

### 2. Konfigurasi Button

| Parameter | Required | Default | Deskripsi |
|-----------|----------|---------|-----------|
| `permission` | ✅ | - | Permission yang diperlukan |
| `variant` | ❌ | 'secondary' | Bootstrap variant (primary, success, info, warning, danger) |
| `class` | ❌ | 'btn-custom' | CSS class untuk JavaScript handling |
| `title` | ❌ | 'Action' | Tooltip text |
| `content` | ❌ | `<i class="fa fa-cog"></i>` | HTML content button |

## 📝 Contoh Implementasi

### User Management dengan Custom Actions
```php
class UserResource extends JsonResource
{
    use HasActionButtons;
    
    protected function getActionPermissions(): array
    {
        return [
            'show' => 'show users',
            'edit' => 'edit users',
            'delete' => 'delete users'
        ];
    }
    
    protected function getCustomButtons(): array
    {
        return [
            [
                'permission' => 'reset password',
                'variant' => 'warning',
                'class' => 'btn-reset-password',
                'title' => 'Reset Password',
                'content' => '<i class="fa fa-key"></i>'
            ],
            [
                'permission' => 'impersonate users',
                'variant' => 'dark',
                'class' => 'btn-impersonate',
                'title' => 'Login As User',
                'content' => '<i class="fa fa-sign-in-alt"></i>'
            ]
        ];
    }
}
```

### Menu Management dengan Custom Actions
```php
class MenuResource extends JsonResource
{
    use HasActionButtons;
    
    protected function getActionPermissions(): array
    {
        return [
            'show' => 'read menus',
            'edit' => 'edit menus',
            'delete' => 'delete menus'
        ];
    }
    
    protected function getCustomButtons(): array
    {
        return [
            [
                'permission' => 'reorder menus',
                'variant' => 'info',
                'class' => 'btn-reorder',
                'title' => 'Reorder Menu',
                'content' => '<i class="fa fa-sort"></i>'
            ],
            [
                'permission' => 'toggle menu status',
                'variant' => 'secondary',
                'class' => 'btn-toggle-status',
                'title' => 'Toggle Status',
                'content' => '<i class="fa fa-toggle-on"></i>'
            ]
        ];
    }
}
```

## 🔧 JavaScript Integration

Tambahkan event listener untuk custom buttons:

```javascript
// Reset Password Button
document.body.addEventListener("click", function (e) {
    const button = e.target.closest(".btn-reset-password");
    if (button) {
        const userId = button.dataset.id;
        // Handle reset password
        resetUserPassword(userId);
    }
});

// Impersonate Button
document.body.addEventListener("click", function (e) {
    const button = e.target.closest(".btn-impersonate");
    if (button) {
        const userId = button.dataset.id;
        // Handle impersonate
        window.location.href = `/admin/impersonate/${userId}`;
    }
});

// Assign Role Button
document.body.addEventListener("click", function (e) {
    const button = e.target.closest(".btn-assign");
    if (button) {
        const roleId = button.dataset.id;
        // Handle assign role
        showAssignRoleModal(roleId);
    }
});
```

## 🎨 Advanced Customization

### Conditional Custom Buttons
```php
protected function getCustomButtons(): array
{
    $buttons = [];
    
    // Hanya tampilkan untuk role tertentu
    if ($this->name !== 'admin') {
        $buttons[] = [
            'permission' => 'assign roles',
            'variant' => 'success',
            'class' => 'btn-assign',
            'title' => 'Assign Role',
            'content' => '<i class="fa fa-user-plus"></i>'
        ];
    }
    
    // Tampilkan berdasarkan status
    if ($this->status === 'active') {
        $buttons[] = [
            'permission' => 'deactivate roles',
            'variant' => 'warning',
            'class' => 'btn-deactivate',
            'title' => 'Deactivate',
            'content' => '<i class="fa fa-pause"></i>'
        ];
    }
    
    return $buttons;
}
```

### Custom Button dengan Data Attributes
```php
protected function getCustomButtons(): array
{
    return [
        [
            'permission' => 'export data',
            'variant' => 'primary',
            'class' => 'btn-export',
            'title' => 'Export Data',
            'content' => '<i class="fa fa-download"></i>',
            'attributes' => [
                'data-format' => 'excel',
                'data-type' => 'roles'
            ]
        ]
    ];
}
```

Untuk mendukung attributes, perlu update trait:
```php
private function buildCustomButton($id, $config)
{
    $template = '<button class="btn btn-{variant} btn-sm {class}" data-id="{id}" title="{title}" {attributes}>{content}</button>';
    
    $attributes = '';
    if (isset($config['attributes'])) {
        foreach ($config['attributes'] as $key => $value) {
            $attributes .= " {$key}=\"{$value}\"";
        }
    }
    
    return str_replace(
        ['{variant}', '{class}', '{id}', '{title}', '{content}', '{attributes}'],
        [
            $config['variant'] ?? 'secondary',
            $config['class'] ?? 'btn-custom',
            $id,
            $config['title'] ?? 'Action',
            $config['content'] ?? '<i class="fa fa-cog"></i>',
            $attributes
        ],
        $template
    );
}
```

## 🚀 Best Practices

1. **Consistent Naming**: Gunakan naming convention yang konsisten untuk class
2. **Permission Check**: Selalu sertakan permission untuk setiap custom button
3. **Icon Consistency**: Gunakan icon yang sesuai dengan action
4. **Color Coding**: Gunakan variant yang sesuai (danger untuk destructive actions, success untuk positive actions)
5. **JavaScript Handling**: Pastikan ada event listener untuk setiap custom button

Custom buttons memberikan fleksibilitas penuh sambil tetap mempertahankan konsistensi dan performance! 🎯