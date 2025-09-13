/**
 * Create table buttons configuration for ModernTable
 * @param {Object} config - Configuration object
 * @param {string} config.tableName - Name of the table (e.g., 'users', 'roles')
 * @param {Array} config.exportColumns - Columns to export (default: ['name'])
 * @param {Function} config.bulkDeleteHandler - Handler for bulk delete action
 * @param {string} config.bulkDeleteButtonId - ID for bulk delete button (default: 'delete-selected-btn')
 * @param {boolean} config.includeBulkDelete - Whether to include bulk delete button (default: true)
 * @param {boolean} config.includeCreateButton - Whether to include create button (default: true)
 * @param {string} config.createButtonId - ID for create button (default: 'btnTambah')
 * @param {Function} config.createHandler - Handler for create action
 * @param {string} config.createButtonText - Text for create button (default: 'Tambah')
 * @param {Array} config.customButtons - Additional custom buttons
 */
export const createTableButtons = (config = {}) => {
    const {
        tableName = "items",
        exportColumns = ["name"],
        bulkDeleteHandler,
        bulkDeleteButtonId = "delete-selected-btn",
        includeBulkDelete = true,
        includeCreateButton = true,
        createButtonId = "btnTambah",
        createHandler,
        createButtonText = "Tambah",
        customButtons = [],
    } = config;

    const today = new Date().toISOString().split("T")[0];

    const buttons = [];

    // Add create button if enabled
    if (includeCreateButton) {
        buttons.push({
            id: createButtonId,
            text: `<i class="fas fa-plus"></i> ${createButtonText}`,
            className: "btn btn-primary btn-sm me-1 btnTambah",
            style: "display: none;", // Hidden by default, shown based on permissions
            action: createHandler || function() {
                console.log(`Create ${tableName} clicked`);
            },
        });
    }

    // Add standard buttons
    buttons.push(
        "copy",
        {
            extend: "csv",
            text: "Export",
            className: "btn btn-success btn-sm btn-csv",
            filename: `${tableName}-export-${today}`,
            exportColumns: exportColumns,
            titleAttr: "Export data as CSV file (Excel compatible)",
        },
        {
            extend: "pdf",
            text: "PDF",
            className: "btn btn-danger btn-sm btn-pdf",
            filename: `${tableName}-report-${today}`,
            orientation: "landscape",
            pageSize: "A4",
            exportColumns: [...exportColumns, "status"],
            titleAttr: "Export data as PDF file",
        },
        {
            extend: "print",
            text: "Print",
            className: "btn btn-warning btn-sm btn-print",
            orientation: "portrait",
            exportColumns: exportColumns,
            titleAttr: "Print selected columns with custom styling",
        },
        "colvis"
    );

    // Add bulk delete button if enabled
    if (includeBulkDelete && bulkDeleteHandler) {
        buttons.push({
            text: 'Delete Bulk (<span class="selected-count">0</span>)',
            className: "btn btn-danger btn-sm btn-delete",
            enabled: false,
            attr: {
                id: bulkDeleteButtonId,
                style: "display: none;",
            },
            action: bulkDeleteHandler,
        });
    }

    // Add custom buttons
    if (customButtons.length > 0) {
        buttons.push(...customButtons);
    }

    return buttons;
};

/**
 * Create standard export buttons only (without bulk delete)
 * @param {Object} config - Configuration object
 * @param {string} config.tableName - Name of the table
 * @param {Array} config.exportColumns - Columns to export
 */
export const createExportButtons = (config = {}) => {
    return createTableButtons({
        ...config,
        includeBulkDelete: false,
    });
};

/**
 * Create minimal buttons (copy, colvis only)
 * @param {Array} customButtons - Additional custom buttons
 */
export const createMinimalButtons = (customButtons = []) => {
    return ["copy", "colvis", ...customButtons];
};
