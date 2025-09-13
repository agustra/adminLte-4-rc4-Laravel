# ModernTable.js - AdminLTE Laravel Implementation

**📚 Dokumentasi Resmi:** https://www.npmjs.com/package/modern-table-js  
**🔗 CDN:** https://cdn.jsdelivr.net/npm/modern-table-js@1.0.6/core/ModernTable.js

---

## Implementasi Aktual dalam AdminLTE Laravel

Dokumentasi ini berdasarkan implementasi nyata ModernTable.js yang digunakan dalam aplikasi AdminLTE Laravel.

### Import dan Inisialisasi

```javascript
import { ModernTable } from "https://cdn.jsdelivr.net/npm/modern-table-js@1.0.6/core/ModernTable.js";

// Inisialisasi dasar
const table = new ModernTable('#table', {
    // Data source (simple)
    api: '/api/data',
    
    // Atau dengan konfigurasi lengkap
    api: {
        url: '/api/data',
        method: 'GET',
        timeout: 30000,
        headers: {
            'Authorization': 'Bearer YOUR_TOKEN',
            'Content-Type': 'application/json'
        },
        beforeSend: function(params) {
            // Show loading, modify params, etc.
        },
        success: function(data, status, response) {
            // Handle successful response
        },
        error: function(error, status, message) {
            // Handle errors
        },
        complete: function() {
            // Always runs (cleanup, hide loading, etc.)
        }
    },
    
    // Columns configuration
    columns: [
        { data: 'name', title: 'Name', orderable: true },
        { data: 'email', title: 'Email' },
        { 
            data: 'status', 
            title: 'Status',
            render: (data) => `<span class="badge">${data}</span>`
        }
    ],
    
    // Features
    paging: true,
    pageLength: 10,
    searching: true,
    columnSearch: false,
    ordering: true,
    select: true,
    responsive: true,
    
    // UI
    theme: 'auto', // 'light', 'dark', 'auto'
    buttons: ['copy', 'csv', 'excel', 'pdf'],
    
    // Advanced
    stateSave: true,
    keyboard: true,
    accessibility: true
});
```

### Konfigurasi Kolom

```javascript
columns: [
    {
        data: "DT_RowIndex",
        title: "No",
        orderable: false,
        searchable: false,
    },
    {
        data: "avatar_url",
        title: "Avatar",
        orderable: false,
        searchable: false,
        render: (_, __, row) => {
            const avatarUrl = row.avatar_url || "/avatar/avatar-default.jpg";
            return `<img src="${avatarUrl}" alt="Avatar" width="40" height="40" style="border-radius: 50%;">`;
        },
    },
    { data: "name", title: "Name", orderable: true },
    { data: "email", title: "Email", orderable: true },
    {
        data: "roles",
        title: "Roles",
        orderable: false,
        render: (_, __, row) =>
            row.roles?.length
                ? row.roles
                      .map(
                          (role) =>
                              `<span class="badge bg-primary me-1">${role.name}</span>`
                      )
                      .join("")
                : "-",
    },
    {
        data: "actions",
        title: "Action",
        className: "text-center",
        orderable: false,
        searchable: false,
        render: (_, __, row) => ActionButton(row),
    }
]
```

### Opsi Konfigurasi ModernTable.js

```javascript
const config = {
    // Data source
    api: '/api/users', // simple
    // atau
    api: {
        url: '/api/users',
        method: 'GET',
        beforeSend: function(params) {
            return Object.assign(params, getApiParams());
        },
        success: function(data, status, response) {
            console.log('Data loaded:', data);
        },
        error: function(error, status, message) {
            console.error('API Error:', error);
        }
    },
    
    // Columns
    columns: [
        { data: 'name', title: 'Name', orderable: true },
        { data: 'email', title: 'Email' },
        {
            data: 'actions',
            title: 'Actions',
            render: (data, type, row) => `<button onclick="edit(${row.id})">Edit</button>`
        }
    ],
    
    // Features
    paging: true,
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50],
    searching: true,
    columnSearch: false, // individual column search
    ordering: true,
    select: true, // row selection
    responsive: true,
    
    // UI
    theme: 'auto', // 'light', 'dark', 'auto'
    buttons: ['copy', 'csv', 'excel', 'pdf'],
    
    // Advanced
    stateSave: true,
    keyboard: true,
    accessibility: true
};
```

### Methods yang Tersedia

Berdasarkan implementasi aktual:

```javascript
// Reload tabel
table.reload();

// Clear selection
table.clearSelection();

// Tidak ada method lain yang digunakan dalam aplikasi
```

### Helper Components AdminLTE Laravel

#### 1. createApiConfig
```javascript
import { createApiConfig } from "@tables/apiConfig.js";

// Helper AdminLTE Laravel yang mengkonversi ke format ModernTable.js
const apiConfig = createApiConfig({
    url: "/api/users",
    beforeSend: (params) => Object.assign(params, getApiParams()),
    onSuccess: (response) => {
        const permissions = response?.meta?.permissions;
        if (permissions) handlePermissionButtons(permissions);
    },
    onError: (error, status, message) => {
        console.error("API Error:", { error, status, message });
    }
});

// createApiConfig menghasilkan object dengan format:
// {
//     api: {
//         url: '/api/users',
//         beforeSend: function(params) { ... },
//         success: function(data, status, response) { ... },
//         error: function(error, status, message) { ... }
//     }
// }
```

#### 2. createTableButtons
```javascript
import { createTableButtons } from "@tables/tableButtons.js";

const buttons = createTableButtons({
    tableName: "users",
    exportColumns: ["name", "email"],
    bulkDeleteHandler: handleBulkDelete,
    bulkDeleteButtonId: "delete-selected-btn",
    includeCreateButton: true,
    createButtonId: "btnTambah",
    createButtonText: "Add User",
    createHandler: () => {
        showModal("/admin/users/create", "create");
    }
});
```

#### 3. ActionButton
```javascript
import { ActionButton } from "@tables/ActionButton.js";

// Dalam definisi kolom
{
    data: "actions",
    title: "Action",
    className: "text-center",
    orderable: false,
    searchable: false,
    render: (_, __, row) => ActionButton(row)
}
```

#### 4. Selection Handlers
```javascript
import { createSelectionChangeHandler } from "@tables/handleSelectionChange.js";
import { createBulkDeleteHandler } from "@helpers/bulkDelete.js";

const handleSelectionChange = createSelectionChangeHandler({
    buttonId: "delete-selected-btn",
    countSelector: ".selected-count",
    onSelectionChange: (selectedRows, { count, hasSelection }) => {
        console.log(`Selection changed: ${count} users selected`);
    }
});

const handleBulkDelete = createBulkDeleteHandler({
    deleteUrl: "/api/users/multiple/delete",
    itemName: "users",
    onSuccess: (selectedItems, response) => {
        console.log("Bulk delete success:", selectedItems.length, "users deleted");
    },
    onError: (error) => {
        console.error("Bulk delete failed:", error);
    }
});
```

### Filter Integration

```javascript
import filterData from "@helpers/filterData.js";
import DateRangePicker from "@helpers/DateRangePicker.js";

// Month/Year Filter
filterData((bulan, tahun) => {
    handleMonthYearFilter(bulan, tahun, table);
});

// Date Range Filter
new DateRangePicker("#idFilterDateRange", {
    buttonText: "Filter Tanggal",
    onDateSelect: (dateRange) => {
        handleDateRangeFilter(dateRange, table);
    }
});

function handleMonthYearFilter(bulan, tahun, table) {
    if (bulan || tahun) {
        setCustomFilter({
            month: bulan || null,
            year: tahun || null,
        });
    } else {
        resetToDefault();
    }
    table.reload();
}
```

### Contoh Implementasi Lengkap

```javascript
// users.js - Implementasi lengkap
import { ModernTable } from "https://cdn.jsdelivr.net/npm/modern-table-js@1.0.6/core/ModernTable.js";
import { createApiConfig } from "@tables/apiConfig.js";
import { createTableButtons } from "@tables/tableButtons.js";
import { ActionButton } from "@tables/ActionButton.js";
// ... imports lainnya

function createTableConfig() {
    // Helper AdminLTE Laravel
    const apiConfig = createApiConfig({
        url: "/api/users",
        beforeSend: (params) => Object.assign(params, getApiParams()),
        onSuccess: (response) => {
            const permissions = response?.meta?.permissions;
            if (permissions) handlePermissionButtons(permissions);
        },
        onError: (error, status, message) => {
            console.error("Users API Error:", { error, status, message });
        }
    });

    return {
        // API configuration (format ModernTable.js)
        api: {
            url: '/api/users',
            beforeSend: function(params) {
                return Object.assign(params, getApiParams());
            },
            success: function(data, status, response) {
                const permissions = response?.meta?.permissions;
                if (permissions) handlePermissionButtons(permissions);
            },
            error: function(error, status, message) {
                console.error("Users API Error:", { error, status, message });
            }
        },
        columns: [
            { data: "DT_RowIndex", title: "No", orderable: false, searchable: false },
            { data: "name", title: "Name", orderable: true },
            { data: "email", title: "Email", orderable: true },
            {
                data: "actions",
                title: "Action",
                className: "text-center",
                orderable: false,
                searchable: false,
                render: (_, __, row) => ActionButton(row)
            }
        ],
        buttons: createTableButtons({
            tableName: "users",
            exportColumns: ["name", "email"],
            bulkDeleteHandler: handleBulkDelete,
            bulkDeleteButtonId: "delete-selected-btn",
            includeCreateButton: true,
            createButtonId: "btnTambah",
            createButtonText: "Add User",
            createHandler: () => showModal("/admin/users/create", "create")
        }),
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        order: [[6, "desc"]],
        ordering: true,
        searching: true,
        columnSearch: true,
        paging: true,
        select: true,
        responsive: true,
        theme: "auto",
        keyboard: false,
        accessibility: true,
        stateSave: true,
        stateDuration: 3600,
        onSelectionChange: handleSelectionChange,
        onError: (err) => console.error("❌ Table error:", err)
    };
}

function initializeUsersTable() {
    const config = createTableConfig();
    
    const table = new ModernTable("#table-users", {
        ...config,
        initComplete: (data, meta) => {
            console.log("✅ Data loaded:", data.length, "records");
        }
    });

    return table;
}
```

---

## Catatan Penting

1. **Dokumentasi ini berdasarkan implementasi aktual** yang digunakan dalam aplikasi AdminLTE Laravel
2. **Untuk dokumentasi resmi ModernTable.js**, silakan kunjungi: https://www.npmjs.com/package/modern-table-js
3. **Helper components** (createApiConfig, createTableButtons, ActionButton) adalah custom components untuk AdminLTE Laravel
4. **Methods yang tersedia** mungkin lebih banyak dari yang tercantum di sini, lihat dokumentasi resmi untuk lengkapnya

---

**Implementasi ini terbukti bekerja** dalam aplikasi AdminLTE Laravel dan digunakan di semua modul (users, roles, permissions, menus, backup, dll).