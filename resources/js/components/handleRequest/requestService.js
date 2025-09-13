import axiosClient from "@api/axiosClient.js";
import { handleAxiosError } from "./handleAxiosError.js";
import { closeModal } from "./modalHandler.js";
import { refreshSidebar } from "@components/sidebar/sidebarRefresher.js";

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content;
}

// Menangani response sukses biasa
export function handleFetchSuccess(response, callback) {
    const { data } = response;

    // Check if response contains error status
    if (data.status === "error") {
        showToast(data.message || "Terjadi kesalahan", "error");
        return;
    }

    if (data.message) {
        showToast(data.message, "success");
    }

    // Check if sidebar needs refresh
    if (data.refresh_sidebar) {
        // Refresh sidebar without page reload
        setTimeout(() => {
            refreshSidebar();
        }, 500);
    }

    closeModal();

    if (typeof callback === "function") {
        callback(data);
    }
}

export async function sendAxiosRequest(options) {
    if (!options.url) {
        showToast("❌ URL tidak boleh kosong!", "error");
        return;
    }

    // Ensure method is a valid string
    let method = "GET";
    if (options.method && typeof options.method === "string") {
        method = options.method.toUpperCase();
    }

    try {
        const response = await axiosClient({
            method: method,
            url: options.url,
            data: options.data || {},
            headers: {
                "X-CSRF-TOKEN": getCsrfToken(),
                Accept: "application/json",
            },
        });
        handleFetchSuccess(response, options.callback);
    } catch (error) {
        handleAxiosError(error, options.callback);
    }
}
