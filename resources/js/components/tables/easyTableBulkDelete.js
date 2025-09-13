/**
 * EasyTable Bulk Delete Component
 * Standalone bulk delete untuk EasyDataTable
 */

import axiosClient from "@api/axiosClient.js";

export class EasyTableBulkDelete {
    constructor(options) {
        this.options = {
            tableInstance: null,
            deleteUrl: "",
            confirmMessage: "Yakin ingin menghapus {count} data terpilih?",
            buttonId: "btnDeleteSelected",
            onDeleteSuccess: null,
            onDeleteError: null,
            ...options,
        };

        if (!this.options.tableInstance) {
            throw new Error("EasyTableBulkDelete: tableInstance is required");
        }

        this.init();
    }

    init() {
        // Auto-attach selection listener
        this.attachSelectionListener();
    }

    // Auto-attach selection listener to table
    attachSelectionListener() {
        if (this.options.tableInstance && this.options.tableInstance.element) {
            this.options.tableInstance.element.addEventListener(
                "select",
                (e) => {
                    const selectedCount = e.detail.selectedRows.length;
                    this.updateButton(selectedCount);
                }
            );
        }
    }

    // Method to trigger bulk delete
    execute() {
        const selectedRows = this.options.tableInstance.getSelectedRows();

        if (selectedRows.length === 0) {
            if (typeof showToast !== "undefined") {
                showToast("Pilih data yang akan dihapus!", "error");
            } else {
                alert("Pilih data yang akan dihapus!");
            }
            return;
        }

        // Use SweetAlert if available
        if (typeof Swal !== "undefined") {
            Swal.fire({
                title: "Apakah Anda yakin?",
                html: `Anda akan menghapus <strong>${selectedRows.length}</strong> data terpilih!`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Tidak",
            }).then((result) => {
                if (result.isConfirmed) {
                    this.performDelete(selectedRows);
                }
            });
        } else {
            // Fallback to confirm
            const message = this.options.confirmMessage.replace(
                "{count}",
                selectedRows.length
            );
            if (confirm(message)) {
                this.performDelete(selectedRows);
            }
        }
    }

    // Perform actual delete
    async performDelete(selectedIds) {
        try {
            // Show loading
            if (typeof Swal !== "undefined") {
                Swal.fire({
                    title: "Menghapus...",
                    html: "Silakan tunggu",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });
            }

            // Use static imported axiosClient

            const response = await axiosClient.post(this.options.deleteUrl, {
                ids: selectedIds,
            });

            if (response.data.status === "success") {
                if (typeof showToast !== "undefined") {
                    showToast(response.data.message, "success");
                }

                // Refresh table
                this.options.tableInstance.reload();
                this.options.tableInstance.clearSelection();

                // Call success callback
                if (this.options.onDeleteSuccess) {
                    this.options.onDeleteSuccess(selectedIds, response.data);
                }
            } else {
                if (typeof showToast !== "undefined") {
                    showToast(response.data.message, "error");
                }
            }
        } catch (error) {
            console.error("Delete failed:", error);
            const message =
                error.response?.data?.message ||
                "Terjadi kesalahan, silakan coba lagi.";
            if (typeof showToast !== "undefined") {
                showToast(message, "error");
            }
        } finally {
            if (typeof Swal !== "undefined") {
                Swal.close();
            }
        }
    }

    // Method to update button visibility and text
    updateButton(selectedCount) {
        const button = document.getElementById(this.options.buttonId);
        if (button) {
            if (selectedCount > 0) {
                button.style.display = "inline-block";
                button.innerHTML = `<i class="fas fa-trash"></i> Hapus ${selectedCount}`;
            } else {
                button.style.display = "none";
            }
        }
    }

    // Cleanup method
    destroy() {
        // No cleanup needed for standalone version
    }
}

// Factory function for easier usage
export function createEasyTableBulkDelete(options) {
    return new EasyTableBulkDelete(options);
}

