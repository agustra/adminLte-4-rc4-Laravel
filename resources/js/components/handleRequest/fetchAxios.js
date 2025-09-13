import { sendAxiosRequest } from "./requestService.js";
import { autoUpdateBadgeForUrl } from "@components/sidebar/badgeUpdater.js";

// Fungsi utama untuk Fetch Setting
export function fetchAxios(options, actionType, callback) {
    if (typeof actionType !== "string") {
        showToast(`❌ Action type harus berupa string!`, "error");
        return;
    }

    // Enhanced callback to include badge update
    const enhancedCallback = function (...args) {
        // Call original callback if exists
        if (callback && typeof callback === "function") {
            callback(...args);
        }

        // Badge update for relevant operations
        if (
            (actionType === "simpan" || actionType === "delete") &&
            options.url
        ) {
            autoUpdateBadgeForUrl(options.url);
        }
    }; 

    sendAxiosRequest({ ...options, actionType, callback: enhancedCallback });
}
