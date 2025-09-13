import InitTomSelect from "@components/form/InitTomSelect.js";
import axiosClient from "@api/axiosClient.js";

/**
 * Initialize Permissions TomSelect for Roles
 */
function initializeRolePermissionsTomSelect(eventOrElement) {
    // Handle both event object and direct element
    let permissionsSelect;
    
    if (eventOrElement && eventOrElement.target) {
        // Called from event listener
        const modal = eventOrElement.target;
        permissionsSelect = modal.querySelector('#permissions');
    } else {
        // Called with direct element
        permissionsSelect = eventOrElement;
    }
    
    if (!permissionsSelect || permissionsSelect.tomselectInstance) return null;

    const rolePermissions = permissionsSelect.dataset.selected 
        ? permissionsSelect.dataset.selected
              .split(",")
              .filter((id) => id.trim())
        : [];

    const permissionsTomSelect = new InitTomSelect(permissionsSelect, {
        urlGet: "/api/permissions/json?grouped=true",
        urlByIds: "/api/permissions/by-ids",
        urlStore: "/api/permissions",
        create: true,
        createSingle: true,
        modalId: "createTomselectModal",
        multiple: true,
        createUrl: "/admin/permissions/create",
        valueField: "id",
        labelField: "name",
        searchField: ["name"],
        preSelectedValues: rolePermissions,
        grouped: true,
        modalOptions: {
            fieldMapping: { permission: "name" },
            buttonId: "btnActionTomSelect",
        },
    });

    // Update badges when permissions change
    permissionsTomSelect.ts.on("change", function () {
        updateRolePermissionBadges(permissionsTomSelect.ts);
    });

    return permissionsTomSelect;
}

/**
 * Update role permission badges display
 */
function updateRolePermissionBadges(eventOrInstance) {
    let tomSelectInstance;
    
    if (eventOrInstance && eventOrInstance.target) {
        // Called from event listener - find the tomselect instance
        const modal = eventOrInstance.target;
        const permissionsSelect = modal.querySelector('#permissions');
        tomSelectInstance = permissionsSelect?.tomselectInstance;
    } else {
        // Called with direct instance
        tomSelectInstance = eventOrInstance;
    }
    
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
                badge.className = "badge bg-success me-1 mb-1";
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

export { initializeRolePermissionsTomSelect, updateRolePermissionBadges };
