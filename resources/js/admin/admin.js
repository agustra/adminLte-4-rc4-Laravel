// resources/js/admin/admin.js
const page = (document.body.dataset.page || "").toLowerCase();

const modules = {
    roles: () => import("./roles/roles.js"),
    permissions: () => import("./permissions/permissions.js"),
    menus: () => import("./menus/menus.js"),
    backup: () => import("./backup.js"),
    users: () => import("./users/users.js"),
    settings: () => import("./settings.js"),
    "badge-configs": () => import("./badge-configs/badge-configs.js"),
    "controller-permissions": () =>
        import("./controller-permissions/controller-permissions.js"),
};

if (modules[page]) {
    modules[page]().then((mod) => {
        // kalau modul export default function → jalankan otomatis
        if (typeof mod.default === "function") {
            mod.default();
        }
    }).catch((error) => {
        console.error(`❌ Failed to load module "${page}":`, error);
    });
} else {
    console.warn(`⚠️ No module found for page: "${page}"`);
}
