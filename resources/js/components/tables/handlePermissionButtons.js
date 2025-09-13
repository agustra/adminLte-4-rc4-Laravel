/**
 * ========================================
 * HANDLE PERMISSION BUTTONS COMPONENT
 * ========================================
 * 
 * Component untuk mengelola visibility dan state button berdasarkan user permissions.
 * Permissions didapat dari API response meta.permissions yang di-generate oleh backend.
 * 
 * FLOW:
 * -----
 * 1. Backend: generatePermissions() → check user permissions
 * 2. API Response: { meta: { permissions: { create: true, delete: true, ... } } }
 * 3. Frontend: handlePermissionButtons(permissions) → show/hide buttons
 * 
 * USAGE EXAMPLES:
 * ===============
 * 
 * 1. DEFAULT (Recommended - 90% cases):
 * -------------------------------------
 * import { handlePermissionButtons } from "@tables/handlePermissionButtons.js";
 * 
 * onSuccess: (response) => {
 *     const permissions = response?.meta?.permissions;
 *     if (permissions) handlePermissionButtons(permissions);
 * }
 * 
 * 2. CUSTOM CONFIGURATION:
 * ------------------------
 * import { createPermissionButtonsHandler } from "@tables/handlePermissionButtons.js";
 * 
 * const handlePermissionButtons = createPermissionButtonsHandler({
 *     createButtonId: 'btnAddProduct',
 *     deleteButtonId: 'bulk-delete-products',
 *     customButtons: [
 *         { buttonId: 'btnExport', permission: 'export' },
 *         { buttonId: 'btnImport', permission: 'import' },
 *         { buttonId: 'btnArchive', permission: 'archive' }
 *     ],
 *     onPermissionUpdate: (permissions) => {
 *         console.log('Custom logic:', permissions);
 *     }
 * });
 * 
 * 3. STANDARD WITH LOGGING:
 * -------------------------
 * import { createStandardPermissionHandler } from "@tables/handlePermissionButtons.js";
 * 
 * const handlePermissionButtons = createStandardPermissionHandler('products');
 * 
 * PERMISSIONS OBJECT STRUCTURE:
 * =============================
 * {
 *     create: boolean,  // Show/hide create button
 *     read: boolean,    // Usually true, not used for buttons
 *     edit: boolean,    // Can be used for custom edit buttons
 *     delete: boolean   // Enable/disable bulk delete
 * }
 * 
 * DEFAULT BUTTON IDs:
 * ===================
 * - Create Button: #btnTambah (global standard)
 * - Delete Button: #delete-selected-btn (bulk delete)
 * 
 * BUTTON BEHAVIORS:
 * =================
 * CREATE BUTTON:
 * - permissions.create = true  → button.style.display = "block", disabled = false
 * - permissions.create = false → button.style.display = "none", disabled = true
 * 
 * DELETE BUTTON:
 * - permissions.delete = true  → Enable bulk delete (shown when rows selected)
 * - permissions.delete = false → Disable bulk delete completely
 * 
 * CUSTOM BUTTONS:
 * - showWhenTrue = true  → Show when permission = true (default)
 * - showWhenTrue = false → Show when permission = false (reverse logic)
 * 
 * GLOBAL STORAGE:
 * ===============
 * window.currentPermissions = permissions (accessible from other scripts)
 * 
 * @param {Object} config - Configuration object
 * @param {string} config.createButtonId - ID for create button (default: 'btnTambah')
 * @param {string} config.deleteButtonId - ID for delete button (default: 'delete-selected-btn')
 * @param {Array} config.customButtons - Additional buttons: [{ buttonId, permission, showWhenTrue }]
 * @param {Function} config.onPermissionUpdate - Callback when permissions are updated
 */
export const createPermissionButtonsHandler = (config = {}) => {
    const {
        createButtonId = 'btnTambah',
        deleteButtonId = 'delete-selected-btn',
        customButtons = [],
        onPermissionUpdate
    } = config;

    return (permissions) => {
        const perms = permissions || {};

        // Handle create button
        const createBtn = document.querySelector(`#${createButtonId}`);
        if (createBtn) {
            if (perms.create) {
                createBtn.style.display = "block";
                createBtn.disabled = false;
            } else {
                createBtn.style.display = "none";
                createBtn.disabled = true;
            }
        }

        // Handle delete button
        const deleteBtn = document.querySelector(`#${deleteButtonId}`);
        if (deleteBtn) {
            if (perms.delete) {
                deleteBtn.style.display = "none"; // Hide initially, show when rows selected
                deleteBtn.disabled = false;
            } else {
                deleteBtn.style.display = "none";
                deleteBtn.disabled = true;
            }
        }

        // Handle custom buttons
        customButtons.forEach(({ buttonId, permission, showWhenTrue = true }) => {
            const button = document.querySelector(`#${buttonId}`);
            if (button) {
                const hasPermission = perms[permission];
                if (showWhenTrue) {
                    button.style.display = hasPermission ? "block" : "none";
                    button.disabled = !hasPermission;
                } else {
                    button.style.display = hasPermission ? "none" : "block";
                    button.disabled = hasPermission;
                }
            }
        });

        // Store permissions globally
        window.currentPermissions = perms;

        // Call custom callback if provided
        if (onPermissionUpdate) {
            onPermissionUpdate(perms);
        }
    };
};

/**
 * Default permission buttons handler (backward compatibility)
 * @param {Object} permissions - Permissions object from API
 */
export const handlePermissionButtons = createPermissionButtonsHandler();

/**
 * Create permission handler with common configurations
 */
export const createStandardPermissionHandler = (tableName) => {
    return createPermissionButtonsHandler({
        createButtonId: 'btnTambah',
        deleteButtonId: 'delete-selected-btn',
        onPermissionUpdate: (permissions) => {
            console.log(`📋 ${tableName} permissions updated:`, permissions);
        }
    });
};