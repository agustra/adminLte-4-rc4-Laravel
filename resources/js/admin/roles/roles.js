import { ModernTable } from "https://cdn.jsdelivr.net/npm/modern-table-js@1.0.6/core/ModernTable.js";
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
// Role Components
import {
    initializeRolePermissionsTomSelect,
    updateRolePermissionBadges,
} from "@tomselect/rolePermissionsSelect.js";

import {
    formatPermissionsColumn,
    initializePermissionsPopup,
} from "@components/ui/permissionsPopup.js";

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
    tableId: "#table-roles",
    urlWeb: "/admin/roles",
    urlApi: "/api/roles",
    deleteMultipleUrl: "/api/roles/multiple/delete",

    // Table Configuration toolbar-center
    tableName: "roles",
    exportColumns: ["name", "permissions"],
    createButtonText: "Add Roles",
    itemName: "roles", // untuk bulk delete

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
    if (!hasCustomFilter) return {}; // ✅ Tidak kirim filter, tampilkan semua data

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
        console.log(`ℹ️ Selection changed: ${count} roles selected`);
    },
});

// Create bulk delete handler using component
const handleBulkDelete = createBulkDeleteHandler({
    deleteUrl: generalConfig.deleteMultipleUrl,
    itemName: "roles",
    onSuccess: (selectedItems, response) => {
        console.log(
            "✅ Bulk delete success:",
            selectedItems.length,
            "roles deleted"
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
            console.error("Roles API Error:", { error, status, message });
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
            { data: "name", title: "Name", orderable: true },
            {
                data: "permissions",
                title: "Permissions",
                orderable: true,
                searchable: true,
                render: (_, __, row) => {
                    // Convert permissions string to array if needed
                    let permissions = row?.permissions ?? [];
                    if (typeof permissions === "string") {
                        permissions = permissions
                            .split(",")
                            .map((p) => p.trim())
                            .filter((p) => p);
                    }
                    return formatPermissionsColumn(permissions, row, "role"); // /api/roles/{id}/permissions/paginated
                },
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
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        order: [[0, "desc"]], // name column
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

// Permission buttons handler - see @tables/handlePermissionButtons.js for full documentation

// Initialize Application
function initializeRolesTable() {
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
function setupEventListeners(table) {
    // Delete functionality
    Delete({
        buttonSelector: generalConfig.deleteButtonClass,
        deleteUrl: generalConfig.urlApi + "/",
        tableSelector: generalConfig.tableId,
        onDeleteSuccess: () => {
            table.reload();
            table.clearSelection();
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
                    () => table.reload()
                );
            }
        }
    });
}

// Start Application
document.addEventListener("DOMContentLoaded", () => {
    // Initialize table first
    const table = initializeRolesTable();

    // Setup event listeners
    setupEventListeners(table);

    // Initialize permissions popup once with event delegation
    initializePermissionsPopup();

    // Modal listener - cleanup and attach once
    if (!window.rolesModalListenerAttached) {
        const modalHandler = function (e) {
            // Only handle roles modal
            if (e.target.id !== "modalAction") return;

            console.log("Roles modal shown!");
            // Initialize TomSelect for permissions
            initializeRolePermissionsTomSelect(e);
            updateRolePermissionBadges(e);
        };

        document.addEventListener("shown.bs.modal", modalHandler);
        window.rolesModalListenerAttached = true;
        window.rolesModalHandler = modalHandler;
    }
});

// Export default function untuk dynamic loading
export default function initRolesModule() {
    if (document.readyState === "loading") {
        return;
    } else {
        const table = initializeRolesTable();
        setupEventListeners(table);

        // Initialize permissions popup
        initializePermissionsPopup();

        // Setup modal handlers
        if (!window.rolesModalListenerAttached) {
            const modalHandler = function (e) {
                if (e.target.id !== "modalAction") return;
                console.log("Roles modal shown!");
                initializeRolePermissionsTomSelect(e);
                updateRolePermissionBadges(e);
            };
            document.addEventListener("shown.bs.modal", modalHandler);
            window.rolesModalListenerAttached = true;
            window.rolesModalHandler = modalHandler;
        }
    }
}
