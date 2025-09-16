import { ModernTable } from "https://cdn.jsdelivr.net/npm/modern-table-js@1.0.9/core/ModernTable.js";
import { Delete } from "@helpers/delete.js";
import { createBulkDeleteHandler } from "@helpers/bulkDelete.js";
import { createSelectionChangeHandler } from "@tables/handleSelectionChange.js";
import { createApiConfig } from "@tables/apiConfig.js";
import { handlePermissionButtons } from "@tables/handlePermissionButtons.js";
import { createTableButtons } from "@tables/tableButtons.js";
import { ActionButton } from "@tables/ActionButton.js";
import filterData from "@helpers/filterData.js";
import DateRangePicker from "@helpers/DateRangePicker.js";
import { showModal } from "@handlers/modalHandler.js";
import { fetchAxios } from "@handlers/fetchAxios.js";
import { formatDate } from "@utils/formatDate.js";

// Permission Components
import { initializeRolesTomSelect } from "@tomselect/rolesSelect.js";

// ============================
// Constants
// ============================
const DEFAULT_FILTERS = {
    month: null,
    year: null,
    start_date: null,
    end_date: null,
};

const generalConfig = {
    // Table & URLs
    tableId: "#table-permissions",
    urlWeb: "/admin/permissions/",
    urlApi: "/api/permissions",
    deleteMultipleUrl: "/api/permissions/multiple/delete",

    // Table Configuration
    tableName: "permissions",
    exportColumns: ["name"],
    createButtonText: "Add Permission",
    itemName: "permissions",

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
    if (!hasCustomFilter) return {};
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
        console.log(`ℹ️ Selection changed: ${count} permissions selected`);
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
            "permissions deleted"
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
            console.error("Permissions API Error:", { error, status, message });
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
            { data: "name", title: "Permission Name", orderable: true },
            { data: "roles", title: "Roles Name", orderable: true },
            {
                data: "created_at",
                title: "Created",
                orderable: true,
                render: (data) => new Date(data).toLocaleDateString("id-ID"),
            },
            {
                data: "actions",
                title: "Action",
                className: "text-center",
                orderable: false,
                searchable: false,
                render: (_, __, row) => ActionButton(row),
            },
        ],

        // Create buttons using component
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

        // Features
        serverSide: true,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        ordering: true,
        searching: true,
        columnSearch: true,
        paging: true,
        select: true,
        responsive: true,

        // UX
        theme: "auto",
        keyboard: false,
        accessibility: true,

        // State
        stateSave: true,
        stateDuration: 3600,

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
function initializePermissionsTable() {
    const config = createTableConfig();

    const table = new ModernTable(generalConfig.tableId, {
        ...config,
        initComplete: (data, meta) => {
            console.log("✅ Data loaded:", data.length, "records");
        },
    });

    // Initialize Month/Year Filter
    filterData((bulan, tahun) => {
        handleMonthYearFilter(bulan, tahun, table);
    });

    // Initialize Date Range Filter
    new DateRangePicker(generalConfig.dateRangeSelector, {
        buttonText: "Filter Tanggal",
        onDateSelect: (dateRange) => {
            handleDateRangeFilter(dateRange, table);
        },
    });

    return table;
}

// Setup Event Listeners
function setupEventListeners(tableInstance) {
    // Delete functionality
    Delete({
        buttonSelector: generalConfig.deleteButtonClass,
        deleteUrl: generalConfig.urlApi + "/",
        tableSelector: generalConfig.tableId,
        onDeleteSuccess: () => {
            tableInstance.reload();
            tableInstance.clearSelection();
        },
    });

    // Event listeners
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

// Start Application
document.addEventListener("DOMContentLoaded", () => {
    // Initialize table first
    const tableInstance = initializePermissionsTable();

    // Setup event listeners dan handlers
    setupEventListeners(tableInstance);
});

// Modal handlers
document.addEventListener("shown.bs.modal", async (e) => {
    const modal = e.target;

    // TomSelect untuk roles (role-based only)
    try {
        setTimeout(() => {
            const rolesElement = modal.querySelector("#role");
            if (rolesElement) {
                initializeRolesTomSelect(rolesElement);
            }
        }, 100);
    } catch (error) {
        console.error("Error loading roles select:", error);
    }
});

// Export default function untuk dynamic loading
export default function initPermissionsModule() {
    console.log("🔐 Permissions module initialized successfully!");

    // Jika DOM sudah ready, langsung jalankan
    if (document.readyState === "loading") {
        // DOM masih loading, tunggu DOMContentLoaded
        return;
    } else {
        // DOM sudah ready, manual trigger karena DOMContentLoaded sudah lewat
        const tableInstance = initializePermissionsTable();

        // Setup event listeners dan handlers lainnya
        setupEventListeners(tableInstance);
    }
}
