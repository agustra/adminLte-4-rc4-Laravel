/**
 * Create API configuration for ModernTable
 * @param {Object} config - Configuration object
 * @param {string} config.url - API endpoint URL
 * @param {Function} config.beforeSend - Function to modify params before sending
 * @param {Function} config.onSuccess - Success callback with response
 * @param {Function} config.onError - Error callback
 * @param {Function} config.onComplete - Complete callback
 * @param {Object} config.headers - Additional headers
 */
export const createApiConfig = (config = {}) => {
    const {
        url,
        beforeSend,
        onSuccess,
        onError,
        onComplete,
        headers = {}
    } = config;

    return {
        api: {
            url,
            headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                )?.content,
                ...headers
            },
            beforeSend: (params) => {
                if (beforeSend) {
                    return beforeSend(params);
                }
                return params;
            },
            success: (response) => {
                if (onSuccess) {
                    onSuccess(response);
                }
            },
            error: (error, status, message) => {
                console.error('API Error:', { error, status, message });
                if (onError) {
                    onError(error, status, message);
                } else {
                    // Default error handling
                    if (typeof showToast !== 'undefined') {
                        showToast(`Error: ${message}`, "error");
                    } else {
                        alert(`Error: ${message}`);
                    }
                }
            },
            complete: () => {
                if (onComplete) {
                    onComplete();
                }
            }
        }
    };
};

