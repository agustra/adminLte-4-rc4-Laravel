import { ModernTable } from "https://cdn.jsdelivr.net/npm/modern-table-js@1.0.6/core/ModernTable.js";
import { Delete } from "@helpers/delete.js";
import { createBulkDeleteHandler } from "@helpers/bulkDelete.js";
import { createSelectionChangeHandler } from "@tables/handleSelectionChange.js";
import { createApiConfig } from "@tables/apiConfig.js";
import { handlePermissionButtons } from "@tables/handlePermissionButtons.js";
import { createTableButtons } from "@tables/tableButtons.js";
import { ActionButton } from "@tables/ActionButton.js";
import { showModal } from "@handlers/modalHandler.js";
import { fetchAxios } from "@handlers/fetchAxios.js";
import filterData from "@helpers/filterData.js";
import DateRangePicker from "@helpers/DateRangePicker.js";
import { formatDate, getTodayString } from "@utils/formatDate.js";

// ============================
// Constants
// ============================
const TODAY_STRING = getTodayString();

const DEFAULT_FILTERS = {
    month: null,
    year: null,
    start_date: null,
    end_date: null,
    date: TODAY_STRING,
};

const generalConfig = {
    // Table & URLs
    tableId: "#table-badge-configs",
    urlWeb: "/admin/badge-configs/",
    urlApi: "/api/badge-configs",
    deleteMultipleUrl: "/api/badge-configs/multiple/delete",

    // Table Configuration
    tableName: "badge-configs",
    exportColumns: ["menu_url", "model_class"],
    createButtonText: "Add Badge Config",
    itemName: "badge-configs", // untuk bulk delete

    // Table Features
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50],
    defaultOrder: [[0, "desc"]], // first column

    // Filter Configuration
    dateRangeButtonText: "Filter Tanggal",
    showAllDataOnLoad: true, // true = semua data, false = hari ini saja

    // Console Messages
    apiErrorPrefix: "Badge Configs API Error:",
    dataLoadedMessage: "✅ Data loaded:",

    // Button IDs
    createButtonId: "btnTambah",
    bulkDeleteButtonId: "delete-selected-btn",

    // Selectors
    dateRangeSelector: "#idFilterDateRange",
    dateRangeTextSelector: "#idFilterDateRange .date-range-text",

    // Form & Modal
    actionButtonSelector: "#btnAction",
    formSelector: ".FormAction",

    // Button Classes
    deleteButtonClass: ".btn-delete",
    createButtonClass: ".btnTambah",
    updateButtonClass: ".buttonUpdate",
    showButtonClass: ".buttonShow",
};

// ============================
// Global State
// ============================
let hasCustomFilter = false;
let currentFilters = { ...DEFAULT_FILTERS };

// ============================
// Helpers
// ============================

const getApiParams = () => {
    if (!hasCustomFilter) {
        return generalConfig.showAllDataOnLoad ? {} : { date: TODAY_STRING };
    }

    return Object.fromEntries(
        Object.entries(currentFilters).filter(([_, v]) => v !== null)
    );
};

const setCustomFilter = (filters) => {
    hasCustomFilter = true;
    currentFilters = { ...currentFilters, ...filters, date: null };
};

const resetToDefault = () => {
    hasCustomFilter = false;
    currentFilters = { ...DEFAULT_FILTERS };
};

// Create selection change handler using component
const handleSelectionChange = createSelectionChangeHandler({
    buttonId: generalConfig.bulkDeleteButtonId,
    countSelector: ".selected-count",
    onSelectionChange: (selectedRows, { count, hasSelection }) => {
        console.log(
            `ℹ️ Selection changed: ${count} ${generalConfig.itemName} selected`
        );
    },
});

// Create bulk delete handler using component
const handleBulkDelete = createBulkDeleteHandler({
    deleteUrl: generalConfig.deleteMultipleUrl,
    itemName: generalConfig.itemName,
    onSuccess: (selectedItems, response) => {
        console.log(
            "✅ Bulk delete success:",
            selectedItems.length,
            `${generalConfig.itemName} deleted`
        );
    },
    onError: (error) => {
        console.error("❌ Bulk delete failed:", error);
    },
});

// ============================
// Table Configuration
// ============================
function createTableConfig() {
    const today = formatDate(new Date());

    // Create API configuration using component
    const apiConfig = createApiConfig({
        url: generalConfig.urlApi,
        beforeSend: (params) => Object.assign(params, getApiParams()),
        onSuccess: (response) => {
            const permissions = response?.meta?.permissions;
            if (permissions) handlePermissionButtons(permissions);
        },
        onError: (error, status, message) => {
            console.error(generalConfig.apiErrorPrefix, {
                error,
                status,
                message,
            });
        },
    });

    return {
        ...apiConfig,
        columns: [
            {
                data: "DT_RowIndex",
                title: "No",
                orderable: false,
                searchable: false,
            },
            { data: "menu_url", title: "Menu URL", orderable: true },
            { data: "model_class", title: "Model Class", orderable: true },
            { data: "date_field", title: "Date Field", orderable: true },
            {
                data: "is_active",
                title: "Status",
                orderable: true,
                render: (data) => {
                    const isActive = data == 1 || data === true;
                    const badgeClass = isActive ? "bg-success" : "bg-danger";
                    const text = isActive ? "Active" : "Inactive";
                    return `<span class="badge ${badgeClass}">${text}</span>`;
                },
            },
            { data: "description", title: "Description", orderable: true },
            {
                data: "actions",
                title: "Action",
                className: "text-center",
                orderable: false,
                searchable: false,
                render: (_, __, row) => ActionButton(row),
            },
        ],
        buttons: createTableButtons({
            tableName: generalConfig.tableName,
            exportColumns: generalConfig.exportColumns,
            bulkDeleteHandler: handleBulkDelete,
            bulkDeleteButtonId: generalConfig.bulkDeleteButtonId,
            includeCreateButton: true,
            createButtonId: generalConfig.createButtonId,
            createButtonText: generalConfig.createButtonText,
            createHandler: () => {
                showModal(`${generalConfig.urlWeb}create`, "create");
            },
        }),
        pageLength: generalConfig.pageLength,
        lengthMenu: generalConfig.lengthMenu,
        order: generalConfig.defaultOrder,
        ordering: true,
        searching: true,
        columnSearch: true,
        paging: true,
        select: true,
        responsive: true,
        theme: "auto",
        onSelectionChange: handleSelectionChange,
        onError: (err) => console.error("❌ Table error:", err),
    };
}

// Filter Handlers
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

function handleDateRangeFilter(dateRange, table) {
    if (dateRange) {
        setCustomFilter({
            start_date: dateRange.start,
            end_date: dateRange.end,
        });

        // Update display text
        const dateText = document.querySelector(
            generalConfig.dateRangeTextSelector
        );
        if (dateText) {
            dateText.textContent = dateRange.formatted;
        }
    } else {
        resetToDefault();
    }
    table.reload();
}

// Initialize Application
function initializeBadgeConfigsTable() {
    const config = createTableConfig();
    const table = new ModernTable(generalConfig.tableId, {
        ...config,
        initComplete: (data, meta) => {
            console.log(
                generalConfig.dataLoadedMessage,
                data.length,
                "records"
            );
        },
    });

    // Initialize Month/Year Filter
    filterData((bulan, tahun) => {
        handleMonthYearFilter(bulan, tahun, table);
    });

    // Initialize Date Range Filter
    new DateRangePicker(generalConfig.dateRangeSelector, {
        buttonText: generalConfig.dateRangeButtonText,
        onDateSelect: (dateRange) => {
            handleDateRangeFilter(dateRange, table);
        },
    });

    return table;
}

// Setup Event Listeners
function setupEventListeners(tableInstance) {
    Delete({
        buttonSelector: generalConfig.deleteButtonClass,
        deleteUrl: generalConfig.urlApi + "/",
        tableSelector: generalConfig.tableId,
        onDeleteSuccess: () => {
            tableInstance.reload();
            tableInstance.clearSelection();
        },
    });

    document.body.addEventListener("click", (e) => {
        const editBtn = e.target.closest(generalConfig.updateButtonClass);
        const showBtn = e.target.closest(generalConfig.showButtonClass);
        const saveBtn = e.target.matches(generalConfig.actionButtonSelector);
        const createBtn = e.target.closest(generalConfig.createButtonClass);

        if (createBtn) {
            e.preventDefault();
            showModal(`${generalConfig.urlWeb}create`, "create");
        } else if (editBtn) {
            e.preventDefault();
            const url = `${generalConfig.urlWeb}${editBtn.dataset.id}/edit`;
            showModal(url, "edit");
        } else if (showBtn) {
            e.preventDefault();
            const url = `${generalConfig.urlWeb}${showBtn.dataset.id}`;
            showModal(url, "show");
        } else if (saveBtn) {
            e.preventDefault();
            const form = document.querySelector(generalConfig.formSelector);
            if (form) {
                fetchAxios(
                    {
                        url: form.action,
                        method: form.method,
                        data: new FormData(form),
                    },
                    "simpan",
                    () => tableInstance.reload()
                );
            }
        }
    });
}

// Modal handlers for TomSelect
document.addEventListener("shown.bs.modal", async (e) => {
    const modal = e.target;

    (async () => {
        try {
            // ---- 1. Sisipkan CSS TomSelect (theme Bootstrap 5) hanya sekali ----
            if (!document.querySelector("link[data-tomselect]")) {
                const link = document.createElement("link");
                link.rel = "stylesheet";
                link.href =
                    "https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.css";
                link.dataset.tomselect = "true";
                document.head.appendChild(link);
            }

            // ---- 2. Load library JS TomSelect dari CDN ----
            await import(
                "https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"
            );

            // ---- 3. Inisialisasi setelah lib siap ----
            const dateFieldsElement = modal.querySelector(".date-fields-select");
            if (dateFieldsElement && !dateFieldsElement.tomselect) {
                new TomSelect(dateFieldsElement, {
                    create: true,
                    maxItems: null, // null = multiple tanpa batas
                    placeholder: "Pilih atau ketik field tanggal...",
                    
                    // Callback saat user create item baru
                    onCreate: function(input, callback) {
                        // Validasi input (opsional)
                        if (!input || input.trim().length < 2) {
                            callback();
                            return;
                        }
                        
                        const newField = input.trim();
                        
                        // Bisa tambah validasi format field name
                        if (!/^[a-zA-Z_][a-zA-Z0-9_]*$/.test(newField)) {
                            showToast('Field name harus format valid (contoh: created_at, registration_date)', 'error');
                            callback();
                            return;
                        }
                        
                        // Create item baru
                        callback({
                            value: newField,
                            text: newField
                        });
                        
                        // Custom field akan tersimpan saat form di-save
                        
                        showToast(`Field '${newField}' berhasil ditambahkan`, 'success');
                    },
                    
                    // Render option
                    render: {
                        option: function(data, escape) {
                            return `<div>${escape(data.text)}</div>`;
                        },
                        item: function(data, escape) {
                            return `<div>${escape(data.text)}</div>`;
                        }
                    }
                });
            }
        } catch (error) {
            console.error("Error loading select:", error);
        }
    })();

});

// Start Application
document.addEventListener("DOMContentLoaded", () => {
    const tableInstance = initializeBadgeConfigsTable();
    setupEventListeners(tableInstance);
});

// Export default function untuk dynamic loading
export default function initBadgeConfigsModule() {

    if (document.readyState === "loading") {
        return;
    } else {
        const tableInstance = initializeBadgeConfigsTable();
        setupEventListeners(tableInstance);
    }
}
