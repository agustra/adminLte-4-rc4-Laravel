
import axiosClient from "@api/axiosClient.js";
import { autoUpdateBadgeForUrl } from '@components/sidebar/badgeUpdater.js';

export function Delete(options) {
    const defaults = {
        buttonSelector: "",
        dataIdAttribute: "data-id",
        deleteUrl: "",
        // tableSelector: "#table",
        confirmTitle: "Apakah Anda yakin?",
        confirmText: "Anda tidak akan dapat mengembalikan ini!",
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Tidak",
        spinnerDuration: 300,
        onDeleteSuccess: null,
    };

    const settings = { ...defaults, ...options };
    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]'
    )?.content;

    async function deleteItem(id, button) {
        toggleButtonLoading(button, true);

        try {
            // Support function or string for deleteUrl
            const deleteUrl = typeof settings.deleteUrl === 'function' 
                ? settings.deleteUrl(id) 
                : `${settings.deleteUrl}${id}`;
                
            const response = await axiosClient.delete(
                deleteUrl,
                {
                    data: { id },
                }
            );

            if (response.data.status === "success" || response.data.success === true) {
                showToast(response.data.message, "success");
                
                // Auto-clear badge cache for delete operations
                const urlForBadge = typeof settings.deleteUrl === 'function' 
                    ? settings.deleteUrl(id) 
                    : settings.deleteUrl;
                autoUpdateBadgeForUrl(urlForBadge);
                
                if (typeof settings.onDeleteSuccess === "function") {
                    settings.onDeleteSuccess(id, response.data);
                }
            } else {
                showToast(response.data.message || 'Terjadi kesalahan', "error");
            }
        } catch (error) {
            const errorMessage =
                error.response?.data?.message ||
                "Terjadi kesalahan, silakan coba lagi.";
            console.error("❌ Kesalahan:", errorMessage);
            showToast(errorMessage, "error");
        } finally {
            toggleButtonLoading(button, false);
        }
    }

    function toggleButtonLoading(button, isLoading) {
        button.innerHTML = isLoading
            ? '<i class="fa fa-spin fa-spinner text-white"></i>'
            : '<i class="fa fa-trash-alt text-danger"></i>';
    }

    document.body.addEventListener("click", (e) => {
        const button = e.target.closest(settings.buttonSelector);
        if (!button) return;

        e.preventDefault();
        const dataId = button.getAttribute(settings.dataIdAttribute);
        const originalContent = button.innerHTML;

        toggleButtonLoading(button, true);

        setTimeout(() => {
            Swal.fire({
                title: settings.confirmTitle,
                text: settings.confirmText,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: settings.confirmButtonText,
                cancelButtonText: settings.cancelButtonText,
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteItem(dataId, button);
                } else {
                    // Reset button to original state when user clicks "Tidak"
                    button.innerHTML = originalContent;
                }
            });
        }, settings.spinnerDuration);
    });
}
