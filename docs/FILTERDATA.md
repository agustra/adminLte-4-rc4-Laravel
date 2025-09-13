# FilterData Component

Komponen JavaScript untuk filter data berdasarkan bulan/tahun menggunakan Air Datepicker dengan dukungan dark mode.

## 📍 Location
```
resources/js/components/helpers/filterData.js
```

## 🔧 Dependencies

- **Air Datepicker** (auto-imported via npm)
- **Bootstrap 5** (untuk styling)
- **FontAwesome Icons** (untuk icon calendar dan reset)

## 📦 Installation

### ES6 Modules (Current Usage)
```javascript
import filterData from "@helpers/filterData.js";

// Initialize dengan callback
filterData((bulan, tahun) => {
    console.log('Filter changed:', bulan, tahun);
    // bulan: "01" - "12" (string dengan leading zero)
    // tahun: 2024 (number)
});
```

## 🎯 Basic Usage

### HTML Requirement
```html
<!-- Container element harus ada dengan ID ini -->
<div id="idFilterSingleDate"></div>
```

### JavaScript
```javascript
import filterData from "@helpers/filterData.js";

// Initialize dengan callback function
filterData((bulan, tahun) => {
    if (bulan && tahun) {
        // Filter applied
        console.log(`Filter: ${bulan}/${tahun}`);
        // Reload table atau data dengan filter
        table.reload({ month: bulan, year: tahun });
    } else {
        // Filter cleared
        console.log('Filter cleared');
        table.reload({});
    }
});
```

## ⚙️ Features

### 🎨 **Auto-Generated UI**
Komponen otomatis membuat HTML:
```html
<div class="d-flex align-items-center mb-2">
    <i class="fas fa-calendar-alt me-2 text-muted"></i>
    <div class="input-group input-group-sm">
        <input type="text" id="monthPicker" class="form-control" placeholder="Pilih Bulan" readonly>
        <input type="hidden" id="filter-bulan">
        <input type="hidden" id="filter-tahun">
        <button class="btn btn-outline-secondary" type="button" id="resetFilter" style="display: none;">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
```

### 🌙 **Dark Mode Support**
- Otomatis detect theme dari `data-bs-theme` attribute
- Dynamic styling untuk dark mode
- Listen untuk `themeChanged` event
- Custom CSS injection untuk Air Datepicker dark theme

### 🇮🇩 **Indonesian Locale**
- Nama bulan dalam bahasa Indonesia
- Format tanggal MM/yyyy
- Senin sebagai hari pertama dalam seminggu

## 📊 Callback Data

Callback function menerima 2 parameter:

```javascript
filterData((bulan, tahun) => {
    // bulan: string "01" - "12" (dengan leading zero)
    // tahun: number 2024
    
    if (bulan && tahun) {
        // Filter applied
        console.log(`Selected: ${bulan}/${tahun}`);
    } else {
        // Filter cleared (both empty strings)
        console.log('Filter cleared');
    }
});
```

## 🛠️ Methods

### Auto-Generated Methods
Komponen tidak expose public methods, tapi menyediakan:
- **Reset Button** - Clear filter dan trigger callback dengan empty values
- **Month Selection** - Trigger callback dengan bulan/tahun yang dipilih

## 📋 Current Usage in Application

### 1. **User Management** (`users.js`)
```javascript
import filterData from "@helpers/filterData.js";

filterData((bulan, tahun) => {
    if (bulan && tahun) {
        table.reload({ month: bulan, year: tahun });
    } else {
        table.reload({});
    }
});
```

### 2. **Role Management** (`roles.js`)
```javascript
import filterData from "@helpers/filterData.js";

filterData((bulan, tahun) => {
    if (bulan && tahun) {
        table.reload({ month: bulan, year: tahun });
    } else {
        table.reload({});
    }
});
```

### 3. **Permission Management** (`permissions.js`)
```javascript
import filterData from "@helpers/filterData.js";

filterData((bulan, tahun) => {
    if (bulan && tahun) {
        table.reload({ month: bulan, year: tahun });
    } else {
        table.reload({});
    }
});
```

### 4. **Backup Management** (`backup.js`)
```javascript
import filterData from "@helpers/filterData.js";

filterData((bulan, tahun) => {
    if (bulan && tahun) {
        table.reload({ month: bulan, year: tahun });
    } else {
        table.reload({});
    }
});
```

### 5. **Badge Config Management** (`badge-configs.js`)
```javascript
import filterData from "@helpers/filterData.js";

filterData((bulan, tahun) => {
    if (bulan && tahun) {
        table.reload({ month: bulan, year: tahun });
    } else {
        table.reload({});
    }
});
```

## 🎨 UI Features

### **Visual Elements**
- **Calendar Icon** - FontAwesome calendar-alt icon
- **Month Picker Input** - Readonly input dengan placeholder "Pilih Bulan"
- **Reset Button** - Muncul setelah filter dipilih, hilang setelah reset
- **Small Size** - Menggunakan `input-group-sm` untuk tampilan compact

### **Dark Mode Styling**
```css
.air-datepicker.air-datepicker-dark {
    background: #212529 !important;
    border: 1px solid #495057 !important;
    color: #f8f9fa !important;
}
```

### **Interactive States**
- **Selected Month** - Green background (#198754)
- **Current Month** - Orange background (#fd7e14)
- **Hover Effect** - Blue background (#0d6efd)

## 🔧 Technical Details

### **Air Datepicker Configuration**
```javascript
const datePicker = new AirDatepicker("#monthPicker", {
    view: "months",           // Show months only
    minView: "months",        // Minimum view is months
    dateFormat: "MM/yyyy",    // Format MM/yyyy
    autoClose: true,          // Close after selection
    locale: { /* Indonesian locale */ }
});
```

### **Theme Detection**
```javascript
const currentTheme = document.documentElement.getAttribute("data-bs-theme");
const isDarkMode = currentTheme === "dark";
```

### **Dynamic CSS Injection**
```javascript
if (!document.getElementById("air-datepicker-dark-styles")) {
    const style = document.createElement("style");
    style.id = "air-datepicker-dark-styles";
    style.textContent = `/* Dark mode CSS */`;
    document.head.appendChild(style);
}
```

## 🌐 Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## ⚠️ Requirements

### **HTML Structure**
```html
<!-- REQUIRED: Container dengan ID ini harus ada -->
<div id="idFilterSingleDate"></div>
```

### **Error Handling**
```javascript
let filterContainer = document.getElementById("idFilterSingleDate");
if (!filterContainer) {
    console.error("❌ Elemen #idFilterSingleDate tidak ditemukan!");
    return;
}
```

## 🔄 Integration with EasyDataTable

Komponen ini dirancang untuk bekerja dengan EasyDataTable:

```javascript
// Typical usage pattern
import filterData from "@helpers/filterData.js";

// Initialize filter
filterData((bulan, tahun) => {
    if (bulan && tahun) {
        // Apply month/year filter to table
        table.reload({
            month: bulan,    // "01" - "12"
            year: tahun      // 2024
        });
    } else {
        // Clear all filters
        table.reload({});
    }
});
```

## 🎯 Use Cases

1. **Monthly Reports** - Filter data berdasarkan bulan tertentu
2. **Financial Data** - Filter transaksi per bulan
3. **User Activity** - Filter aktivitas user per bulan
4. **Backup Management** - Filter backup files per bulan
5. **Log Analysis** - Filter log entries per bulan

---

**Status**: ✅ **Active & Maintained**  
**Last Updated**: September 2025  
**Used In**: 5+ admin modules  
**Type**: Month/Year Filter Component