// Sidebar Refresher Component
import axiosClient from "@api/axiosClient.js";

/**
 * Refresh sidebar menu without page reload
 */
export function refreshSidebar() {
    axiosClient
        .get("/admin/api/menus/sidebar")
        .then((response) => {
            if (response.data.success && response.data.html) {
                const navigationElement =
                    document.querySelector("nav ul#navigation");

                if (navigationElement) {
                    // Store current open state
                    const openMenus = [];
                    document
                        .querySelectorAll(".nav-item.has-treeview.menu-open")
                        .forEach((item) => {
                            const link = item.querySelector(".nav-link");
                            if (link) {
                                openMenus.push(link.textContent.trim());
                            }
                        });

                    // Parse the HTML response to get only the inner content
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(
                        response.data.html,
                        "text/html"
                    );
                    const newUl = doc.querySelector("ul#navigation");

                    if (
                        newUl &&
                        navigationElement &&
                        navigationElement.tagName === "UL"
                    ) {
                        // Replace only the innerHTML of the UL element
                        navigationElement.innerHTML = newUl.innerHTML;
                    }

                    // Wait for DOM to be fully updated
                    setTimeout(() => {
                        const newNavigation = navigationElement;

                        if (newNavigation) {
                            // Force reinitialize treeview
                            initializeTreeview();

                            // Check if current page matches any submenu and open parent
                            const currentPath = window.location.pathname;

                            document
                                .querySelectorAll(".nav-link")
                                .forEach((link) => {
                                    const href = link.getAttribute("href");
                                    if (href) {
                                        // Remove domain and get clean path
                                        const linkPath = href.replace(
                                            window.location.origin,
                                            ""
                                        );

                                        // Check if current path matches or starts with link path
                                        if (
                                            currentPath === linkPath ||
                                            currentPath.startsWith(
                                                linkPath.replace(/\/$/, "") +
                                                    "/"
                                            )
                                        ) {
                                            // Add active class to matching link
                                            link.classList.add("active");

                                            // If it's a submenu link, open parent
                                            const parentTreeview = link.closest(
                                                ".nav-item.has-treeview"
                                            );
                                            if (parentTreeview) {
                                                parentTreeview.classList.add(
                                                    "menu-open"
                                                );
                                            }
                                        }
                                    }
                                });
                        }
                    }, 200);
                }
            }
        })
        .catch((error) => {
            // Silent error handling
        });
}

/**
 * Initialize treeview functionality
 */
function initializeTreeview() {
    // Remove all existing event listeners first
    document
        .querySelectorAll(".nav-item.has-treeview > .nav-link")
        .forEach((item) => {
            item.replaceWith(item.cloneNode(true));
        });

    // Add new event listeners
    const treeviewItems = document.querySelectorAll(
        ".nav-item.has-treeview > .nav-link"
    );

    treeviewItems.forEach((item) => {
        item.addEventListener("click", handleTreeviewClick);
    });

    // Also initialize data-lte-toggle="treeview" functionality
    const navigation = document.getElementById("navigation");
    if (navigation) {
        navigation.setAttribute("data-lte-toggle", "treeview");
        navigation.setAttribute("data-accordion", "false");
    }
}

/**
 * Handle treeview click events
 */
function handleTreeviewClick(e) {
    e.preventDefault();
    e.stopPropagation();

    const parentItem = this.closest(".nav-item");
    const submenu = parentItem.querySelector(".nav-treeview");

    if (submenu) {
        parentItem.classList.toggle("menu-open");
    }
}
