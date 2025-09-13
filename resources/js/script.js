import Swal from "sweetalert2";
import Toastify from "toastify-js";
import { initializeDarkMode } from "@components/ui/darkMode.js";
import { initializeMenuTreeview } from "@components/ui/menuInit.js";

// Make libraries globally available
window.Swal = Swal;
window.Toastify = Toastify;

// ===== GLOBAL TOAST FUNCTION =====
window.showToast = function (message, type = "info") {
    const config = {
        success: {
            color: "linear-gradient(to right, #00b09b, #96c93d)",
            icon: "✔️",
        },
        info: {
            color: "linear-gradient(to right, #17a2b8, #5bc0de)",
            icon: "ℹ️",
        },
        warning: {
            color: "linear-gradient(to right, #ffc107, #ffea00)",
            icon: "⚠️",
        },
        error: {
            color: "linear-gradient(to right, #dc3545, #ff6f61)",
            icon: "❌",
        },
    };

    const { color, icon } = config[type] || config.info;
    const iconStyle =
        "background-color: rgba(255, 255, 255, 0.8); border-radius: 50%; padding: 5px; font-size: 13px;";

    Toastify({
        text: `<span style="${iconStyle}">${icon}</span> ${message}`,
        duration: 3000,
        close: true,
        gravity: "bottom",
        position: "right",
        escapeMarkup: false,
        style: { background: color, color: "#fff" },
    }).showToast();
};

// ===== REFRESH BUTTON FIX =====
function fixRefreshButtons() {
    document.querySelectorAll('a[href*="/clear/cache"]').forEach((link) => {
        link.href = "#";
        link.onclick = (e) => {
            e.preventDefault();
            window.location.href = "/clear/cache";
        };
    });
}

// ===== DOM READY INITIALIZATION =====
document.addEventListener("DOMContentLoaded", function () {
    // Initialize UI components
    initializeDarkMode();
    initializeMenuTreeview();

    // Initialize global functions
    fixRefreshButtons();

    // Fix Bootstrap modal aria-hidden warning
    document.addEventListener("hidden.bs.modal", function (e) {
        const modal = e.target;
        if (modal) {
            modal.removeAttribute("aria-hidden");
            const focusedElement = modal.querySelector(":focus");
            if (focusedElement) focusedElement.blur();
        }
    });

    // Fix focus management when modal is hiding
    document.addEventListener("hide.bs.modal", function (e) {
        const modal = e.target;
        if (modal) {
            modal.querySelectorAll(":focus").forEach((el) => el.blur());
        }
    });
});

// Watch for dynamic content
const observer = new MutationObserver(() => fixRefreshButtons());
observer.observe(document.body, { childList: true, subtree: true });
