import InitTomSelect from "@forms/InitTomSelect.js";

/**
 * Initialize Roles TomSelect for Permissions
 */
function initializeRolesTomSelect(eventOrElement) {
    // Handle both event object and direct element
    let rolesSelect;
    
    if (eventOrElement && eventOrElement.target) {
        // Called from event listener
        const modal = eventOrElement.target;
        rolesSelect = modal.querySelector('#roles') || modal.querySelector('#role');
    } else {
        // Called with direct element
        rolesSelect = eventOrElement;
    }
    
    if (!rolesSelect || rolesSelect.tomselectInstance) return null;

    const selectedRoles = rolesSelect.dataset.selected
        ? rolesSelect.dataset.selected
              .split(",")
              .filter((id) => id.trim())
        : [];

    const rolesTomSelect = new InitTomSelect(rolesSelect, {
        urlGet: "/api/roles/json",
        urlByIds: "/api/roles/by-ids",
        urlStore: "/api/roles",
        create: true,
        createSingle: true,
        modalId: "createTomselectModal",
        multiple: true,
        createUrl: "/admin/roles/create",
        valueField: "id",
        labelField: "name",
        searchField: ["name"],
        preSelectedValues: selectedRoles,
        modalOptions: {
            fieldMapping: { role: "name" },
            buttonId: "btnActionTomSelect",
        },
    });

    return rolesTomSelect;
}

export { initializeRolesTomSelect };