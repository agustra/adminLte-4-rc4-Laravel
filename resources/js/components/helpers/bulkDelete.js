import axiosClient from "@api/axiosClient.js";

/**
 * Handle bulk delete for ModernTable
 * @param {Object} config - Configuration object
 * @param {string} config.deleteUrl - URL for bulk delete API
 * @param {string} config.itemName - Name of items being deleted (e.g., 'users', 'roles')
 * @param {Function} config.onSuccess - Callback on successful delete
 * @param {Function} config.onError - Callback on error
 */
export const createBulkDeleteHandler = (config) => {
    const {
        deleteUrl,
        itemName = 'items',
        onSuccess,
        onError
    } = config;

    return (e, dt) => {
        const selected = dt.getSelectedRows();
        if (selected.length === 0) return;

        if (!confirm(`Delete ${selected.length} selected ${itemName}?`)) return;

        axiosClient
            .post(deleteUrl, {
                ids: selected.map((row) => row.id),
            })
            .then(({ data }) => {
                if (data?.status === "success") {
                    alert(`Successfully deleted ${selected.length} ${itemName}!`);
                    dt.reload();
                    dt.clearSelection();
                    
                    if (onSuccess) {
                        onSuccess(selected, data);
                    }
                } else {
                    const errorMsg = "Error: " + (data?.message ?? "Unknown error");
                    alert(errorMsg);
                    
                    if (onError) {
                        onError(new Error(errorMsg), data);
                    }
                }
            })
            .catch((error) => {
                console.error("Delete error:", error);
                alert(`Error deleting ${itemName}!`);
                
                if (onError) {
                    onError(error);
                }
            });
    };
};

// Backward compatibility - simple handler
export const handleBulkDelete = createBulkDeleteHandler;