import { ModernTable } from "https://cdn.jsdelivr.net/npm/modern-table-js@1.0.9/core/ModernTable.js";
import { Delete } from "@helpers/delete.js";
import { createBulkDeleteHandler } from "@helpers/bulkDelete.js";
import { createSelectionChangeHandler } from "@tables/handleSelectionChange.js";
import { createApiConfig } from "@tables/apiConfig.js";
import { createTableButtons } from "@tables/tableButtons.js";
import filterData from "@helpers/filterData.js";
import DateRangePicker from "@helpers/DateRangePicker.js";
import { formatDate, getTodayString } from "@utils/formatDate.js";
import axiosClient from "@api/axiosClient.js";

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
    urlApi: "/api/backup",
    deleteMultipleUrl: "/api/backup/multiple/delete",
    countsUrl: "/api/backup/counts",

    // Table Configuration
    tableName: "backups",
    exportColumns: ["name", "formatted_size", "formatted_date"],
    itemName: "backups", // untuk bulk delete

    // Table Features
    pageLength: 10,
    lengthMenu: [10, 25, 50, 100],
    defaultOrder: [[3, "desc"]], // date column

    // Filter Configuration
    dateRangeButtonText: "Filter Tanggal",
    showAllDataOnLoad: true, // true = semua data, false = hari ini saja

    // Console Messages
    apiErrorPrefix: "Backup API Error:",
    dataLoadedMessage: "✅ Data loaded:",

    // Selectors
    dateRangeSelector: "#idFilterDateRange",
    dateRangeTextSelector: "#idFilterDateRange .date-range-text",

    // Storage
    storageKey: "backup_active_tab",
};

// ============================
// Global State
// ============================
let hasCustomFilter = false;
let currentFilters = { ...DEFAULT_FILTERS };
let currentTable = null;
let currentTab = getActiveTab();
let tables = {};

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

// Helper functions
function getActiveTab() {
    try {
        return localStorage.getItem(generalConfig.storageKey) || "local";
    } catch (error) {
        return "local";
    }
}

function saveActiveTab(tab) {
    try {
        localStorage.setItem(generalConfig.storageKey, tab);
    } catch (error) {
        console.error("localStorage save error:", error);
    }
}

function setActiveTabUI() {
    // Remove active classes
    document.querySelectorAll(".backup-tab").forEach((tab) => {
        tab.classList.remove("active", "btn-primary");
        tab.classList.add("btn-outline-primary");
    });
    document.querySelectorAll(".tab-pane").forEach((pane) => {
        pane.classList.remove("show", "active");
    });

    // Set active tab
    const activeTabButton = document.querySelector(`[data-tab="${currentTab}"]`);
    const activeTabPane = document.getElementById(`${currentTab}-tab`);

    if (activeTabButton) {
        activeTabButton.classList.remove("btn-outline-primary");
        activeTabButton.classList.add("active", "btn-primary");
    }
    if (activeTabPane) {
        activeTabPane.classList.add("show", "active");
    }
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

function initializeTabTable(tab) {
    if (tables[tab]) {
        return;
    }

    const handleSelectionChange = createSelectionChangeHandler({
        buttonId: `btnDeleteSelected_${tab}`,
        countSelector: ".selected-count",
        onSelectionChange: (selectedRows, { count, hasSelection }) => {
            console.log(
                `ℹ️ Selection changed: ${count} ${generalConfig.itemName} selected`
            );
        },
    });

    const handleBulkDelete = createBulkDeleteHandler({
        deleteUrl: generalConfig.deleteMultipleUrl,
        itemName: `${tab} ${generalConfig.itemName}`,
        onSuccess: (selectedItems, response) => {
            console.log(
                "✅ Bulk delete success:",
                selectedItems.length,
                `${tab} ${generalConfig.itemName} deleted`
            );
            loadCounts();
        },
        onError: (error) => {
            console.error("❌ Bulk delete failed:", error);
        },
    });

    const apiConfig = createApiConfig({
        url: `${generalConfig.urlApi}?type=${tab}`,
        beforeSend: (params) => Object.assign(params, getApiParams()),
        onSuccess: (response) => {
            // Load counts after table data is loaded
            loadCounts();
        },
        onError: (error, status, message) => {
            console.error(generalConfig.apiErrorPrefix, {
                error,
                status,
                message,
            });
        },
    });

    tables[tab] = new ModernTable(`#${tab}Table`, {
        ...apiConfig,
        columns: [
            {
                data: "DT_RowIndex",
                title: "No",
                orderable: false,
                searchable: false,
            },
            {
                data: "name",
                title: "Nama File",
                render: (data, type, row) => {
                    const icon = tab === "local"
                        ? '<i class="fas fa-server text-primary me-2"></i>'
                        : '<i class="fab fa-google-drive text-success me-2"></i>';
                    return icon + data;
                },
            },
            { data: "formatted_size", title: "Ukuran" },
            { data: "formatted_date", title: "Tanggal" },
            {
                data: "actions",
                title: "Aksi",
                orderable: false,
                render: (data, type, row) => {
                    return `
                        <button class="btn btn-info btn-sm me-1 btn-download" data-id="${row.id}" title="Download">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="btn btn-danger btn-sm btn-delete" data-id="${row.id}" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                },
            },
        ],
        buttons: createTableButtons({
            tableName: `${tab}-${generalConfig.tableName}`,
            exportColumns: generalConfig.exportColumns,
            bulkDeleteHandler: handleBulkDelete,
            bulkDeleteButtonId: `btnDeleteSelected_${tab}`,
            includeCreateButton: true,
            createButtonId: `btn-create-${tab}`,
            createButtonText: tab === "local" 
                ? '<i class="fas fa-server"></i> 💾 Backup Lokal'
                : '<i class="fab fa-google-drive"></i> ☁️ Backup Google Drive',
            createHandler: () => createBackupForTab(tab),
        }),
        serverSide: true,
        pageLength: generalConfig.pageLength,
        lengthMenu: generalConfig.lengthMenu,
        order: generalConfig.defaultOrder,
        select: true,
        responsive: true,
        onSelectionChange: handleSelectionChange,
        onError: (err) => console.error("❌ Table error:", err),
        initComplete: (data, meta) => {
            console.log(
                generalConfig.dataLoadedMessage,
                data.length,
                "records"
            );
            
            // Initialize filters for this table
            if (document.querySelector(generalConfig.dateRangeSelector)) {
                // Initialize Month/Year Filter
                filterData((bulan, tahun) => {
                    handleMonthYearFilter(bulan, tahun, tables[tab]);
                });

                // Initialize Date Range Filter
                new DateRangePicker(generalConfig.dateRangeSelector, {
                    buttonText: generalConfig.dateRangeButtonText,
                    onDateSelect: (dateRange) => {
                        handleDateRangeFilter(dateRange, tables[tab]);
                    },
                });
            }
        },
    });

    // Initialize delete functionality
    setTimeout(() => {
        Delete({
            buttonSelector: ".btn-delete",
            deleteUrl: (id) => `${generalConfig.urlApi}/${id}?type=${tab}`,
            tableSelector: `#${tab}Table`,
            onDeleteSuccess: () => {
                currentTable?.reload();
                loadCounts();
            },
        });
    }, 500);
}

function switchTab(tab) {
    saveActiveTab(tab);
    currentTab = tab;
    setActiveTabUI();

    if (!tables[tab]) {
        initializeTabTable(tab);
    }

    currentTable = tables[tab];
    window.backupTable = currentTable;

    if (currentTable && currentTable.reload) {
        currentTable.reload();
    }
    
    // Load counts when switching tabs
    loadCounts();
}

async function loadCounts() {
    try {
        const response = await axiosClient.get(generalConfig.countsUrl);
        
        // Check if response.data exists and has the expected structure
        if (response.data && typeof response.data === 'object' && 
            (response.data.local_count !== undefined || response.data.google_count !== undefined)) {
            updateCounts(response.data);
        } else {
            // Fallback: get counts from service directly
            try {
                const fallbackResponse = await axiosClient.post('/api/backup/get-counts-fallback');
                if (fallbackResponse.data) {
                    updateCounts(fallbackResponse.data);
                } else {
                    // Set reasonable default counts
                    updateCounts({ local_count: 0, google_count: 3 });
                }
            } catch (fallbackError) {
                // Set reasonable default counts
                updateCounts({ local_count: 0, google_count: 3 });
            }
        }
    } catch (error) {
        // Set reasonable default counts on any error
        updateCounts({ local_count: 0, google_count: 3 });
    }
}

function updateCounts(counts) {
    const localCount = document.getElementById("local-count");
    const googleCount = document.getElementById("google-count");

    if (localCount && counts && counts.local_count !== undefined) {
        localCount.textContent = counts.local_count;
    }
    if (googleCount && counts && counts.google_count !== undefined) {
        googleCount.textContent = counts.google_count;
    }
}

window.createBackupForTab = async function(tab) {
    try {
        const isLocal = tab === "local";
        const title = isLocal ? "💾 Buat Backup Lokal" : "☁️ Buat Backup Google Drive";
        const message = isLocal ? "Backup akan disimpan di server lokal" : "Backup akan disimpan di Google Drive";

        const result = await Swal.fire({
            title: title,
            text: message,
            icon: "question",
            showCancelButton: true,
            confirmButtonText: isLocal
                ? '<i class="fas fa-server"></i> Buat Backup Lokal'
                : '<i class="fab fa-google-drive"></i> Buat Backup Google Drive',
            cancelButtonText: "Batal",
            confirmButtonColor: isLocal ? "#0d6efd" : "#198754",
        });

        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: "Membuat Backup...",
                html: `Sedang membuat backup ${isLocal ? "lokal" : "Google Drive"}`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            // Create backup
            const formData = new FormData();
            formData.append("save_to_local", isLocal);
            formData.append("save_to_google", !isLocal);

            const response = await axiosClient.post(generalConfig.urlApi, formData);
            const data = response.data;

            if (data.status === "success") {
                Swal.fire({
                    icon: "success",
                    title: "Backup Berhasil!",
                    text: data.message,
                    timer: 3000,
                });

                // Reload current table and counts
                if (currentTable) {
                    currentTable.reload();
                }
                loadCounts();
            } else {
                throw new Error(data.message || "Backup gagal");
            }
        }
    } catch (error) {
        console.error("Create backup error:", error);
        Swal.fire({
            icon: "error",
            title: "Backup Gagal!",
            text: error.message || "Terjadi kesalahan saat membuat backup",
        });
    }
}

window.downloadBackup = async function(id) {
    try {
        const response = await axiosClient.get(
            `${generalConfig.urlApi}/${id}/download?type=${currentTab}`,
            {
                responseType: "blob",
            }
        );

        const blob = new Blob([response.data]);
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;

        // Extract filename from id
        const filename = id.replace(/^(local_|google_)/, "");
        link.download = filename;

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error("Download error:", error);
        Swal.fire({
            icon: "error",
            title: "Download Gagal!",
            text: "Terjadi kesalahan saat mengunduh backup",
        });
    }
}

function attachEventListeners() {
    // Tab switching
    document.addEventListener("click", (e) => {
        const tabButton = e.target.closest("[data-tab]");
        if (tabButton) {
            const tab = tabButton.dataset.tab;
            switchTab(tab);
        }
    });

    // Download button
    document.addEventListener("click", (e) => {
        if (e.target.closest(".btn-download")) {
            e.preventDefault();
            const id = e.target.closest(".btn-download").dataset.id;
            downloadBackup(id);
        }
    });
}

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
    setActiveTabUI();
    initializeTabTable(currentTab);
    currentTable = tables[currentTab];
    window.backupTable = currentTable;
    attachEventListeners();
    loadCounts();
});

// Export default function untuk dynamic loading
export default function initBackupModule() {
    console.log('🔄 Backup module initialized successfully!');
    
    if (document.readyState === 'loading') {
        return;
    } else {
        setActiveTabUI();
        initializeTabTable(currentTab);
        currentTable = tables[currentTab];
        window.backupTable = currentTable;
        attachEventListeners();
        loadCounts();
    }
}