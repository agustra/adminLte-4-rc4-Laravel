import InitTomSelect from "@components/form/InitTomSelect.js";

/**
 * Konfigurasi dasar (fleksibel untuk berbagai kebutuhan)
 */
const baseConfig = {
    multiple: false,
    grouped: false,
    createSingle: true,
    modalId: "createTomselectModal",
    valueField: "value", // Lebih umum untuk berbagai use case
    labelField: "text",  // Lebih umum untuk berbagai use case
    searchField: ["text"],
    modalOptions: {
        fieldMapping: { name: "text" }, // Lebih umum
        buttonId: "btnActionTomSelect",
    },
};

/**
 * Shortcut untuk date fields (mode offline)
 */
export function initializeDateFieldsTomSelect(el, options = {}) {
    return initializeGeneralTomSelect(el, {
        multiple: true,
        useApi: false, // Mode offline
        placeholder: "Pilih atau ketik field tanggal...",
        ...options
    });
}

/**
 * Inisialisasi TomSelect generik - fleksibel dengan atau tanpa API
 *
 * @param {HTMLSelectElement} el - Elemen <select> yang akan dijadikan TomSelect
 * @param {Object} options - konfigurasi opsional
 * @param {Object} options.urls - { get, getGrouped, byIds, store, createPage } - opsional untuk API mode
 * @param {boolean} options.useApi - true untuk menggunakan API, false untuk mode offline (default: true jika urls ada)
 * @returns {InitTomSelect|null}
 */
export function initializeGeneralTomSelect(el, options = {}) {
    if (!el || el.tomselectInstance) return null;

    const { urls, useApi, ...rest } = options;
    
    // Tentukan mode: API atau offline
    const isApiMode = useApi !== false && urls && urls.get;
    
    // Jika API mode tapi URLs tidak lengkap, throw error
    if (isApiMode && (!urls.byIds || !urls.store)) {
        console.warn('API mode membutuhkan minimal urls.get, urls.byIds, dan urls.store');
    }

    const cfg = { ...baseConfig, ...rest };

    const selectedValues = el.dataset.selected
        ? cfg.multiple
            ? el.dataset.selected.split(",").map((s) => s.trim())
            : [el.dataset.selected.trim()]
        : [];

    const tomSelectConfig = {
        create: true,
        createSingle: cfg.createSingle,
        modalId: cfg.modalId,
        multiple: cfg.multiple,
        closeAfterSelect: !cfg.multiple,
        valueField: cfg.valueField,
        labelField: cfg.labelField,
        searchField: cfg.searchField,
        preSelectedValues: selectedValues,
        grouped: cfg.grouped,
        modalOptions: cfg.modalOptions,
    };

    if (isApiMode) {
        // Mode API - gunakan URLs
        tomSelectConfig.urlGet = cfg.grouped ? urls.getGrouped ?? urls.get : urls.get;
        tomSelectConfig.urlByIds = urls.byIds;
        tomSelectConfig.urlStore = urls.store;
        tomSelectConfig.createUrl = urls.createPage;
    } else {
        // Mode offline - gunakan opsi HTML yang ada, bisa create manual
        tomSelectConfig.urlGet = null;
        tomSelectConfig.urlByIds = null;
        tomSelectConfig.urlStore = urls?.store || null; // Tetap bisa store jika ada
        tomSelectConfig.createUrl = urls?.createPage || null;
    }

    return new InitTomSelect(el, tomSelectConfig);
}

/* ========================================
 * CARA PAKAI / USAGE EXAMPLES
 * ========================================
 *
 * 1. MODE API PENUH (untuk data master seperti permissions, roles, users)
 * 
 *    import { initializeGeneralTomSelect } from "@tomselect/badgeConfigsTomSelect.js";
 * 
 *    // Di modal handler
 *    const permissionsElement = modal.querySelector("#permissions");
 *    initializeGeneralTomSelect(permissionsElement, {
 *        urls: {
 *            get: "/api/permissions/json",
 *            byIds: "/api/permissions/by-ids",
 *            store: "/api/permissions",
 *            createPage: "/admin/permissions/create"
 *        },
 *        multiple: true,
 *        grouped: false,
 *        valueField: "id",
 *        labelField: "name"
 *    });
 *
 * 2. MODE OFFLINE (untuk input manual seperti date fields)
 * 
 *    import { initializeDateFieldsTomSelect } from "@tomselect/badgeConfigsTomSelect.js";
 * 
 *    // Shortcut untuk date fields
 *    const dateFieldsElement = modal.querySelector(".date-fields-select");
 *    initializeDateFieldsTomSelect(dateFieldsElement);
 * 
 *    // Atau manual
 *    initializeGeneralTomSelect(dateFieldsElement, {
 *        useApi: false,
 *        multiple: true,
 *        placeholder: "Ketik field tanggal..."
 *    });
 *
 * 3. MODE HYBRID (offline + bisa create via API)
 * 
 *    initializeGeneralTomSelect(element, {
 *        useApi: false,
 *        urls: {
 *            store: "/api/date-fields", // Hanya untuk create
 *            createPage: "/admin/date-fields/create"
 *        },
 *        multiple: true,
 *        placeholder: "Pilih atau buat field baru..."
 *    });
 *
 * 4. PENGGUNAAN DI BADGE CONFIGS
 * 
 *    // Di badge-configs.js
 *    document.addEventListener("shown.bs.modal", async (e) => {
 *        const modal = e.target;
 *        
 *        try {
 *            const { initializeDateFieldsTomSelect } = await import(
 *                "@tomselect/badgeConfigsTomSelect.js"
 *            );
 *            
 *            setTimeout(() => {
 *                const dateFieldsElement = modal.querySelector(".date-fields-select");
 *                if (dateFieldsElement) {
 *                    initializeDateFieldsTomSelect(dateFieldsElement);
 *                }
 *            }, 100);
 *        } catch (error) {
 *            console.error("Error loading date fields select:", error);
 *        }
 *    });
 *
 * 5. HTML STRUCTURE YANG DIBUTUHKAN
 * 
 *    <!-- Untuk mode offline (date fields) -->
 *    <select class="form-select date-fields-select" name="date_fields[]" multiple>
 *        <option value="created_at">created_at</option>
 *        <option value="updated_at">updated_at</option>
 *        <option value="date">date</option>
 *        <!-- User bisa ketik custom field -->
 *    </select>
 * 
 *    <!-- Untuk mode API -->
 *    <select class="form-select" name="permissions[]" id="permissions" multiple>
 *        <!-- Options akan di-load dari API -->
 *    </select>
 *
 */
