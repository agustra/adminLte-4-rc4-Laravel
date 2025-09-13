export function initializeMenuTreeview() {
    const treeviewItems = document.querySelectorAll(
        ".nav-item.has-treeview > .nav-link"
    );

    treeviewItems.forEach((item) => {
        // Remove any existing listeners
        item.removeEventListener("click", handleTreeviewClick);
        // Add new listener
        item.addEventListener("click", handleTreeviewClick);
    });
}

function handleTreeviewClick(e) {
    e.preventDefault();
    e.stopPropagation();

    const parentItem = this.closest(".nav-item");
    const submenu = parentItem.querySelector(".nav-treeview");

    if (submenu) {
        parentItem.classList.toggle("menu-open");
    }
}