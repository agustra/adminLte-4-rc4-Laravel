// Create axios client factory
function createAxiosClient() {
    // Use global axios if available
    const axios = window.axios;
    if (!axios) {
        throw new Error('Axios is required but not available. Please ensure axios is loaded in app.js');
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    const axiosClient = axios.create({
        baseURL: "/",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "Content-Type": "application/json",
        },
    });

    // Interceptor untuk menambahkan token setiap request
    axiosClient.interceptors.request.use(
        (config) => {
            const token = localStorage.getItem("token");
            if (token) {
                config.headers.Authorization = `Bearer ${token}`;
            }
            return config;
        },
        (error) => Promise.reject(error)
    );

    // Interceptor untuk tangani error global
    axiosClient.interceptors.response.use(
        (response) => response,
        (error) => {
            if (error.response?.status === 401) {
                localStorage.removeItem("token");
                localStorage.removeItem("authentication");
                window.location.href = "/admin/login";
            }
            return Promise.reject(error);
        }
    );

    return axiosClient;
}

// Export the client instance
const axiosClient = createAxiosClient();
export default axiosClient;