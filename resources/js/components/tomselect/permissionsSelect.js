import InitTomSelect from "@components/form/InitTomSelect.js";
import axiosClient from "@api/axiosClient.js";

/**
 * Initialize Permissions TomSelect
 * @param {Element} permissionsSelect - The select element
 * @param {Object} options - Configuration options
 * @param {boolean} options.multiple - Whether to allow multiple selections (default: false)
 * @param {boolean} options.grouped - Whether to use grouped permissions (default: false)
 * @param {boolean} options.createSingle - Whether to allow creating new items (default: true)
 */
function initializePermissionsTomSelect(permissionsSelect, options = {}) {
    const { multiple = false, grouped = false, createSingle = true } = options;
    if (!permissionsSelect || permissionsSelect.tomselectInstance) return null;

    const selectedPermission = permissionsSelect.dataset.selected
        ? multiple
            ? permissionsSelect.dataset.selected.split(",").map((s) => s.trim())
            : [permissionsSelect.dataset.selected.trim()]
        : [];

    // Untuk controller-permissions, ambil dari value attribute jika ada
    const currentValues = permissionsSelect.value
        ? Array.isArray(permissionsSelect.value)
            ? permissionsSelect.value
            : [permissionsSelect.value]
        : selectedPermission;

    const permissionsTomSelect = new InitTomSelect(permissionsSelect, {
        urlGet: grouped
            ? "/api/permissions/json?grouped=true"
            : "/api/permissions/json",
        urlByIds: "/api/permissions/by-ids",
        urlStore: "/api/permissions",
        create: true,
        createSingle: createSingle,
        modalId: "createTomselectModal",
        multiple: multiple,
        closeAfterSelect: !multiple,
        createUrl: "/api/permissions/create",
        valueField: options.valueField || "id",
        labelField: options.labelField || "name",
        searchField: ["name"],
        preSelectedValues: currentValues,
        grouped: grouped,
        modalOptions: {
            fieldMapping: { permission: "name" },
            buttonId: "btnActionTomSelect",
        },
    });

    // Update badges when permissions change
    permissionsTomSelect.ts.on("change", function () {
        updatePermissionBadges(permissionsTomSelect.ts);
    });

    return permissionsTomSelect;
}

/**
 * Update permission badges display
 */
function updatePermissionBadges(tomSelectInstance) {
    const selectedPermissions = tomSelectInstance
        ? tomSelectInstance.getValue()
        : [];
    const badgeContainer = document.getElementById("permission-badges");

    if (!badgeContainer) return;

    badgeContainer.innerHTML = "";

    if (selectedPermissions.length === 0) {
        const countText = document.createElement("small");
        countText.className = "text-muted";
        countText.textContent = "(0 permissions)";
        badgeContainer.appendChild(countText);
        return;
    }

    // Fetch permission names from API
    axiosClient
        .get(`/api/permissions/by-ids?ids=${selectedPermissions.join(",")}`)
        .then((response) => {
            const permissions = response.data.data || [];

            badgeContainer.innerHTML = "";

            permissions.forEach((permission) => {
                const badge = document.createElement("span");
                badge.className = "badge bg-primary me-1 mb-1";
                badge.textContent = permission.name;
                badgeContainer.appendChild(badge);
            });

            const countText = document.createElement("small");
            countText.className = "text-muted ms-2";
            countText.textContent = `(${permissions.length} permissions)`;
            badgeContainer.appendChild(countText);
        })
        .catch((error) => {
            console.error("Error fetching permission names:", error);
            // Fallback to IDs if API fails
            selectedPermissions.forEach((permissionId) => {
                const badge = document.createElement("span");
                badge.className = "badge bg-secondary me-1 mb-1";
                badge.textContent = `Permission ${permissionId}`;
                badgeContainer.appendChild(badge);
            });
        });
}

export { initializePermissionsTomSelect, updatePermissionBadges };
