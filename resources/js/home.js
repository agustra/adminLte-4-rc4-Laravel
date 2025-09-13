import { ModernTable } from "https://cdn.jsdelivr.net/npm/modern-table-js@1.0.6/core/ModernTable.js";
import { ActionButton } from "@components/tables/ActionButton.js";
import {
    formatPermissionsColumn,
} from "@components/ui/permissionsPopup.js";
import filterData from "@helpers/filterData.js";
import DateRangePicker from "@helpers/DateRangePicker.js";

// Constants
const TODAY = new Date();
const TODAY_STRING =
    TODAY.getFullYear() +
    "-" +
    String(TODAY.getMonth() + 1).padStart(2, "0") +
    "-" +
    String(TODAY.getDate()).padStart(2, "0");

// Global State
let hasCustomFilter = false;
let currentFilters = {
    month: null,
    year: null,
    start_date: null,
    end_date: null,
    date: TODAY_STRING,
};

const generalConfig = {
    tableId: "#usersTable",
    urlWeb: "/admin/users/",
    urlApi: "/api/users",
    deleteMultipleUrl: "/api/users/multiple/delete/",
};

// Helper Functions
function getApiParams() {
    if (!hasCustomFilter) {
        return { date: TODAY_STRING };
    }

    const params = {};
    Object.entries(currentFilters).forEach(([key, value]) => {
        if (value !== null) params[key] = value;
    });
    return params;
}

function setCustomFilter(filters) {
    hasCustomFilter = true;
    currentFilters.date = null;
    Object.assign(currentFilters, filters);
}

function resetToDefault() {
    hasCustomFilter = false;
    currentFilters = {
        month: null,
        year: null,
        start_date: null,
        end_date: null,
        date: TODAY_STRING,
    };
}

function handleSelectionChange(selectedRows) {
    const deleteBtn = document.getElementById("delete-selected-btn");
    if (deleteBtn) {
        deleteBtn.style.display =
            selectedRows.length > 0 ? "inline-block" : "none";
        deleteBtn.disabled = selectedRows.length === 0;
        const countSpan = deleteBtn.querySelector(".selected-count");
        if (countSpan) countSpan.textContent = selectedRows.length;
    }
}

function handleBulkDelete(e, dt, node, config) {
    const selected = dt.getSelectedRows();
    if (
        selected.length > 0 &&
        confirm(`Delete ${selected.length} selected users?`)
    ) {
        fetch("/api/users/bulk-delete", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                )?.content,
            },
            body: JSON.stringify({
                ids: selected.map((row) => row.id),
            }),
        })
            .then((response) => response.json())
            .then(() => {
                alert(`Successfully deleted ${selected.length} users!`);
                dt.reload();
            })
            .catch((error) => {
                console.error("Delete error:", error);
                alert("Error deleting users!");
            });
    }
}

// Table Configuration
function createTableConfig() {
    const today = new Date().toISOString().split("T")[0];

    return {
        api: {
            url: generalConfig.urlApi,
            headers: {
                Authorization: "Bearer " + localStorage.getItem("token"),
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                )?.content,
            },
            beforeSend: (params) => Object.assign(params, getApiParams()),
        },

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
                render: (data, type, row) => {
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
                render: (data, type, row) => {
                    if (!row.roles || row.roles.length === 0) return "-";
                    return row.roles
                        .map(
                            (role) =>
                                `<span class="badge bg-primary me-1">${role.name}</span>`
                        )
                        .join("");
                },
            },
            {
                data: "permissions_count",
                title: "Permissions",
                orderable: false,
                render: (data, type, row) =>
                    formatPermissionsColumn(row.permissions || [], row, "user"),
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
                render: (data, type, row) => ActionButton(row),
            },
        ],

        buttons: [
            "copy",
            {
                extend: "csv",
                text: "Export",
                className: "btn btn-success btn-sm btn-csv",
                titleAttr: "Export data as CSV file (Excel compatible)",
                filename: `users-export-${today}`,
                exportColumns: ["name", "email"],
            },
            {
                extend: "pdf",
                text: "PDF",
                className: "btn btn-danger btn-sm btn-pdf",
                titleAttr: "Export data as PDF file",
                filename: `users-report-${today}`,
                orientation: "landscape",
                pageSize: "A4",
                exportColumns: ["name", "email", "status"],
            },
            {
                extend: "print",
                text: "Print",
                className: "btn btn-warning btn-sm btn-print",
                titleAttr: "Print selected columns with custom styling",
                exportColumns: ["name", "email"],
                orientation: "portrait",
            },
            "colvis",
            {
                text: 'Delete Bulk (<span class="selected-count">0</span>)',
                className: "btn btn-danger btn-sm btn-delete",
                enabled: false,
                attr: {
                    id: "delete-selected-btn",
                    style: "display: none;",
                },
                action: handleBulkDelete,
            },
        ],

        // Table Features
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        ordering: true,
        searching: true,
        columnSearch: true,
        paging: true,
        select: true,
        responsive: true,

        // Modern Features
        theme: "auto",
        keyboard: false,
        accessibility: true,

        // State Management
        stateSave: true,
        stateDuration: 3600,

        onSelectionChange: handleSelectionChange,
        onError: (error) => console.error("❌ Table error:", error),
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
            "#idFilterDateRange .date-range-text"
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
            console.log("✅ Data loaded:", data.length, "records");
        },
        onRowClick: (row, event) => {
            console.log("👆 Row clicked:", row);
        },
    });

    // Initialize Month/Year Filter
    filterData((bulan, tahun) => {
        handleMonthYearFilter(bulan, tahun, table);
    });

    // Initialize Date Range Filter
    new DateRangePicker("#idFilterDateRange", {
        buttonText: "Filter Tanggal",
        onDateSelect: (dateRange) => {
            handleDateRangeFilter(dateRange, table);
        },
    });

    return table;
}

// Start Application
document.addEventListener("DOMContentLoaded", () => {
    initializeUsersTable();
});
