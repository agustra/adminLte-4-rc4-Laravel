// Real-time Badge Updater for Sidebar Menu
import axiosClient from "@api/axiosClient.js";

/**
 * Update badge for specific menu URL
 */
export async function updateMenuBadge(menuUrl) {
    try {
        // Clear cache first to get fresh data
        await axiosClient.post("/api/menu/clear-badge-cache", { url: menuUrl });

        const response = await axiosClient.get("/api/menu/badge-count", {
            params: { url: menuUrl },
        });

        if (response.data.success) {
            const { count, color } = response.data;
            updateBadgeInDOM(menuUrl, count, color);
        }
    } catch (error) {
        console.error("❌ Error updating menu badge:", error);
    }
}

/**
 * Update badge in DOM
 */
function updateBadgeInDOM(menuUrl, count, color) {
    // Try multiple selectors to find the menu link
    let menuLink = document.querySelector(`a.nav-link[href="${menuUrl}"]`);

    if (!menuLink) {
        // Try with full URL
        const fullUrl = window.location.origin + menuUrl;
        menuLink = document.querySelector(`a.nav-link[href="${fullUrl}"]`);
    }

    if (!menuLink) {
        // Try to find by text content
        const menuText = menuUrl.split("/").pop();
        const allLinks = document.querySelectorAll("a.nav-link");
        menuLink = Array.from(allLinks).find((link) => {
            const text = link.textContent.toLowerCase().trim();
            return text.includes(menuText);
        });
    }

    if (!menuLink) return;

    const menuP = menuLink.querySelector("p");
    if (!menuP) return;

    // Remove existing badge
    const existingBadge = menuP.querySelector(".badge");
    if (existingBadge) {
        existingBadge.remove();
    }

    // Add new badge if count > 0
    if (count > 0) {
        const badge = document.createElement("span");
        badge.className = `badge bg-${color} ms-auto`;
        badge.textContent = count;

        // Insert before arrow (if exists) or at the end
        const arrow = menuP.querySelector(".nav-arrow");
        if (arrow) {
            badge.classList.add("me-2");
            menuP.insertBefore(badge, arrow);
        } else {
            menuP.appendChild(badge);
        }

        // Add animation
        badge.style.animation = "badgeUpdate 0.3s ease-in-out";
    }
}

/**
 * Auto-update all configured badges
 */
export async function autoUpdateBadgeForUrl(url) {
    try {
        // Get all configured badge URLs from API
        const response = await axiosClient.get("/api/menu/all-badge-counts");

        if (response.data.success) {
            const badges = response.data.badges;

            // Update all badges that might be affected
            Object.keys(badges).forEach((menuUrl) => {
                setTimeout(() => {
                    updateMenuBadge(menuUrl);
                }, 500);
            });
        }
    } catch (error) {
        // Silent error handling
    }
}

// CSS Animation for badge update
const style = document.createElement("style");
style.textContent = `
    @keyframes badgeUpdate {
        0% { transform: scale(0.8); opacity: 0.5; }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); opacity: 1; }
    }
`;
document.head.appendChild(style);
