function handleValidationErrors(response, callback) {
    let errors = response || {};
    let firstErrorDisplayed = false;
    // Reset semua input & select
    document.querySelectorAll("[data-name], input, select, textarea").forEach((el) => {
        el.classList.remove("is-invalid", "is-valid");

        // Jika elemen ada di dalam Tom Select, cari parent ".ts-wrapper"
        let wrapper = el.closest(".ts-wrapper");
        if (wrapper) {
            wrapper.classList.remove("is-invalid", "is-valid");
        }
    });

    document.querySelectorAll(".invalid-feedback").forEach((el) => {
        el.textContent = "";
        el.style.display = "none";
    });

    Object.keys(errors).forEach((key) => {
        let formattedKey = key.replace(/\.\d+$/, "");
        let inputField = document.querySelector(
            `[data-name="${formattedKey}"], [name="${formattedKey}"]`
        );
        let errorMessageDiv = inputField
            ?.closest(".form-group")
            ?.querySelector(".invalid-feedback") ||
            inputField
            ?.closest(".input-group")
            ?.querySelector(".invalid-feedback") ||
            document.querySelector(`.${formattedKey}_error`);

        if (inputField) {
            inputField.classList.add("is-invalid");
            
            // Handle TomSelect wrapper
            let tsWrapper = inputField.closest(".ts-wrapper") || 
                           document.querySelector(`#${inputField.id}_tomselect`)?.closest(".ts-wrapper") ||
                           inputField.parentElement?.querySelector(".ts-wrapper");
            
            if (tsWrapper) {
                tsWrapper.classList.add("is-invalid");
            }
        }

        if (errorMessageDiv) {
            errorMessageDiv.textContent = errors[key][0];
            errorMessageDiv.style.display = "block";
        }

        if (!firstErrorDisplayed) {
            showToast(errors[key][0], "error");
            firstErrorDisplayed = true;
        }
    });

    if (typeof callback === "function") callback();
}

export { handleValidationErrors };