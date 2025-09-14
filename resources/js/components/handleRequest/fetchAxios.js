import { sendAxiosRequest } from "./requestService.js";
import { autoUpdateBadgeForUrl } from "@components/sidebar/badgeUpdater.js";
import { badgeCache } from "@components/sidebar/badgeConfigCache.js";

// Fungsi utama untuk Fetch Setting
export function fetchAxios(options, actionType, callback) {
    if (typeof actionType !== "string") {
        showToast(`❌ Action type harus berupa string!`, "error");
        return;
    }

    // Enhanced callback to include smart badge update
    const enhancedCallback = async function (...args) {
        // Call original callback if exists
        if (callback && typeof callback === "function") {
            callback(...args);
        }

        // Smart badge update - only if URL has active badge config
        if (
            (actionType === "simpan" || actionType === "delete") &&
            options.url
        ) {
            try {
                const shouldUpdate = await badgeCache.shouldUpdateBadge(options.url);
                if (shouldUpdate) {
                    autoUpdateBadgeForUrl(options.url);
                }
            } catch (error) {
                console.error('Error in badge update logic:', error);
            }
        }
    }; 

    sendAxiosRequest({ ...options, actionType, callback: enhancedCallback });
}
