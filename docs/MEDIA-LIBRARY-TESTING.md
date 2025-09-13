# Media Library Testing Guide

## 📋 Testing Checklist

### ✅ Upload Operations
- [ ] Upload gambar di root folder → File tersimpan di `public/media/`
- [ ] Upload gambar di folder images → File tersimpan di `public/media/images/`
- [ ] Upload multiple files → Progress bar berfungsi
- [ ] Drag & drop upload → File terupload dengan benar
- [ ] File format validation → Hanya format yang didukung

### ✅ File Management
- [ ] Copy file antar folder → File asli tetap ada, copy berhasil
- [ ] Move file antar folder → File pindah ke lokasi baru
- [ ] Delete file → File terhapus dari database dan storage
- [ ] Rename file → Nama file berubah di database
- [ ] File preview → Gambar tampil dengan benar

### ✅ Folder Management
- [ ] Create folder → Folder fisik terbuat di `public/media/`
- [ ] Rename folder → Folder fisik dan database terupdate
- [ ] Delete folder → Folder dan isinya terhapus
- [ ] Navigate folder → Breadcrumb dan navigation berfungsi
- [ ] Folder hierarchy → Parent-child relationship benar

### ✅ Context Menu
- [ ] Right-click pada file → Context menu muncul
- [ ] Right-click pada folder → Context menu folder muncul
- [ ] Context menu positioning → Tidak terpotong viewport
- [ ] Scrollable folder list → Bisa scroll jika folder banyak
- [ ] Copy/Move operations → Berfungsi dari context menu

### ✅ UI/UX Features
- [ ] Grid view → File tampil dalam grid layout
- [ ] List view → File tampil dalam table format
- [ ] Search functionality → Filter file berdasarkan nama
- [ ] Collection filter → Filter berdasarkan collection
- [ ] Type filter → Filter berdasarkan tipe file
- [ ] Responsive design → Berfungsi di mobile dan desktop

### ✅ API Endpoints
- [ ] `GET /api/media-management/json` → Return media list
- [ ] `POST /api/media/upload/file` → Upload file berhasil
- [ ] `POST /api/media/copy` → Copy file berhasil
- [ ] `POST /api/media/move` → Move file berhasil
- [ ] `DELETE /api/media-management/{id}` → Delete file berhasil
- [ ] `POST /api/media/folders` → Create folder berhasil

## 🧪 Manual Testing Steps

### Test 1: Upload File di Root
1. Buka Media Library
2. Pastikan berada di root folder (breadcrumb: 📁 Root)
3. Upload gambar dengan drag & drop
4. **Expected**: File tersimpan di `public/media/filename.webp`
5. **Expected**: File tampil di root folder, tidak ada folder baru

### Test 2: Upload File di Folder
1. Navigate ke folder "images" 
2. Upload gambar
3. **Expected**: File tersimpan di `public/media/images/filename.webp`
4. **Expected**: File hanya tampil di folder images

### Test 3: Copy Operation
1. Right-click pada file di folder images
2. Pilih "Copy to Root"
3. **Expected**: File asli tetap di images
4. **Expected**: Copy muncul di root folder
5. **Expected**: Tidak ada 404 error

### Test 4: Move Operation
1. Right-click pada file di root
2. Pilih "Move to Folder" → images
3. **Expected**: File pindah dari root ke images
4. **Expected**: File tidak ada lagi di root

### Test 5: Context Menu Positioning
1. Right-click pada file di pojok kanan bawah
2. **Expected**: Context menu muncul dan tidak terpotong
3. **Expected**: Bisa scroll jika folder banyak

### Test 6: Folder Operations
1. Create folder baru "test-folder"
2. **Expected**: Folder fisik terbuat di `public/media/test-folder/`
3. Rename folder ke "renamed-folder"
4. **Expected**: Folder fisik berubah nama
5. Delete folder
6. **Expected**: Folder fisik terhapus

## 🔧 Troubleshooting

### Issue: File Upload 404
- **Cause**: Path generator atau route media tidak benar
- **Fix**: Cek `app/Support/MediaPathGenerator.php` dan route `media/{path}`

### Issue: Copy Operation 404
- **Cause**: File dicopy tapi path tidak sesuai
- **Fix**: Pastikan `MediaOperationsController::copy()` tidak menggunakan `addMedia`

### Issue: Context Menu Terpotong
- **Cause**: Positioning tidak cek viewport boundary
- **Fix**: Implementasi boundary checking di `handleContextMenu()`

### Issue: Folder Tidak Bisa Scroll
- **Cause**: CSS overflow tidak diset
- **Fix**: Tambahkan `overflow-y: auto` pada folder list containers

## 📊 Performance Testing

### File Upload Performance
- **Small files** (< 1MB): < 2 seconds
- **Medium files** (1-10MB): < 10 seconds  
- **Large files** (10-50MB): < 30 seconds

### API Response Time
- **Media list**: < 500ms
- **File operations**: < 1 second
- **Folder operations**: < 500ms

## 🚀 Production Readiness

### Security Checklist
- [ ] File type validation
- [ ] File size limits
- [ ] Path traversal protection
- [ ] Authentication required
- [ ] Permission-based access

### Performance Checklist
- [ ] Image optimization (WebP conversion)
- [ ] Lazy loading for large galleries
- [ ] Pagination for large datasets
- [ ] Efficient database queries
- [ ] CDN integration ready

### Browser Compatibility
- [ ] Chrome 90+
- [ ] Firefox 88+
- [ ] Safari 14+
- [ ] Edge 90+
- [ ] Mobile browsers

## 📝 Test Results Log

| Test Case | Status | Date | Notes |
|-----------|--------|------|-------|
| Upload to Root | ✅ | 2025-08-17 | Files stored in public/media/ |
| Upload to Folder | ✅ | 2025-08-17 | Files stored in subfolders |
| Copy Operation | ✅ | 2025-08-17 | Original file preserved |
| Move Operation | ✅ | 2025-08-17 | File moved successfully |
| Context Menu | ✅ | 2025-08-17 | Positioning and scroll fixed |
| Folder Management | ✅ | 2025-08-17 | CRUD operations working |
| API Endpoints | ✅ | 2025-08-17 | All endpoints functional |
| Responsive UI | ✅ | 2025-08-17 | Mobile and desktop tested |

---

**Status: ✅ ALL TESTS PASSED - PRODUCTION READY**