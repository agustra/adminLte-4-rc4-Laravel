/**
 * Create selection change handler for ModernTable
 * @param {Object} config - Configuration object
 * @param {string} config.buttonId - ID of the bulk action button (default: 'delete-selected-btn')
 * @param {string} config.countSelector - Selector for count span (default: '.selected-count')
 * @param {string} config.buttonText - Text template for button (default: 'Delete ({count})')
 * @param {Function} config.onSelectionChange - Custom callback for selection changes
 */
export const createSelectionChangeHandler = (config = {}) => {
    const {
        buttonId = 'delete-selected-btn',
        countSelector = '.selected-count',
        buttonText = 'Delete ({count})',
        onSelectionChange
    } = config;

    return (selectedRows) => {
        const deleteBtn = document.getElementById(buttonId);
        if (!deleteBtn) return;

        const hasSelection = selectedRows.length > 0;
        const count = selectedRows.length;

        // Update button visibility and state
        deleteBtn.style.display = hasSelection ? "inline-block" : "none";
        deleteBtn.disabled = !hasSelection;

        // Update count in button text
        const countSpan = deleteBtn.querySelector(countSelector);
        if (countSpan) {
            countSpan.textContent = count;
        }

        // Update button text if template provided
        if (buttonText.includes('{count}') && hasSelection) {
            const textElement = deleteBtn.querySelector('.btn-text') || deleteBtn;
            const currentText = textElement.innerHTML;
            
            // Only update if it contains the template pattern
            if (currentText.includes('selected-count') || currentText.includes('{count}')) {
                textElement.innerHTML = buttonText.replace('{count}', `<span class="selected-count">${count}</span>`);
            }
        }

        // Call custom callback if provided
        if (onSelectionChange) {
            onSelectionChange(selectedRows, { count, hasSelection });
        }
    };
};

// Default handler for backward compatibility
export const handleSelectionChange = createSelectionChangeHandler();
