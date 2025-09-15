# FileManager Flow Documentation

## 🎯 **Stable Configuration Overview**

### **User Access Matrix**

| User Role | Admin FileManager | User FileManager | Shared Folder | Private Folder |
|-----------|-------------------|------------------|---------------|----------------|
| **Super Admin** | ✅ Full Access | ✅ Full Access | ✅ Read/Write | ✅ All Users |
| **Admin** | ✅ Full Access | ✅ Full Access | ✅ Read/Write | ✅ All Users |
| **Auditor** | ❌ No Access | ✅ Own Only | ❌ No Access | ✅ Own Only |
| **User** | ❌ No Access | ✅ Own Only | ❌ No Access | ✅ Own Only |

### **Menu Visibility**

| Menu Item | Super Admin | Admin | Auditor | User |
|-----------|-------------|-------|---------|------|
| My Profile | ✅ | ✅ | ✅ | ✅ |
| My Files | ✅ | ✅ | ✅ | ✅ |
| All Files (Admin) | ✅ | ✅ | ❌ | ❌ |

### **Folder Structure (Username-Based)**

```
storage/app/public/filemanager/
├── images/
│   ├── public/              # Shared images (admin only)
│   ├── super-admin/         # Super Admin private images
│   ├── admin/               # Admin private images
│   ├── auditor/             # Auditor private images
│   └── user/                # User private images
└── files/
    ├── public/              # Shared files (admin only)
    ├── super-admin/         # Super Admin private files
    ├── admin/               # Admin private files
    ├── auditor/             # Auditor private files
    └── user/                # User private files
```

### **Setup Command**
```bash
# After fresh migration/seeding
php artisan filemanager:setup
```

## 🔒 **Security Layers**

### **1. Route Protection**
- Admin routes: `middleware(['auth', 'role:admin|super admin'])`
- User routes: `middleware(['auth'])`

### **2. Handler Validation**
- Double-check user roles in `UserFolderHandler`
- Fallback to private folder for non-admin

### **3. Menu Permissions**
- Admin FileManager menu requires `create users` permission
- Only admin/super admin have this permission

### **4. View Protection**
- Admin views redirect non-admin users
- Clear error messages for unauthorized access

## 📁 **File Upload Flow**

### **Admin Upload Flow (3-Tab Interface)**
1. Access `/admin/file-manager`
2. Choose tab:
   - **System Images**: Public folder images only
   - **System Files**: Public folder files only
   - **User Monitoring**: All user folders (auditor/, admin/, etc.)
3. Upload to appropriate location
4. Files visible to all admins

### **User Upload Flow**
1. Access `/my-files`
2. Choose "My Images" or "My Files" tab
3. Upload to private folder only
4. Files only visible to user and admins

### **Avatar Upload Flow**
1. Access `/profile`
2. Click "Change Avatar"
3. Opens `/user-filemanager?type=image`
4. Upload to private images folder
5. Avatar path saved to `profile_photo_path`

## 🎛️ **Configuration Files**

### **Handler: `app/Handler/UserFolderHandler.php`**
- Controls folder access based on routes
- Admin routes: return `null` (all folders)
- User routes: return `user->id` (private only)

### **Config: `config/lfm.php`**
- `allow_private_folder: true`
- `allow_shared_folder: true`
- `shared_folder_name: 'public'`
- Supported MIME types for Excel/CSV

### **Routes: `routes/admin.php` & `routes/web.php`**
- Admin: `/admin/filemanager/*` with role middleware
- User: `/user-filemanager/*` with auth middleware

## 🧪 **Testing Checklist**

### **Admin Testing**
- [ ] Can access `/admin/file-manager`
- [ ] Can see shared folder and all user folders
- [ ] Can upload to any folder
- [ ] Can delete files (with caution)

### **Auditor Testing**
- [ ] Cannot see "All Files (Admin)" menu
- [ ] Can access `/my-files` only
- [ ] Can upload Excel/CSV files
- [ ] Cannot access shared folder
- [ ] Cannot see other user folders

### **Profile Testing**
- [ ] Avatar upload works via FileManager
- [ ] Avatar appears in profile immediately
- [ ] Avatar saved to database correctly
- [ ] Avatar visible in "My Images" tab

## 🚨 **Troubleshooting**

### **403 Forbidden on Upload**
- Check user is accessing correct route (`/user-filemanager/upload`)
- Verify middleware permissions
- Clear config cache: `php artisan config:clear`

### **Menu Still Visible to Non-Admin**
- Check menu permission in database
- Verify user doesn't have `create users` permission
- Clear all caches: `php artisan optimize:clear`

### **Files Not Appearing**
- Check correct tab (Images vs Files)
- Verify folder permissions
- Check handler configuration

## ✅ **Stability Confirmed**

- ✅ Role-based access control working
- ✅ Menu permissions properly configured
- ✅ File uploads working for all user types
- ✅ Security layers preventing unauthorized access
- ✅ Avatar integration functioning
- ✅ Excel/CSV support enabled