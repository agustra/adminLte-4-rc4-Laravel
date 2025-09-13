/**
 * Sidebar Utilities
 * General utilities for sidebar management across pages
 */

// Collapse sidebar
export function collapseSidebar() {
    const body = document.body;
    body.classList.remove("sidebar-open");
    body.classList.add("sidebar-collapse");
}

// Expand sidebar
export function expandSidebar() {
    const body = document.body;
    body.classList.remove("sidebar-collapse");
    body.classList.add("sidebar-open");
}

// Toggle sidebar
export function toggleSidebar() {
    const body = document.body;
    if (body.classList.contains("sidebar-collapse")) {
        expandSidebar();
    } else {
        collapseSidebar();
    }
}

// Set sidebar state
export function setSidebarState(collapsed = true) {
    if (collapsed) {
        collapseSidebar();
    } else {
        expandSidebar();
    }
}