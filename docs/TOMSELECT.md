# TomSelect Components

Komponen JavaScript untuk dropdown dengan search, create, dan modal integration menggunakan TomSelect library.

## 📍 Location
```
resources/js/components/form/
├── InitTomSelect.js      # Main TomSelect wrapper class
└── TomSelectModal.js     # Modal integration helper
```

## 🔧 Dependencies

- **TomSelect** (npm package)
- **Bootstrap 5** (untuk modal dan styling)
- **Axios** (untuk API calls)
- **TomSelect Bootstrap 5 CSS** (auto-imported)

## 📦 Main Component: InitTomSelect

### Basic Usage
```javascript
import InitTomSelect from "@forms/InitTomSelect.js";

const tomSelect = new InitTomSelect("#my-select", {
    urlGet: "/api/users",
    urlStore: "/api/users",
    create: true,
    multiple: false
});
```

## ⚙️ Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `valueField` | String | `"id"` | Field untuk value option |
| `labelField` | String | `"name"` | Field untuk label option |
| `searchField` | Array | `["name"]` | Field yang bisa dicari |
| `plugins` | Array | `["virtual_scroll"]` | TomSelect plugins |
| `maxOptions` | Number | `200` | Maximum options ditampilkan |
| `closeAfterSelect` | Boolean | `false` | Close dropdown setelah select |
| `create` | Boolean | `false` | Allow create new items |
| `multiple` | Boolean | `false` | Multiple selection |
| `urlGet` | String | `null` | API endpoint untuk load data |
| `urlStore` | String | `null` | API endpoint untuk create data |
| `urlByIds` | String | `null` | API endpoint untuk load by IDs |
| `modalId` | String | `null` | Modal ID untuk create form |
| `createUrl` | String | `null` | URL untuk load create form |
| `createSingle` | Boolean | `false` | Use modal untuk create |
| `grouped` | Boolean | `false` | Enable optgroup support |
| `preSelectedValues` | Array | `[]` | Pre-selected values untuk edit mode |

## 🎯 Usage Examples

### 1. **Simple Dropdown**
```javascript
import InitTomSelect from "@forms/InitTomSelect.js";

const userSelect = new InitTomSelect("#user_id", {
    urlGet: "/api/users",
    multiple: false
});
```

### 2. **Multiple Selection with Create**
```javascript
const rolesSelect = new InitTomSelect("#roles", {
    urlGet: "/api/roles",
    urlStore: "/api/roles",
    create: true,
    multiple: true
});
```

### 3. **With Modal Create Form**
```javascript
const categorySelect = new InitTomSelect("#category_id", {
    urlGet: "/api/categories",
    urlStore: "/api/categories",
    create: true,
    createSingle: true,
    modalId: "createCategoryModal",
    createUrl: "/admin/categories/create"
});
```

### 4. **Grouped Options**
```javascript
const permissionSelect = new InitTomSelect("#permissions", {
    urlGet: "/api/permissions/grouped",
    grouped: true,
    multiple: true
});
```

### 5. **Edit Mode with Pre-selected Values**
```javascript
const editRolesSelect = new InitTomSelect("#roles", {
    urlGet: "/api/roles",
    urlByIds: "/api/roles/by-ids", // Efficient loading
    multiple: true,
    preSelectedValues: ["1", "3", "5"] // Pre-select these IDs
});
```

## 🔄 Current Usage in Application

### 1. **Role Selection** (`rolesSelect.js`)
```javascript
import InitTomSelect from "@forms/InitTomSelect.js";

const rolesTomSelect = new InitTomSelect(rolesSelect, {
    urlGet: "/api/roles/json",
    urlByIds: "/api/roles/by-ids",
    multiple: true,
    preSelectedValues: preSelectedRoles
});
```

### 2. **Permission Selection** (`rolePermissionsSelect.js`)
```javascript
const permissionsTomSelect = new InitTomSelect(permissionsSelect, {
    urlGet: "/api/permissions/json",
    urlByIds: "/api/permissions/by-ids",
    multiple: true,
    grouped: true,
    preSelectedValues: preSelectedPermissions
});
```

### 3. **Brand Selection** (`merekSelect.js`)
```javascript
const tomSelectInstance = new InitTomSelect(merekSelect, {
    urlGet: "/api/master-data/merek/data",
    urlStore: "/api/master-data/merek",
    create: true,
    createSingle: true,
    modalId: "createMerekModal",
    createUrl: "/admin/master-data/create/merek"
});
```

## 🎨 Features

### **🔍 Search & Pagination**
- Real-time search dengan throttling (250ms)
- Virtual scrolling untuk performance
- Pagination dengan `has_more` detection
- Auto-load more saat scroll

### **➕ Create New Items**
- **Direct Create**: Langsung POST ke API
- **Modal Create**: Load form dalam modal
- Field mapping untuk form submission
- Toast notification setelah create

### **👥 Grouped Options**
- Optgroup support dengan badge colors
- Auto-generate optgroup dari data
- Custom optgroup headers dengan random colors

### **✏️ Edit Mode Support**
- Pre-select values untuk edit form
- Efficient loading dengan `urlByIds`
- Fallback ke `urlGet` jika `urlByIds` tidak ada

## 📊 API Response Format

### **Standard Response**
```json
{
    "data": [
        {"id": 1, "name": "Option 1"},
        {"id": 2, "name": "Option 2"}
    ],
    "has_more": false
}
```

### **Grouped Response**
```json
{
    "data": [
        {
            "label": "Users",
            "options": [
                {"id": 1, "name": "User 1"},
                {"id": 2, "name": "User 2"}
            ]
        },
        {
            "label": "Admins", 
            "options": [
                {"id": 3, "name": "Admin 1"}
            ]
        }
    ]
}
```

### **By IDs Response**
```json
{
    "data": [
        {"id": 1, "name": "Selected Item 1"},
        {"id": 3, "name": "Selected Item 3"}
    ]
}
```

## 🎨 UI Customization

### **Custom Renderers**
```javascript
render: {
    option: function(d, esc) {
        return `<div>${esc(d.name)}</div>`;
    },
    item: function(d, esc) {
        return `<div>${esc(d.name)}</div>`;
    },
    option_create: function(data, escape) {
        return `<div class="create">+ Tambah "${escape(data.input)}"</div>`;
    },
    optgroup_header: function(data, escape) {
        const randomColor = colors[Math.floor(Math.random() * colors.length)];
        return `<div class="optgroup-header">
            <span class="badge bg-${randomColor} me-2">${label.charAt(0)}</span>
            ${label}
        </div>`;
    }
}
```

## 🔧 TomSelectModal Helper

### Usage
```javascript
import { handleTomSelectModal } from "@forms/TomSelectModal.js";

handleTomSelectModal(
    "/admin/categories/create",  // Form URL
    "createModal",               // Modal ID
    "New Category",              // Pre-fill value
    {
        submitUrl: "/api/categories",
        fieldMapping: { "name": "category_name" }
    },
    (response) => {
        // Success callback
        console.log("Created:", response.data);
    }
);
```

### Features
- Load form content via AJAX
- Pre-fill form fields
- Handle form submission
- Field mapping untuk compatibility
- Success callback integration

## 🔄 Integration Patterns

### **With EasyDataTable**
```javascript
// In form modal
const roleSelect = new InitTomSelect("#roles", {
    urlGet: "/api/roles",
    multiple: true,
    preSelectedValues: editData?.roles?.map(r => r.id.toString()) || []
});
```

### **With Form Validation**
```javascript
// TomSelect automatically updates hidden input
// Form validation works normally
<select name="roles[]" id="roles" multiple required>
    <!-- Options loaded dynamically -->
</select>
```

## 🌐 Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## ⚠️ Requirements

### **HTML Structure**
```html
<!-- Basic select element -->
<select name="category_id" id="category_id">
    <option value="">Select Category</option>
</select>

<!-- Multiple select -->
<select name="roles[]" id="roles" multiple>
    <!-- Options loaded dynamically -->
</select>
```

### **Modal Structure** (for createSingle)
```html
<div class="modal fade" id="createModal">
    <div class="modal-dialog">
        <!-- Content loaded via AJAX -->
    </div>
</div>
```

## 🎯 Performance Features

- **Virtual Scrolling** - Handle ribuan options
- **Load Throttling** - 250ms delay untuk search
- **Pagination** - Load data secara bertahap
- **Efficient Pre-loading** - `urlByIds` untuk edit mode
- **Memory Management** - Proper cleanup dan event handling

---

**Status**: ✅ **Active & Maintained**  
**Last Updated**: September 2025  
**Used In**: 4+ specialized selectors  
**Type**: Advanced Dropdown Component