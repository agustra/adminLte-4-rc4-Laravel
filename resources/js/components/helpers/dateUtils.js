/**
 * Date utilities for generating date, month, year options
 */

/**
 * Get current date info
 */
export function getCurrentDate() {
    const now = new Date();
    return {
        day: now.getDate(),
        month: now.getMonth() + 1, // 1-12
        year: now.getFullYear()
    };
}

/**
 * Get month names in Indonesian
 */
export function getMonthNames() {
    return {
        1: 'Jan', 2: 'Feb', 3: 'Mar', 4: 'Apr',
        5: 'Mei', 6: 'Jun', 7: 'Jul', 8: 'Agu',
        9: 'Sep', 10: 'Okt', 11: 'Nov', 12: 'Des'
    };
}

/**
 * Generate days array (1-31)
 * @param {number} selectedYear - Selected year
 * @param {number} selectedMonth - Selected month (1-12)
 * @param {boolean} limitToCurrent - Limit to current date if current year/month
 */
export function getDays(selectedYear = null, selectedMonth = null, limitToCurrent = true) {
    const current = getCurrentDate();
    let maxDay = 31;
    
    if (limitToCurrent && selectedYear === current.year && selectedMonth === current.month) {
        maxDay = current.day;
    } else if (selectedYear && selectedMonth) {
        // Get actual days in month
        maxDay = new Date(selectedYear, selectedMonth, 0).getDate();
    }
    
    const days = [];
    for (let i = 1; i <= maxDay; i++) {
        days.push({ value: i, label: i.toString() });
    }
    
    return days;
}

/**
 * Generate months array (1-12)
 * @param {number} selectedYear - Selected year
 * @param {boolean} limitToCurrent - Limit to current month if current year
 */
export function getMonths(selectedYear = null, limitToCurrent = true) {
    const current = getCurrentDate();
    const monthNames = getMonthNames();
    const months = [];
    
    let maxMonth = 12;
    if (limitToCurrent && selectedYear === current.year) {
        maxMonth = current.month;
    }
    
    for (let i = 1; i <= maxMonth; i++) {
        months.push({ 
            value: i, 
            label: monthNames[i] 
        });
    }
    
    return months;
}

/**
 * Generate years array (current year to past years)
 * @param {number} yearsBack - How many years back from current (default: 5)
 */
export function getYears(yearsBack = 5) {
    const current = getCurrentDate();
    const years = [];
    
    for (let i = current.year; i >= (current.year - yearsBack); i--) {
        years.push({ value: i, label: i.toString() });
    }
    
    return years;
}

/**
 * Populate select element with options
 * @param {HTMLSelectElement} selectElement 
 * @param {Array} options - Array of {value, label} objects
 * @param {string} placeholder - Placeholder text
 * @param {string|number} selectedValue - Value to select
 */
export function populateSelect(selectElement, options, placeholder = '', selectedValue = null) {
    if (!selectElement) return;
    
    // Clear existing options
    selectElement.innerHTML = '';
    
    // Add placeholder
    if (placeholder) {
        const placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        placeholderOption.textContent = placeholder;
        selectElement.appendChild(placeholderOption);
    }
    
    // Add options
    options.forEach(option => {
        const optionElement = document.createElement('option');
        optionElement.value = option.value;
        optionElement.textContent = option.label;
        
        if (selectedValue && option.value == selectedValue) {
            optionElement.selected = true;
        }
        
        selectElement.appendChild(optionElement);
    });
}