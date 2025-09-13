// Reusable Permissions Popup Component

/**
 * Create permissions display with popup for large data
 * @param {Array} permissions - Array of permissions
 * @param {Object} item - Item data (role/user)
 * @param {string} type - Type: 'role' or 'user'
 * @returns {string} HTML string
 */
export function formatPermissionsColumn(permissions, item, type = "role") {
    if (
        !permissions ||
        !Array.isArray(permissions) ||
        permissions.length === 0
    ) {
        return '<span class="text-muted">No permissions</span>';
    }

    const count = permissions.length;
    const itemId = item.id;
    const itemName = item.name;

    if (count <= 3) {
        // Show all permissions if 3 or less
        return permissions
            .map(
                (p) =>
                    `<span class="badge bg-primary me-1">${p.name || p}</span>`
            )
            .join("");
    } else {
        // Show count with clickable popup
        const firstThree = permissions
            .slice(0, 3)
            .map(
                (p) =>
                    `<span class="badge bg-primary me-1">${p.name || p}</span>`
            )
            .join("");

        return `
            ${firstThree}
            <button class="btn btn-sm btn-outline-secondary show-permissions-btn" 
                    data-item-id="${itemId}"
                    data-item-name="${itemName}"
                    data-item-type="${type}">
                +${count - 3} more
            </button>
        `;
    }
}

/**
 * Initialize permissions popup handlers
 */
export function initializePermissionsPopup() {
    if (!document.body.hasAttribute("data-permissions-popup-initialized")) {
        document.body.addEventListener("click", function (e) {
            if (e.target.classList.contains("show-permissions-btn")) {
                e.preventDefault();
                e.stopPropagation();

                const itemId = e.target.dataset.itemId;
                const itemName = e.target.dataset.itemName;
                const itemType = e.target.dataset.itemType;

                showPermissionsModal(itemId, itemName, itemType);
            }
        });

        document.body.setAttribute("data-permissions-popup-initialized", "true");
    }
}

/**
 * Show permissions modal with server-side pagination
 */
export async function showPermissionsModal(itemId, itemName, type = "role") {
    const typeLabel = type === "user" ? "User" : "Role";
    const icon = type === "user" ? "fa-user" : "fa-shield-alt";
    
    const modalHtml = `
        <div class="modal fade" id="permissionsModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas ${icon} me-2"></i>Permissions for ${typeLabel}: <strong>${itemName}</strong>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div id="permissionsContent">
                            <div class="text-center p-4">
                                <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
                                <div>Loading permissions...</div>
                            </div>
                        </div>
                        <div id="paginationContainer"></div>
                        <div id="permissionsSummary" class="mt-4" style="display: none;">
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <span id="totalInfo"></span>
                                </small>
                            </div>
                        </div>
                    </div>
                    <style>
                    .permission-card {
                        transition: all 0.3s ease;
                        border: 1px solid #e9ecef !important;
                    }
                    .permission-card:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                        border-color: #007bff !important;
                    }
                    .permission-list {
                        max-height: 200px;
                        overflow-y: auto;
                    }
                    .permission-list::-webkit-scrollbar {
                        width: 4px;
                    }
                    .permission-list::-webkit-scrollbar-track {
                        background: #f1f1f1;
                        border-radius: 2px;
                    }
                    .permission-list::-webkit-scrollbar-thumb {
                        background: #c1c1c1;
                        border-radius: 2px;
                    }
                    </style>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove existing modal
    const existingModal = document.getElementById("permissionsModal");
    if (existingModal) {
        existingModal.remove();
    }

    // Add modal to body
    document.body.insertAdjacentHTML("beforeend", modalHtml);

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById("permissionsModal"));
    modal.show();

    // Load first page
    await loadPermissionsPage(itemId, type, 1, 6);

    // Clean up modal after hide
    document.getElementById("permissionsModal").addEventListener("hidden.bs.modal", function () {
        this.remove();
    });
}

/**
 * Load permissions page from server
 */
async function loadPermissionsPage(itemId, type, page, limit) {
    try {
        const { default: axiosClient } = await import('@api/axiosClient.js');
        
        const endpoint = type === 'user' 
            ? `/api/users/${itemId}/permissions/paginated`
            : `/api/roles/${itemId}/permissions/paginated`;
            
        const response = await axiosClient.get(endpoint, {
            params: { page, limit }
        });
        
        renderPermissionsPage(response.data, page, limit, itemId, type);
        
    } catch (error) {
        console.error('Error loading permissions:', error);
        document.getElementById('permissionsContent').innerHTML = `
            <div class="text-center p-4 text-danger">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                <div>Error loading permissions</div>
            </div>
        `;
    }
}

/**
 * Render permissions page
 */
function renderPermissionsPage(data, currentPage, itemsPerPage, itemId, type) {
    const { modules, total, totalPages, totalModules } = data;
    
    const moduleIcons = {
        users: "bi-people-fill",
        roles: "bi-shield-fill-check", 
        permissions: "bi-key-fill",
        menus: "bi-list-ul",
        media: "bi-image-fill",
        backup: "bi-archive-fill",
        settings: "bi-gear-fill",
        general: "bi-folder2-open",
    };

    // Render content
    const contentHtml = `
        <div class="row g-3">
            ${modules.map(module => `
                <div class="col-md-6 col-lg-4">
                    <div class="permission-card border rounded-3 p-3 h-100 bg-light">
                        <div class="d-flex align-items-center mb-2">
                            <i class="${moduleIcons[module.name] || moduleIcons.general} text-warning me-2" style="font-size: 1.2rem;"></i>
                            <h6 class="mb-0 fw-bold text-dark">${module.name.charAt(0).toUpperCase() + module.name.slice(1)}</h6>
                            <span class="badge bg-primary ms-auto">${module.permissions.length}</span>
                        </div>
                        <div class="permission-list">
                            ${module.permissions.map(p => `
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-check-circle-fill text-success me-2" style="font-size: 0.8rem;"></i>
                                    <span class="text-muted small">${p.name}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;

    document.getElementById('permissionsContent').innerHTML = contentHtml;

    // Render pagination
    if (totalPages > 1) {
        renderPagination(currentPage, totalPages, itemId, type, itemsPerPage);
    }

    // Show summary
    document.getElementById('totalInfo').textContent = `Total: ${total} permissions across ${totalModules} modules`;
    document.getElementById('permissionsSummary').style.display = 'block';
}

/**
 * Render pagination
 */
function renderPagination(currentPage, totalPages, itemId, type, itemsPerPage) {
    let paginationHtml = '<nav class="mt-3"><ul class="pagination pagination-sm justify-content-center">';
    
    // Previous button
    paginationHtml += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage - 1}">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>
    `;
    
    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        paginationHtml += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>
        `;
    }
    
    // Next button
    paginationHtml += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    `;
    
    paginationHtml += '</ul></nav>';
    document.getElementById('paginationContainer').innerHTML = paginationHtml;

    // Add click events
    document.querySelectorAll('#paginationContainer .page-link').forEach(link => {
        link.addEventListener('click', async (e) => {
            e.preventDefault();
            const page = parseInt(e.target.closest('a').dataset.page);
            if (page && page !== currentPage && page >= 1 && page <= totalPages) {
                // Show loading
                document.getElementById('permissionsContent').innerHTML = `
                    <div class="text-center p-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
                        <div>Loading permissions...</div>
                    </div>
                `;
                await loadPermissionsPage(itemId, type, page, itemsPerPage);
            }
        });
    });
}