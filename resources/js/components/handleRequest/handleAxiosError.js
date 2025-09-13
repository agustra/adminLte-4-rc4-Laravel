import { handleValidationErrors } from "./validationHandler.js";

function handleAxiosError(error, callback) {
    if (error.response) {
        const { status, data } = error.response;

        // Debug log untuk melihat struktur response
        // console.log('Error response:', { status, data });

        if (status === 422) {
            let validationErrors;

            if (data.errors) {
                // Format: { errors: {field: ["error"]} }
                validationErrors = data.errors;
            } else if (data.message && typeof data.message === "object") {
                // Format: { message: {field: ["error"]} }
                validationErrors = data.message;
            } else {
                showToast(
                    data.message || "Terjadi kesalahan validasi",
                    "error"
                );
                return;
            }

            handleValidationErrors(validationErrors, callback);
        } else {
            // Handle all other HTTP error status codes (500, 403, 404, etc.)
            // Check if data has the expected error structure
            if (data && data.message) {
                showToast(data.message, "error");
            } else {
                showToast("Terjadi kesalahan, silakan coba lagi.", "error");
            }
        }
    } else {
        console.error("Network error:", error);
        showToast("Terjadi kesalahan jaringan. Coba lagi.", "error");
    }
}

export { handleAxiosError };
