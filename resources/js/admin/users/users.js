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
import {
    formatPermissionsColumn,
    initializePermissionsPopup,
} from "@components/ui/permissionsPopup.js";
import { showModal } from "@handlers/modalHandler.js";
import { fetchAxios } from "@handlers/fetchAxios.js";
import { formatDate, getTodayString } from "@utils/formatDate.js";

// User Components
import "./avatar.js";

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
    tableId: "#table-users",
    urlWeb: "/admin/users",
    urlApi: "/api/users",
    deleteMultipleUrl: "/api/users/multiple/delete",

    // Table Configuration
    tableName: "users",
    exportColumns: ["name", "email"],
    createButtonText: "Add User",
    itemName: "users", // untuk bulk delete

    // Table Features
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50],
    defaultOrder: [[6, "desc"]], // created_at column

    // Filter Configuration
    dateRangeButtonText: "Filter Tanggal",
    showAllDataOnLoad: true, // true = semua data, false = hari ini saja

    // Console Messages
    apiErrorPrefix: "Users API Error:",
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
            {
                data: "avatar_url",
                title: "Avatar",
                orderable: false,
                searchable: false,
                render: (_, __, row) => {
                    const avatarUrl =
                        row.avatar_url || "/avatar/avatar-default.jpg";
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
                data: "permissions",
                title: "Permissions",
                orderable: false,
                render: (_, __, row) =>
                    formatPermissionsColumn(row.permissions ?? [], row, "user"), // /api/users/{id}/permissions/paginated
            },
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
        pageLength: generalConfig.pageLength,
        lengthMenu: generalConfig.lengthMenu,
        order: generalConfig.defaultOrder,
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
function initializeUsersTable() {
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

// Setup Modal Handlers
function setupModalHandlers() {
    // Modal handlers
    document.addEventListener("shown.bs.modal", async (e) => {
        const modal = e.target;

        // TomSelect untuk roles (role-based only)
        try {
            const { initializeRolesTomSelect } = await import(
                "@tomselect/rolesSelect.js"
            );
            setTimeout(() => {
                const rolesElement =
                    modal.querySelector("#roles") ||
                    modal.querySelector(".role-select");
                if (rolesElement) {
                    const tomSelectInstance =
                        initializeRolesTomSelect(rolesElement);

                    // Listen for role changes to update permissions display
                    if (tomSelectInstance && tomSelectInstance.ts) {
                        tomSelectInstance.ts.on("change", function () {
                            updatePermissionDisplay(this.getValue());
                        });

                        // Initial load for edit mode
                        setTimeout(() => {
                            const selectedRoles =
                                tomSelectInstance.ts.getValue();
                            if (selectedRoles && selectedRoles.length > 0) {
                                updatePermissionDisplay(selectedRoles);
                            }
                        }, 300);
                    }
                }
            }, 100);
        } catch (error) {
            console.error("Error loading roles select:", error);
        }
    });

    // Update permission display based on selected roles
    async function updatePermissionDisplay(selectedRoleIds) {
        const permissionBadges = document.getElementById("permission-badges");
        if (!permissionBadges) return;

        if (!selectedRoleIds || selectedRoleIds.length === 0) {
            permissionBadges.innerHTML =
                '<span class="badge bg-secondary">Pilih role untuk melihat permissions</span>';
            return;
        }

        try {
            // Import axiosClient
            const { default: axiosClient } = await import(
                "@api/axiosClient.js"
            );

            // Show loading
            permissionBadges.innerHTML =
                '<span class="badge bg-info"><i class="fa fa-spinner fa-spin"></i> Loading permissions...</span>';

            // Fetch role permissions
            const response = await axiosClient.get(
                `/api/roles/by-ids?ids=${selectedRoleIds.join(
                    ","
                )}&include_permissions=true`
            );
            const data = response.data;

            if (data.permissions && data.permissions.length > 0) {
                // Get unique permissions
                const uniquePermissions = [...new Set(data.permissions)];

                // Fetch permission details
                const permResponse = await axiosClient.get(
                    `/api/permissions/by-ids?ids=${uniquePermissions.join(",")}`
                );
                const permData = permResponse.data;

                if (permData.data && permData.data.length > 0) {
                    permissionBadges.innerHTML =
                        permData.data
                            .map(
                                (permission) =>
                                    `<span class="badge bg-primary me-1 mb-1">${permission.name}</span>`
                            )
                            .join("") +
                        `<small class="text-muted ms-2">(${permData.data.length} permissions)</small>`;
                } else {
                    permissionBadges.innerHTML =
                        '<span class="badge bg-warning">No permissions found</span>';
                }
            } else {
                permissionBadges.innerHTML =
                    '<span class="badge bg-warning">Role tidak memiliki permissions</span>';
            }
        } catch (error) {
            console.error("Error fetching permissions:", error);
            permissionBadges.innerHTML =
                '<span class="badge bg-danger">Error loading permissions</span>';
        }
    }
}

// Start Application
document.addEventListener("DOMContentLoaded", () => {
    // Initialize table first
    const table = initializeUsersTable();

    // Initialize permissions popup once with event delegation
    initializePermissionsPopup();

    // Setup event listeners dan handlers
    setupEventListeners(table);
    setupModalHandlers();
});

// Export default function untuk dynamic loading
export default function initUsersModule() {
    // Jika DOM sudah ready, langsung jalankan
    if (document.readyState === "loading") {
        // DOM masih loading, tunggu DOMContentLoaded
        return;
    } else {
        // DOM sudah ready, manual trigger karena DOMContentLoaded sudah lewat
        const table = initializeUsersTable();

        // Initialize permissions popup
        initializePermissionsPopup();

        // Setup event listeners dan handlers lainnya
        setupEventListeners(table);
        setupModalHandlers();
    }
}
