# Spatie Media Library Integration

## 📋 Overview

Proyek ini telah diintegrasikan dengan **Spatie Media Library** untuk mengelola file upload, khususnya avatar user dan file media lainnya.

## 🚀 Fitur yang Tersedia

### ✅ Avatar Management
- Upload avatar untuk user
- Preview avatar real-time
- Hapus avatar dengan konfirmasi
- Fallback ke avatar default
- Validasi file (tipe dan ukuran)

### ✅ File Upload System
- Upload file ke collection tertentu
- Drag & drop support
- Progress indicator
- File validation
- Multiple file types support

## 🛠️ Komponen yang Ditambahkan

### 1. Database
- **Tabel**: `media` (untuk menyimpan metadata file)
- **Migration**: `2025_08_16_173155_create_media_table`

### 2. Model Updates
- **User Model**: Ditambahkan trait `InteractsWithMedia` dan `HasMedia` interface
- **Media Collections**: Collection `avatar` untuk user avatars

### 3. Services
- **MediaService**: Service untuk mengelola upload dan operasi media
- **Methods**: `uploadAvatar()`, `uploadToCollection()`, `deleteFromCollection()`

### 4. Controllers
- **MediaController**: API controller untuk upload media
- **Endpoints**: 
  - `POST /api/media/upload/avatar/{userId}`
  - `DELETE /api/media/avatar/{userId}`
  - `POST /api/media/upload/file`

### 5. Frontend Components
- **MediaUpload.js**: JavaScript component untuk handle upload
- **CSS**: Styling untuk upload interface
- **Form Updates**: User form dengan avatar upload modern

## 📝 Cara Penggunaan

### Upload Avatar User

#### Via API
```javascript
// Upload avatar
const formData = new FormData();
formData.append('avatar', file);

fetch('/api/media/upload/avatar/1', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer ' + token,
        'X-CSRF-TOKEN': csrfToken
    },
    body: formData
})
.then(response => response.json())
.then(data => {
    console.log('Avatar uploaded:', data);
});
```

#### Via Form
```html
<input type="file" class="avatar-upload" data-user-id="1" accept="image/*">
```

### Upload File ke Collection

```javascript
const formData = new FormData();
formData.append('file', file);
formData.append('model_type', 'User');
formData.append('model_id', 1);
formData.append('collection', 'documents');

fetch('/api/media/upload/file', {
    method: 'POST',
    body: formData
});
```

### Menggunakan di Model

```php
// Di model yang menggunakan media
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Model implements HasMedia
{
    use InteractsWithMedia;
    
    // Register collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
        $this->addMediaCollection('documents');
    }
    
    // Get avatar URL
    public function getAvatarUrlAttribute(): string
    {
        $media = $this->getFirstMedia('avatar');
        return $media ? $media->getUrl() : asset('avatar/avatar-default.jpg');
    }
}
```

### Menggunakan MediaService

```php
use App\Services\MediaService;

class SomeController extends Controller
{
    protected $mediaService;
    
    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }
    
    public function uploadAvatar(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $media = $this->mediaService->uploadAvatar($user, $request->file('avatar'));
        
        return response()->json([
            'url' => $media->getUrl()
        ]);
    }
}
```

## 🎨 Frontend Integration

### JavaScript Component
```javascript
// Auto-initialize MediaUpload component
import { MediaUpload } from '/js/components/MediaUpload.js';

const mediaUpload = new MediaUpload({
    maxFileSize: 2048, // KB
    allowedTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
});
```

### CSS Classes
```css
/* Avatar upload area */
.avatar-container:hover .avatar-overlay {
    opacity: 1 !important;
}

/* Drag & drop zone */
.media-dropzone {
    border: 2px dashed #dee2e6;
    padding: 2rem;
    text-align: center;
}

.media-dropzone.dragover {
    border-color: #0d6efd;
    background-color: rgba(13, 110, 253, 0.1);
}
```

## 🔧 Konfigurasi

### File Types & Validation
```php
// Di MediaService atau Controller
$rules = [
    'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
];
```

### Media Collections
```php
// Di model
public function registerMediaCollections(): void
{
    $this->addMediaCollection('avatar')
        ->singleFile()
        ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
        
    $this->addMediaCollection('documents')
        ->acceptsMimeTypes(['application/pdf', 'application/msword']);
}
```

## 🚨 Error Handling

### Common Errors
1. **File too large**: Cek `upload_max_filesize` di php.ini
2. **Invalid file type**: Pastikan MIME type sesuai dengan yang diizinkan
3. **Permission denied**: Pastikan folder storage writable

### Debug Mode
```php
// Untuk debugging, tambahkan di .env
LOG_LEVEL=debug

// Atau di controller
\Log::info('Media upload attempt', ['file' => $request->file('avatar')]);
```

## 📊 Database Schema

### Media Table
```sql
CREATE TABLE `media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `collection_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `disk` varchar(255) NOT NULL,
  `conversions_disk` varchar(255) DEFAULT NULL,
  `size` bigint unsigned NOT NULL,
  `manipulations` json NOT NULL,
  `custom_properties` json NOT NULL,
  `generated_conversions` json NOT NULL,
  `responsive_images` json NOT NULL,
  `order_column` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `media_uuid_index` (`uuid`),
  KEY `media_order_column_index` (`order_column`)
);
```

## 🔄 Migration Commands

```bash
# Install package
composer require spatie/laravel-medialibrary

# Publish migration
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"

# Run migration
php artisan migrate

# Clear cache (jika diperlukan)
php artisan config:clear
php artisan cache:clear
```

## 📈 Performance Tips

1. **Optimize Images**: Gunakan image conversion untuk resize otomatis
2. **CDN Integration**: Pertimbangkan menggunakan CDN untuk file storage
3. **Lazy Loading**: Implement lazy loading untuk preview images
4. **Cleanup**: Buat command untuk cleanup unused media files

## 🔒 Security

1. **File Validation**: Selalu validasi file type dan size
2. **Sanitize Filename**: Hindari karakter berbahaya di nama file
3. **Access Control**: Implementasikan permission untuk upload/delete
4. **Virus Scanning**: Pertimbangkan virus scanning untuk file upload

## 📚 Resources

- [Spatie Media Library Documentation](https://spatie.be/docs/laravel-medialibrary)
- [Laravel File Storage](https://laravel.com/docs/filesystem)
- [Image Optimization](https://spatie.be/docs/image)

---

**Status**: ✅ **READY TO USE**

Spatie Media Library telah berhasil diintegrasikan dan siap digunakan untuk mengelola file upload di aplikasi Laravel Anda.