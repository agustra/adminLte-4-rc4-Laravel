// currencyInput.js
export function initializeCurrencyInput(selector, context = document) {
    const inputs = context.querySelectorAll(selector);

    inputs.forEach((input) => {
        if (input.value) {
            input.value = formatNumber(Math.floor(parseFloat(input.value)));
        }

        input.removeEventListener("input", handleInput);
        input.removeEventListener("keypress", preventNonNumericInput);
        input.removeEventListener("paste", handlePaste);

        input.addEventListener("input", handleInput);
        input.addEventListener("keypress", preventNonNumericInput);
        input.addEventListener("paste", handlePaste);
    });
}

export function parseNumber(numberString) {
    let isNegative = numberString.startsWith("-");
    
    // For form input, just remove dots and parse as integer
    let angka = parseInt(numberString.replace(/[^\d]/g, "")) || 0;
    
    return isNegative ? -angka : angka;
}

export function formatNumber(angka) {
    // Use manual formatting to avoid locale issues
    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

export function formatNumberWithDecimal(angka) {
    // Parse as float to handle decimals properly
    const floatValue = parseFloat(angka);
    
    // Split integer and decimal parts
    const parts = floatValue.toFixed(2).split('.');
    const integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    const decimalPart = parts[1];
    
    // Always show 2 decimal places like rupiah.js
    return integerPart + ',' + decimalPart;
}

// Private functions (tidak diexport)
function handleInput(e) {
    let cursorPosition = e.target.selectionStart;
    let oldValue = e.target.value;
    let newValue = validateAndFormat(oldValue);
    e.target.value = newValue;

    // Only set cursor position for text inputs, not number inputs
    if (e.target.type === 'text' && e.target.setSelectionRange) {
        let newCursorPosition =
            cursorPosition + (newValue.length - oldValue.length);
        e.target.setSelectionRange(newCursorPosition, newCursorPosition);
    }
}

function preventNonNumericInput(e) {
    let input = e.target;
    let value = input.value;

    if (
        ["Backspace", "Delete", "Tab"].includes(e.key) ||
        !isNaN(parseInt(e.key))
    ) {
        return;
    }

    if (e.key === "-" && input.selectionStart === 0 && !value.includes("-")) {
        return;
    }

    e.preventDefault();
    showErrorMessage(input);
}

function handlePaste(e) {
    e.preventDefault();
    const pastedText = (e.clipboardData || window.clipboardData).getData(
        "text"
    );
    let numericValue = pastedText.replace(/[^\d-]/g, "");
    if ((numericValue.match(/-/g) || []).length > 1) {
        numericValue = numericValue.replace(/-/g, "");
    }
    e.target.value = formatNumber(parseInt(numericValue) || 0);
}

function validateAndFormat(value) {
    let isNegative = value.startsWith("-");
    let numericValue = value.replace(/[^\d]/g, "");
    let formattedValue = formatNumber(parseInt(numericValue) || 0);
    return isNegative ? "-" + formattedValue : formattedValue;
}

function showErrorMessage(input) {
    let inputName =
        input.getAttribute("data-label") ||
        formatInputName(input.name) ||
        "Input";
    Swal.fire({
        title: `Error ${inputName}!`,
        text: "Mohon masukkan hanya angka atau angka negatif!",
        icon: "error",
    });
}

function formatInputName(name) {
    if (!name) return "";
    return name
        .split(/[_-]/)
        .map(
            (word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
        )
        .join(" ");
}
