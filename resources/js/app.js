import "bootstrap/dist/css/bootstrap.min.css";
import * as bootstrap from "bootstrap";
window.bootstrap = bootstrap;

// Import CSS
import "bootstrap-icons/font/bootstrap-icons.css";
import "@fortawesome/fontawesome-free/css/all.min.css";
import "sweetalert2/dist/sweetalert2.min.css";
import "toastify-js/src/toastify.css";

// Import axios and make it globally available
import axios from "axios";
window.axios = axios;

// ===== LAST VISITED PAGE TRACKER =====
// Simpan URL terakhir, kecuali halaman auth dan root
const currentPath = window.location.pathname;
const excludedPaths = ["/login", "/register", "/logout", "/password"];
const shouldSave = !excludedPaths.some((path) => currentPath.includes(path));

if (shouldSave && currentPath !== "/") {
    localStorage.setItem("lastVisited", currentPath);
    console.log("✅ Saved lastVisited:", currentPath);
} else {
    console.log("❌ Not saving path:", currentPath, "(excluded or root)");
}

// Helper function untuk redirect setelah login
window.redirectToLastPage = async function () {
    const lastVisited = localStorage.getItem("lastVisited");
    let redirectUrl = "/"; // Default fallback - root adalah dashboard

    if (lastVisited && lastVisited !== "/login") {
        // Cek apakah URL masih valid dengan HEAD request
        try {
            const response = await fetch(lastVisited, { method: "HEAD" });
            if (response.ok || response.status === 302) {
                // URL valid atau redirect (302) - gunakan lastVisited
                redirectUrl = lastVisited;
            } else {
                // URL tidak valid - hapus dari localStorage
                localStorage.removeItem("lastVisited");
                console.log("🧹 Removed invalid URL:", lastVisited);
            }
        } catch (error) {
            // Network error atau URL tidak accessible - hapus dari localStorage
            localStorage.removeItem("lastVisited");
            console.log("🧹 Removed inaccessible URL:", lastVisited);
        }
    }

    localStorage.removeItem("lastVisited"); // Clean up
    window.location.href = redirectUrl;
};
