# DateRangePicker Component

Komponen JavaScript untuk memilih rentang tanggal dengan Bootstrap dropdown dan Flatpickr.

## 📍 Location
```
resources/js/components/helpers/DateRangePicker.js
```

## 🔧 Dependencies

- Bootstrap 5 (harus sudah ada di project)
- Flatpickr (auto-loaded dari CDN)
- DayJS (auto-loaded dari CDN)
- FontAwesome Icons (harus sudah ada di project)

## 📦 Installation

### ES6 Modules (Current Usage)
```javascript
import DateRangePicker from "@helpers/DateRangePicker.js";

const datePicker = new DateRangePicker("#idFilterDateRange", {
    onDateSelect: function(dateRange) {
        console.log('Selected:', dateRange);
    }
});
```

**Catatan:** Komponen akan otomatis memuat Flatpickr CSS/JS dan DayJS dari CDN.

## 🎯 Basic Usage

### HTML
```html
<div id="date-picker-container"></div>
```

### JavaScript
```javascript
import DateRangePicker from "@helpers/DateRangePicker.js";

const datePicker = new DateRangePicker('#date-picker-container', { 
    onDateSelect: function(dateRange) {
        console.log('Selected:', dateRange);
    }
});
```

## ⚙️ Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `onDateSelect` | Function | `console.log` | Callback saat tanggal dipilih |
| `buttonText` | String | `'Pilih Filter'` | Text pada tombol dropdown |
| `buttonClass` | String | Bootstrap classes | CSS class untuk tombol |
| `buttonStyle` | String | `'min-width: 150px;'` | Inline style untuk tombol |

## 📊 Callback Data

Callback `onDateSelect` menerima object dengan struktur:

```javascript
{
    start: "2024-01-01",           // Format YYYY-MM-DD
    end: "2024-01-07",             // Format YYYY-MM-DD  
    formatted: "1 Jan 2024 - 7 Jan 2024", // Format readable
    startDate: dayjs_object,       // DayJS object
    endDate: dayjs_object          // DayJS object
}
```

## 🛠️ Methods

### `setDateRange(startDate, endDate)`
Set rentang tanggal secara programmatic:
```javascript
datePicker.setDateRange('2024-01-01', '2024-01-07');
```

### `destroy()`
Hapus komponen dari DOM:
```javascript
datePicker.destroy();
```

## 📋 Examples

### 1. Current Usage in Application
```javascript
// Used in: users.js, roles.js, permissions.js, backup.js, badge-configs.js
import DateRangePicker from "@helpers/DateRangePicker.js";

const datePicker = new DateRangePicker("#idFilterDateRange", {
    onDateSelect: function(dateRange) {
        if (dateRange) {
            // Apply date filter to table
            table.reload({
                start_date: dateRange.start,
                end_date: dateRange.end
            });
        } else {
            // Clear date filter
            table.reload({});
        }
    }
});
```

### 2. Custom Styling
```javascript
const picker = new DateRangePicker('#container', {
    buttonText: 'Pilih Periode',
    buttonClass: 'btn btn-primary dropdown-toggle',
    buttonStyle: 'min-width: 320px;'
});
```

### 3. With Server Integration
```javascript
const picker = new DateRangePicker('#container', {
    onDateSelect: async function(dateRange) {
        if (dateRange) {
            await fetch('/api/filter', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dateRange)
            });
        }
    }
});
```

## 🚀 Quick Range Options

Komponen menyediakan pilihan cepat:
- **Hari Ini** - Tanggal hari ini
- **Kemarin** - Tanggal kemarin  
- **7 Hari Terakhir** - 7 hari terakhir termasuk hari ini
- **30 Hari Terakhir** - 30 hari terakhir termasuk hari ini
- **Bulan Ini** - Dari tanggal 1 sampai akhir bulan ini
- **Bulan Lalu** - Dari tanggal 1 sampai akhir bulan lalu
- **Rentang Kustom** - Buka Flatpickr calendar untuk pilih manual
- **Reset Filter** - Clear semua filter tanggal

## 🎨 UI Features

- **Bootstrap 5 Integration** - Menggunakan dropdown Bootstrap
- **FontAwesome Icons** - Icon untuk setiap pilihan
- **Responsive Design** - Bekerja di desktop dan mobile
- **Auto-loading Dependencies** - Tidak perlu manual load Flatpickr/DayJS
- **Indonesian Locale** - Menggunakan bahasa Indonesia

## 🌐 Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## 🔄 Current Implementation

Komponen ini saat ini digunakan di:
- **User Management** (`resources/js/admin/users/users.js`)
- **Role Management** (`resources/js/admin/roles/roles.js`)
- **Permission Management** (`resources/js/admin/permissions/permissions.js`)
- **Backup Management** (`resources/js/admin/backup.js`)
- **Badge Config Management** (`resources/js/admin/badge-configs/badge-configs.js`)

Semua menggunakan pattern yang sama untuk filtering data berdasarkan rentang tanggal.

## 🔧 Technical Details

### Auto-loading Dependencies
```javascript
async loadDependencies() {
    // Load CSS
    if (!document.querySelector('link[href*="flatpickr"]')) {
        await this.loadCSS("https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css");
    }

    // Load JavaScript dependencies
    const scripts = [
        { url: "https://cdn.jsdelivr.net/npm/flatpickr", check: () => window.flatpickr },
        { url: "https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js", check: () => window.flatpickr?.l10ns?.id },
        { url: "https://cdn.jsdelivr.net/npm/dayjs@1.10.8/dayjs.min.js", check: () => window.dayjs },
        // ... more dependencies
    ];
}
```

### Vite Alias Configuration
```javascript
// vite.config.js
resolve: {
    alias: {
        "@helpers": path.resolve(__dirname, "resources/js/components/helpers"),
        // ... other aliases
    },
}
```

---

**Status**: ✅ **Active & Maintained**  
**Last Updated**: September 2025  
**Used In**: 5+ admin modules