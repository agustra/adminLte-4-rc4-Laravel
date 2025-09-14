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
import { initializePermissionsTomSelect } from "@tomselect/permissionsSelect.js";
import { initializeRolesTomSelect } from "@tomselect/rolesSelect.js";
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
    tableId: "#table-menus",
    urlWeb: "/admin/menus/",
    urlApi: "/api/menus",
    deleteMultipleUrl: "/api/menus/multiple/delete",

    // Table Configuration
    tableName: "menus",
    exportColumns: ["name", "url"],
    createButtonText: "Add Menu",
    itemName: "menus", // untuk bulk delete

    // Table Features
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50],
    defaultOrder: [[6, "desc"]], // created_at column

    // Filter Configuration
    dateRangeButtonText: "Filter Tanggal",
    showAllDataOnLoad: true, // true = semua data, false = hari ini saja

    // Console Messages
    apiErrorPrefix: "Menus API Error:",
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
// Create handlers
const handleSelectionChange = createSelectionChangeHandler({
    buttonId: generalConfig.bulkDeleteButtonId,
    countSelector: ".selected-count",
    onSelectionChange: (selectedRows, { count, hasSelection }) => {
        console.log(`ℹ️ Selection changed: ${count} menus selected`);
    },
});

const handleBulkDelete = createBulkDeleteHandler({
    deleteUrl: generalConfig.deleteMultipleUrl,
    itemName: generalConfig.itemName,
    onSuccess: (selectedItems, response) => {
        console.log(
            "✅ Bulk delete success:",
            selectedItems.length,
            "menus deleted"
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
            { data: "name", title: "Name", orderable: true },
            { data: "url", title: "URL", orderable: true },
            {
                data: "icon",
                title: "Icon",
                orderable: false,
                render: (data) =>
                    data ? `<i class="${data}"></i> ${data}` : "-",
            },
            { data: "permission", title: "Permission", orderable: true },
            {
                data: "roles",
                title: "Roles",
                orderable: false,
                render: (data, type, row) => {
                    // Handle different data types
                    if (!data) {
                        return '<span class="text-muted">All Roles</span>';
                    }

                    // If data is string (JSON), parse it
                    if (typeof data === "string") {
                        try {
                            data = JSON.parse(data);
                        } catch (e) {
                            return '<span class="text-muted">All Roles</span>';
                        }
                    }

                    // Check if it's array and has items
                    if (!Array.isArray(data) || data.length === 0) {
                        return '<span class="text-muted">All Roles</span>';
                    }

                    return data
                        .map(
                            (role) =>
                                `<span class="badge bg-info me-1">${role}</span>`
                        )
                        .join("");
                },
            },
            {
                data: "parent_name",
                title: "Parent",
                orderable: true,
                render: (data) => data || "-",
            },
            { data: "order", title: "Order", orderable: true },
            {
                data: "is_active",
                title: "Status",
                orderable: true,
                render: (data) =>
                    data == "aktif"
                        ? '<span class="badge bg-success">Aktif</span>'
                        : '<span class="badge bg-secondary">Inaktif</span>',
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
        order: [[1, "asc"]],
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
function initializeMenusTable() {
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

// Modal listener - only attach once
if (!window.menusModalListenerAttached) {
    document.addEventListener("shown.bs.modal", function (e) {
        console.log("Modal shown!");

        const modal = e.target;

        // Initialize TomSelect for permissions
        const permissionsSelect = modal.querySelector("#permission");
        initializePermissionsTomSelect(permissionsSelect, {
            multiple: false,
            grouped: true,
            createSingle: true,
        });

        // Initialize TomSelect for roles
        const rolesSelect = modal.querySelector("#roles");
        if (rolesSelect) {
            initializeRolesTomSelect(rolesSelect, {
                plugins: ["remove_button"],
                placeholder: "Pilih roles (kosongkan untuk semua role)",
                allowEmptyOption: true,
                multiple: true,
                create: false,
            });
        }

        // Initialize Icon Picker
        initializeIconPicker(e);
    });
    window.menusModalListenerAttached = true;
}

// ===== GLOBAL ICON PICKER FUNCTIONS =====
window.toggleIconPicker = function () {
    const iconPicker = document.getElementById("icon-picker");
    if (!iconPicker) return;

    const isVisible = iconPicker.style.display !== "none";
    iconPicker.style.display = isVisible ? "none" : "block";

    if (!isVisible) {
        window.populatePopularIcons();
    }
};

window.populatePopularIcons = function () {
    const popularIcons = [
        "fas fa-home",
        "fas fa-user",
        "fas fa-users",
        "fas fa-cog",
        "fas fa-chart-bar",
        "fas fa-file",
        "fas fa-folder",
        "fas fa-edit",
        "fas fa-trash",
        "fas fa-plus",
        "fas fa-search",
        "fas fa-bell",
        "fas fa-envelope",
        "fas fa-calendar",
        "fas fa-clock",
        "fas fa-heart",
        "fas fa-star",
        "fas fa-bookmark",
        "fas fa-tag",
        "fas fa-shopping-cart",
    ];

    const container = document.getElementById("popular-icons");
    if (!container) return;

    container.innerHTML = "";

    popularIcons.forEach((icon) => {
        const col = document.createElement("div");
        col.className = "col-2 text-center mb-2";
        col.innerHTML = `
            <button type="button" class="btn btn-sm btn-outline-secondary" 
                    data-icon="${icon}" title="${icon}">
                <i class="${icon}"></i>
            </button>
        `;

        const iconBtn = col.querySelector("button");
        iconBtn.addEventListener("click", function () {
            window.selectIcon(icon);
        });

        container.appendChild(col);
    });
};

window.selectIcon = function (iconClass) {
    const iconInput = document.getElementById("icon");
    const iconPreview = document.getElementById("icon-preview");
    const iconPicker = document.getElementById("icon-picker");

    if (iconInput) iconInput.value = iconClass;
    if (iconPreview) iconPreview.className = iconClass;
    if (iconPicker) iconPicker.style.display = "none";
};

// ===== ICON PICKER FUNCTIONS =====
function initializeIconPicker(e) {
    const modal = e.target;

    // Icon picker button click
    const iconPickerBtn = modal.querySelector("#icon-picker-btn");
    if (iconPickerBtn) {
        iconPickerBtn.addEventListener("click", function (event) {
            event.preventDefault();
            window.toggleIconPicker();
        });
    }

    // Icon input change for real-time preview
    const iconInput = modal.querySelector("#icon");
    if (iconInput) {
        iconInput.addEventListener("input", function () {
            const preview = modal.querySelector("#icon-preview");
            if (preview) {
                preview.className = this.value || "fas fa-home";
            }
        });
    }
}

// Start Application
document.addEventListener("DOMContentLoaded", () => {
    const tableInstance = initializeMenusTable();
    setupEventListeners(tableInstance);
});

// Export default function untuk dynamic loading
export default function initMenusModule() {
    console.log("🔗 Menus module initialized successfully!");

    if (document.readyState === "loading") {
        return;
    } else {
        const tableInstance = initializeMenusTable();
        setupEventListeners(tableInstance);
    }
}
