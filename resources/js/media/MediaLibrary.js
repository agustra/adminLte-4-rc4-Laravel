// MediaLibrary - Full implementation (working version)
// Also exported from components/media/MediaLibrary.js for new imports

// Keep old imports for components that still need them
import { fetchAxios } from "../components/handleRequest/fetchAxios.js";
import axiosClient from "../components/apiService/axiosClient.js";
import { MediaUpload } from "./media-upload.js";
import { ImageCropper } from "./image-cropper.js";
import { MediaPicker } from "./media-picker.js";
import { ContextMenu } from "./context-menu.js";

class MediaLibrary {
    constructor(options = {}) {
        // Merge with global config if available
        const config = { ...window.mediaLibraryConfig, ...options };
        
        this.selectedItems = new Set();
        this.currentView = localStorage.getItem("mediaLibraryView") || "grid";
        this.mediaData = [];
        this.filteredData = [];
        this.currentEditItem = null;
        this.currentFolder = config.folder || localStorage.getItem("mediaCurrentFolder") || "";
        this.folders = [];
        this.contextMenuTarget = null;
        this.contextMenuType = null;
        this.config = config;

        // Initialize components
        this.mediaUpload = null;
        this.imageCropper = null;
        this.mediaPicker = null;
        this.contextMenu = null;

        this.init();
        
        // Set global variable if specified
        if (window.mediaLibraryGlobalVar) {
            window[window.mediaLibraryGlobalVar] = this;
        }
    }

    init() {
        this.initializeComponents();
        this.bindEvents();
        this.loadMedia();
    }

    initializeComponents() {
        try {
            // Initialize MediaUpload component
            this.mediaUpload = new MediaUpload({
                collection: this.currentFolder,
                onUploadSuccess: (result, file) => {
                    this.showToast('success', `${file.name} uploaded successfully`);
                },
                onUploadComplete: (queue) => {
                    this.loadMedia();
                    const completed = queue.filter(q => q.status === 'completed').length;
                    if (completed > 0) {
                        this.showToast('success', `${completed} file(s) uploaded successfully`);
                    }
                },
                onUploadError: (error, file) => {
                    this.showToast('error', `Failed to upload ${file.name}: ${error.message}`);
                }
            });
            
            // Make it globally available
            window.mediaUpload = this.mediaUpload;
        } catch (error) {
            console.warn('MediaUpload component not available:', error);
        }

        try {
            // Initialize ImageCropper component
            this.imageCropper = new ImageCropper({
                onSave: (result, item) => {
                    // Update current item data if available
                    if (result && result.url && this.currentEditItem) {
                        this.currentEditItem.url = result.url;
                        this.currentEditItem.name = result.name || this.currentEditItem.name;
                    }
                    
                    // Reload media list to get updated data
                    this.loadMedia();
                },
                onCancel: (item) => {
                    const preview = document.getElementById("mediaPreview");
                    if (preview) preview.style.display = "block";
                }
            });
        } catch (error) {
            console.warn('ImageCropper component not available:', error);
        }

        try {
            // Initialize ContextMenu component
            this.contextMenu = new ContextMenu({
                onCopy: (target, targetFolder) => {
                    this.loadMedia();
                },
                onMove: (target, targetFolder, type) => {
                    this.loadMedia();
                },
                onRename: (target, newName) => {
                    this.loadMedia();
                },
                onDelete: (target) => {
                    // If deleted folder is current folder, navigate to parent
                    if (this.currentFolder === target) {
                        const parentFolder = target.includes('/') ? target.substring(0, target.lastIndexOf('/')) : '';
                        this.navigateToFolder(parentFolder);
                    } else {
                        this.loadMedia();
                    }
                }
            });
        } catch (error) {
            console.warn('ContextMenu component not available:', error);
        }
    }

    bindEvents() {
        // Upload events are now handled by MediaUpload component
        // Just check if elements exist for other functionality

        // View toggle
        document.querySelectorAll(".view-toggle").forEach((btn) => {
            btn.addEventListener("click", (e) => {
                document
                    .querySelectorAll(".view-toggle")
                    .forEach((b) => b.classList.remove("active"));
                e.target.classList.add("active");
                this.currentView = e.target.dataset.view;
                localStorage.setItem("mediaLibraryView", this.currentView);
                this.renderMedia();
            });
        });

        // Set initial view button state
        document.querySelectorAll(".view-toggle").forEach((btn) => {
            if (btn.dataset.view === this.currentView) {
                btn.classList.add("active");
            } else {
                btn.classList.remove("active");
            }
        });

        // Initialize folder breadcrumb if element exists
        if (document.getElementById("folderBreadcrumb")) {
            this.updateBreadcrumb();
        }

        // Filters - with null checks
        const collectionFilter = document.getElementById("collectionFilter");
        const typeFilter = document.getElementById("typeFilter");
        const searchMedia = document.getElementById("searchMedia");

        if (collectionFilter)
            collectionFilter.addEventListener("change", () =>
                this.applyFilters()
            );
        if (typeFilter)
            typeFilter.addEventListener("change", () => this.applyFilters());
        if (searchMedia)
            searchMedia.addEventListener("input", () => this.applyFilters());

        // Selection actions - with null checks
        const deleteSelected = document.getElementById("deleteSelected");
        const clearSelection = document.getElementById("clearSelection");
        const selectAllList = document.getElementById("selectAllList");

        if (deleteSelected)
            deleteSelected.addEventListener("click", () =>
                this.deleteSelected()
            );
        if (clearSelection)
            clearSelection.addEventListener("click", () =>
                this.clearSelection()
            );
        if (selectAllList)
            selectAllList.addEventListener("change", (e) =>
                this.selectAll(e.target.checked)
            );

        // Folder actions - with null checks
        const createFolder = document.getElementById("createFolder");
        const createFolderBtn = document.getElementById("createFolderBtn");

        if (createFolder)
            createFolder.addEventListener("click", () =>
                this.showCreateFolderModal()
            );
        if (createFolderBtn)
            createFolderBtn.addEventListener("click", () =>
                this.createFolder()
            );

        // Context menu is now handled by ContextMenu component

        // Modal actions - with null checks
        const saveMedia = document.getElementById("saveMedia");
        const deleteMedia = document.getElementById("deleteMedia");
        const editImage = document.getElementById("editImage");
        const applyEdit = document.getElementById("applyEdit");
        const cancelEdit = document.getElementById("cancelEdit");

        if (saveMedia)
            saveMedia.addEventListener("click", () => this.saveMediaDetails());
        if (deleteMedia)
            deleteMedia.addEventListener("click", () =>
                this.deleteCurrentMedia()
            );
        if (editImage)
            editImage.addEventListener("click", () => this.startImageEdit());
        if (applyEdit)
            applyEdit.addEventListener("click", () => this.applyImageEdit());
        if (cancelEdit)
            cancelEdit.addEventListener("click", () => this.cancelImageEdit());

        // Cropper controls are now handled by ImageCropper component
    }

    // File handling is now managed by MediaUpload component
    // Update collection when folder changes
    updateUploadCollection() {
        if (this.mediaUpload) {
            this.mediaUpload.setCollection(this.currentFolder);
        }
    }

    async loadMedia() {
        try {
            const params = new URLSearchParams();
            if (this.currentFolder) {
                params.append("folder", this.currentFolder);
            }
            
            // Add cache busting parameter
            params.append("_t", Date.now());

            const response = await axiosClient.get(
                `/api/media-management/json?${params}`
            );

            // Clear existing data first
            this.mediaData = [];
            this.folders = [];
            this.allMediaData = [];
            this.filteredData = [];
            
            // Set new data
            this.mediaData = response.data.data || [];
            this.folders = response.data.folders || [];
            this.allMediaData = response.data.all_data || this.mediaData;
            
            // Re-apply filters if any are active
            this.applyFilters();
            
            this.renderMedia();
            this.updateMediaCount();
        } catch (error) {
            console.error("Failed to load media:", error);
            this.showToast("error", "Failed to load media");

            // Show empty state
            this.mediaData = [];
            this.folders = [];
            this.allMediaData = [];
            this.filteredData = [];
            this.renderMedia();
            this.updateMediaCount();
        }
    }

    applyFilters() {
        const collectionEl = document.getElementById("collectionFilter");
        const typeEl = document.getElementById("typeFilter");
        const searchEl = document.getElementById("searchMedia");
        
        const collection = collectionEl ? collectionEl.value : "";
        const type = typeEl ? typeEl.value : "";
        const search = searchEl ? searchEl.value.toLowerCase() : "";

        // Determine search scope
        const isGlobalSearch = !this.currentFolder && search;
        const searchData = isGlobalSearch ? this.allMediaData : this.mediaData;

        this.filteredData = searchData.filter((item) => {
            const matchCollection =
                !collection || item.collection === collection;
            const matchType =
                !type || (item.mime_type && item.mime_type.startsWith(type));
            const matchSearch =
                !search ||
                item.name.toLowerCase().includes(search) ||
                item.file_name.toLowerCase().includes(search);

            return matchCollection && matchType && matchSearch;
        });

        // Update search indicator
        this.updateSearchIndicator(isGlobalSearch, search);
        this.renderMedia();
        this.updateMediaCount();
    }

    updateSearchIndicator(isGlobal, searchTerm) {
        const searchInput = document.getElementById("searchMedia");
        const searchScope = document.getElementById("searchScope");
        
        if (!searchInput) return;

        if (searchTerm) {
            const scope = isGlobal ? "🌐 Searching in all folders" : "📁 Searching in current folder";
            searchInput.title = `${scope}: "${searchTerm}"`;
            searchInput.style.borderColor = isGlobal ? "#007bff" : "#28a745";
            
            if (searchScope) {
                searchScope.textContent = scope;
                searchScope.style.display = "block";
                searchScope.style.color = isGlobal ? "#007bff" : "#28a745";
            }
        } else {
            searchInput.title = "Search media...";
            searchInput.style.borderColor = "";
            
            if (searchScope) {
                searchScope.style.display = "none";
            }
        }
    }

    renderMedia() {
        // Skip rendering if essential elements don't exist
        const grid = document.getElementById("mediaGrid");
        if (!grid) {
            return;
        }

        if (this.currentView === "grid") {
            this.renderGridView();
        } else {
            this.renderListView();
        }
    }

    renderGridView() {
        const grid = document.getElementById("mediaGrid");
        const list = document.getElementById("mediaList");

        // Skip if elements don't exist
        if (!grid) {
            return;
        }

        if (list) {
            list.style.display = "none";
        }
        grid.style.display = "grid";

        if (this.filteredData.length === 0 && this.folders.length === 0) {
            grid.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No media files found</p>
                </div>
            `;
            return;
        }

        // Render folders first
        let foldersHtml = "";
        if (this.folders && this.folders.length > 0) {
            foldersHtml = this.folders
                .map(
                    (folder) => `
                <div class="media-item folder-item" data-folder="${
                    folder.path
                }">
                    <div style="width: 100%; height: 120px; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                        <i class="fas fa-folder text-warning" style="font-size: 48px;"></i>
                    </div>
                    <div class="mt-2">
                        <small class="fw-bold d-block text-truncate" title="${
                            folder.name
                        }">📁 ${folder.name}</small>
                        <small class="text-muted">${folder.count || 0} ${
                        folder.count === 1 ? "item" : "items"
                    }</small>
                    </div>
                </div>
            `
                )
                .join("");
        }

        // Render media files
        let mediaHtml = this.filteredData
            .map(
                (item) => `
            <div class="media-item ${
                this.selectedItems.has(item.id) ? "selected" : ""
            }" 
                 data-id="${item.id}" data-url="${item.url}" data-name="${
                    item.name
                }">
                ${this.getMediaPreview(item)}
                <div class="mt-2">
                    <small class="fw-bold d-block text-truncate" title="${
                        item.name
                    }">${item.name}</small>
                    <small class="text-muted">${item.size}</small>
                </div>
                <div class="media-item-overlay">
                    <input type="checkbox" ${
                        this.selectedItems.has(item.id) ? "checked" : ""
                    }>
                </div>
                <div class="media-item-actions" style="position: absolute; top: 5px; right: 5px; display: none;">
                    <button class="btn btn-sm btn-primary btn-edit-grid" data-id="${
                        item.id
                    }" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-delete-grid" data-id="${
                        item.id
                    }" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `
            )
            .join("");

        // Combine folders and media
        grid.innerHTML = foldersHtml + mediaHtml;

        // Add folder click events
        grid.querySelectorAll(".folder-item").forEach((folder) => {
            folder.addEventListener("dblclick", (e) => {
                e.preventDefault();
                const folderPath = e.currentTarget.dataset.folder;
                this.navigateToFolder(folderPath);
            });
        });

        // Add event listeners after rendering
        grid.querySelectorAll(".media-item:not(.folder-item)").forEach(
            (item) => {
                const id = parseInt(item.dataset.id);
                if (!id) return; // Skip if no valid ID

                // Show/hide action buttons on hover
                item.addEventListener("mouseenter", () => {
                    const actions = item.querySelector(".media-item-actions");
                    if (actions) actions.style.display = "block";
                });

                item.addEventListener("mouseleave", () => {
                    const actions = item.querySelector(".media-item-actions");
                    if (actions) actions.style.display = "none";
                });

                item.addEventListener("click", (e) => {
                    if (
                        e.target.type === "checkbox" ||
                        e.target.closest("button")
                    )
                        return;
                    this.toggleSelection(id);
                });

                item.addEventListener("dblclick", (e) => {
                    e.preventDefault();

                    // Check if in picker mode (iframe)
                    const urlParams = new URLSearchParams(
                        window.location.search
                    );
                    if (
                        urlParams.get("mode") === "picker" &&
                        window.parent !== window
                    ) {
                        const mediaItem = this.mediaData.find(
                            (m) => m.id === id
                        );
                        if (mediaItem) {
                            window.parent.postMessage(
                                {
                                    type: "MEDIA_SELECTED",
                                    media: {
                                        id: mediaItem.id,
                                        url: mediaItem.url,
                                        name: mediaItem.name,
                                    },
                                },
                                "*"
                            );
                            return;
                        }
                    }

                    this.showMediaDetails(id);
                });

                const checkbox = item.querySelector('input[type="checkbox"]');
                if (checkbox) {
                    checkbox.addEventListener("change", (e) => {
                        e.stopPropagation();
                        this.toggleSelection(id);
                    });
                }

                const editBtn = item.querySelector(".btn-edit-grid");
                if (editBtn) {
                    editBtn.addEventListener("click", (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        this.showMediaDetails(id);
                    });
                }

                const deleteBtn = item.querySelector(".btn-delete-grid");
                if (deleteBtn) {
                    deleteBtn.addEventListener("click", (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        this.deleteMedia(id);
                    });
                }
            }
        );
    }

    renderListView() {
        const grid = document.getElementById("mediaGrid");
        const list = document.getElementById("mediaList");
        const tbody = document.getElementById("mediaListBody");

        // Skip if elements don't exist
        if (!grid || !list || !tbody) {
            return;
        }

        grid.style.display = "none";
        list.style.display = "block";

        if (this.filteredData.length === 0 && this.folders.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <i class="fas fa-folder-open fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No media files found</p>
                    </td>
                </tr>
            `;
            return;
        }

        // Render folders first
        let foldersHtml = "";
        if (this.folders && this.folders.length > 0) {
            foldersHtml = this.folders
                .map(
                    (folder) => `
                <tr class="folder-list-item" data-folder="${folder.path}">
                    <td></td>
                    <td>
                        <i class="fas fa-folder fa-2x text-warning"></i>
                    </td>
                    <td>
                        <div class="fw-bold">📁 ${folder.name}</div>
                        <small class="text-muted">${folder.count || 0} ${
                        folder.count === 1 ? "item" : "items"
                    }</small>
                    </td>
                    <td><span class="badge bg-warning">Folder</span></td>
                    <td>-</td>
                    <td>-</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary btn-open-folder" data-folder="${
                            folder.path
                        }" title="Open">
                            <i class="fas fa-folder-open"></i>
                        </button>
                    </td>
                </tr>
            `
                )
                .join("");
        }

        // Render media files
        let mediaHtml = this.filteredData
            .map(
                (item) => `
            <tr class="${
                this.selectedItems.has(item.id) ? "table-primary" : ""
            }" data-id="${item.id}">
                <td>
                    <input type="checkbox" ${
                        this.selectedItems.has(item.id) ? "checked" : ""
                    }>
                </td>
                <td>${this.getMediaPreview(item, "small")}</td>
                <td>
                    <div class="fw-bold">${item.name}</div>
                    <small class="text-muted">${item.file_name}</small>
                </td>
                <td><span class="badge bg-secondary">${
                    item.mime_type?.split("/")[0] || "unknown"
                }</span></td>
                <td>${item.size}</td>
                <td>${item.created_at}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary btn-edit" data-id="${
                        item.id
                    }">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger btn-delete-single" data-id="${
                        item.id
                    }">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `
            )
            .join("");

        // Combine folders and media
        tbody.innerHTML = foldersHtml + mediaHtml;

        // Add event listeners for folders
        tbody.querySelectorAll(".folder-list-item").forEach((row) => {
            const folderPath = row.dataset.folder;

            row.addEventListener("dblclick", (e) => {
                e.preventDefault();
                this.navigateToFolder(folderPath);
            });

            const openBtn = row.querySelector(".btn-open-folder");
            openBtn.addEventListener("click", (e) => {
                e.preventDefault();
                this.navigateToFolder(folderPath);
            });
        });

        // Add event listeners for media files
        tbody.querySelectorAll("tr[data-id]").forEach((row) => {
            const id = parseInt(row.dataset.id);

            row.addEventListener("dblclick", (e) => {
                e.preventDefault();
                this.showMediaDetails(id);
            });

            const checkbox = row.querySelector('input[type="checkbox"]');
            if (checkbox) {
                checkbox.addEventListener("change", (e) => {
                    e.stopPropagation();
                    this.toggleSelection(id);
                });
            }

            const editBtn = row.querySelector(".btn-edit");
            if (editBtn) {
                editBtn.addEventListener("click", (e) => {
                    e.preventDefault();
                    this.showMediaDetails(id);
                });
            }

            const deleteBtn = row.querySelector(".btn-delete-single");
            if (deleteBtn) {
                deleteBtn.addEventListener("click", (e) => {
                    e.preventDefault();
                    this.deleteMedia(id);
                });
            }
        });
    }

    getMediaPreview(item, size = "normal") {
        let imgClass = "";
        if (size === "small") {
            imgClass = "width: 40px; height: 40px;";
        } else if (size === "modal") {
            imgClass =
                "max-width: 100%; max-height: 300px; width: auto; height: auto;";
        }

        if (item.mime_type && item.mime_type.startsWith("image/")) {
            return `<img src="${item.url}" alt="${item.name}" style="${imgClass} object-fit: contain; border-radius: 4px;">`;
        }

        const iconSize = size === "small" ? "fa-lg" : "fa-3x";
        const icons = {
            video: "fa-video",
            audio: "fa-music",
            application: "fa-file-alt",
            text: "fa-file-text",
        };

        const type = item.mime_type?.split("/")[0] || "unknown";
        const icon = icons[type] || "fa-file";

        return `<div class="file-icon"><i class="fas ${icon} ${iconSize}"></i></div>`;
    }

    toggleSelection(id) {
        if (this.selectedItems.has(id)) {
            this.selectedItems.delete(id);
        } else {
            this.selectedItems.add(id);
        }

        this.updateSelectionUI();
        this.renderMedia();
    }

    selectAll(checked) {
        if (checked) {
            this.filteredData.forEach((item) =>
                this.selectedItems.add(item.id)
            );
        } else {
            this.selectedItems.clear();
        }

        this.updateSelectionUI();
        this.renderMedia();
    }

    clearSelection() {
        this.selectedItems.clear();
        this.updateSelectionUI();
        this.renderMedia();
    }

    updateSelectionUI() {
        const count = this.selectedItems.size;
        const deleteBtn = document.getElementById("deleteSelected");
        const selectedInfo = document.getElementById("selectedInfo");
        const selectedCount = document.getElementById("selectedCount");

        if (count > 0) {
            deleteBtn.style.display = "inline-block";
            selectedInfo.style.display = "block";
            selectedCount.textContent = count;
        } else {
            deleteBtn.style.display = "none";
            selectedInfo.style.display = "none";
        }
    }

    updateMediaCount() {
        const mediaCount = document.getElementById("mediaCount");
        if (mediaCount) {
            mediaCount.textContent = `${this.filteredData.length} items`;
        }
    }

    showMediaDetails(id) {
        const item = this.mediaData.find((m) => m.id === id);
        if (!item) {
            this.showToast("error", "Media tidak ditemukan dalam daftar saat ini. Memuat ulang...");
            this.loadMedia();
            return;
        }

        // Populate modal
        document.getElementById("mediaTitle").value = item.name || "";
        document.getElementById("mediaAlt").value =
            item.custom_properties?.alt || "";
        document.getElementById("mediaDescription").value =
            item.custom_properties?.description || "";
        document.getElementById("mediaCollection").value =
            item.collection || "default";
        document.getElementById("mediaFileName").textContent =
            item.file_name || "";
        document.getElementById("mediaFileSize").textContent = item.size || "";
        document.getElementById("mediaFileType").textContent =
            item.mime_type || "";
        document.getElementById("mediaUploadDate").textContent =
            item.created_at || "";

        // Show preview
        const preview = document.getElementById("mediaPreview");
        preview.innerHTML = this.getMediaPreview(item, "modal");

        // Show/hide edit image button
        const editImageBtn = document.getElementById("editImage");
        if (item.mime_type && item.mime_type.startsWith("image/")) {
            editImageBtn.style.display = "inline-block";
        } else {
            editImageBtn.style.display = "none";
        }

        // Store current item
        this.currentMediaId = id;
        this.currentEditItem = item;

        // Show modal
        const modal = document.getElementById("mediaDetailModal");
        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
    }

    async deleteSelected() {
        if (this.selectedItems.size === 0) return;

        if (!confirm(`Delete ${this.selectedItems.size} selected items?`))
            return;

        try {
            await axiosClient.post("/api/media-management/multiple/delete", {
                ids: Array.from(this.selectedItems),
            });

            this.showToast("success", "Selected items deleted successfully");
            this.selectedItems.clear();
            this.loadMedia();
        } catch (error) {
            this.showToast("error", "Failed to delete selected items");
        }
    }

    async deleteMedia(id) {
        if (!confirm("Delete this media file?")) return;

        try {
            const response = await axiosClient.delete(`/api/media-management/${id}`);
            
            if (response.data.status === 'success') {
                // Remove from local data immediately
                this.mediaData = this.mediaData.filter(item => item.id !== id);
                this.allMediaData = this.allMediaData.filter(item => item.id !== id);
                this.filteredData = this.filteredData.filter(item => item.id !== id);
                
                // Remove from selection if selected
                this.selectedItems.delete(id);
                
                // Update UI immediately
                this.renderMedia();
                this.updateMediaCount();
                this.updateSelectionUI();
                
                this.showToast("success", response.data.message || "Media deleted successfully");
                
                // Reload data to ensure consistency
                await this.loadMedia();
            } else {
                this.showToast("error", response.data.message || "Failed to delete media");
            }
        } catch (error) {
            console.error('Delete media error:', error);
            
            // Handle different error responses
            if (error.response && error.response.data) {
                const errorData = error.response.data;
                if (errorData.message) {
                    this.showToast("error", errorData.message);
                } else if (errorData.status === 'error') {
                    this.showToast("error", "Cannot delete this media");
                } else {
                    this.showToast("error", "Failed to delete media");
                }
            } else {
                this.showToast("error", "Failed to delete media");
            }
        }
    }

    async saveMediaDetails() {
        if (!this.currentMediaId) return;

        const data = {
            name: document.getElementById("mediaTitle").value,
            collection_name: document.getElementById("mediaCollection").value,
            custom_properties: {
                alt: document.getElementById("mediaAlt").value,
                description: document.getElementById("mediaDescription").value,
            },
        };
        
        try {
            const response = await axiosClient.put(
                `/api/media-management/${this.currentMediaId}`,
                data
            );

            this.showToast("success", "Media details updated successfully");
            
            // Hide modal first
            bootstrap.Modal.getInstance(
                document.getElementById("mediaDetailModal")
            ).hide();
            
            // Force reload with delay to ensure modal is closed
            setTimeout(() => {
                this.loadMedia();
            }, 300);
            
        } catch (error) {
            console.error('Save error:', error);
            
            if (error.response && error.response.status === 404) {
                this.showToast("error", "Media tidak ditemukan. Mungkin sudah dihapus. Memuat ulang daftar media...");
                
                // Hide modal
                bootstrap.Modal.getInstance(
                    document.getElementById("mediaDetailModal")
                ).hide();
                
                // Reload media list
                setTimeout(() => {
                    this.loadMedia();
                }, 300);
            } else {
                const errorMsg = error.response?.data?.message || "Failed to update media details";
                this.showToast("error", errorMsg);
            }
        }
    }

    async deleteCurrentMedia() {
        if (this.currentMediaId) {
            await this.deleteMedia(this.currentMediaId);
            bootstrap.Modal.getInstance(
                document.getElementById("mediaDetailModal")
            ).hide();
        }
    }

    startImageEdit() {
        if (
            !this.currentEditItem ||
            !this.currentEditItem.mime_type.startsWith("image/")
        )
            return;

        // Hide preview
        const preview = document.getElementById("mediaPreview");
        if (preview) preview.style.display = "none";

        // Start editing with ImageCropper component
        this.imageCropper.startEdit(this.currentEditItem.url, this.currentEditItem);
    }

    cancelImageEdit() {
        // Cancel editing with ImageCropper component
        this.imageCropper.cancel();
    }

    // Image cropping methods are now handled by ImageCropper component
    // These methods are kept for backward compatibility if needed
    setCropMode(aspectRatio) {
        if (this.imageCropper) {
            this.imageCropper.setCropMode(aspectRatio);
        }
    }

    async applyImageEdit() {
        if (this.imageCropper) {
            await this.imageCropper.save();
        }
    }

    showCreateFolderModal() {
        document.getElementById("folderName").value = "";
        new bootstrap.Modal(
            document.getElementById("createFolderModal")
        ).show();
    }

    async createFolder() {
        const folderName = document.getElementById("folderName").value.trim();
        if (!folderName) {
            this.showToast("error", "Please enter a folder name");
            return;
        }

        try {
            const folderPath = this.currentFolder
                ? `${this.currentFolder}/${folderName}`
                : folderName;

            await axiosClient.post("/api/media/folders", {
                name: folderName,
                path: folderPath,
                parent: this.currentFolder,
            });

            this.showToast("success", "Folder created successfully");
            bootstrap.Modal.getInstance(
                document.getElementById("createFolderModal")
            ).hide();
            this.loadMedia();
        } catch (error) {
            this.showToast("error", "Failed to create folder");
        }
    }

    navigateToFolder(folderPath) {
        this.currentFolder = folderPath;
        localStorage.setItem("mediaCurrentFolder", folderPath);
        this.updateBreadcrumb();
        this.updateUploadCollection();
        this.loadMedia();
    }

    updateBreadcrumb() {
        const breadcrumb = document.getElementById("folderBreadcrumb");
        if (!breadcrumb) {
            return;
        }

        let html =
            '<li class="breadcrumb-item"><a href="#" data-folder="">📁 Root</a></li>';

        if (this.currentFolder) {
            const parts = this.currentFolder.split("/");
            let currentPath = "";

            parts.forEach((part, index) => {
                currentPath += (index > 0 ? "/" : "") + part;
                const isLast = index === parts.length - 1;

                if (isLast) {
                    html += `<li class="breadcrumb-item active">📁 ${part}</li>`;
                } else {
                    html += `<li class="breadcrumb-item"><a href="#" data-folder="${currentPath}">📁 ${part}</a></li>`;
                }
            });
        }

        breadcrumb.innerHTML = html;

        // Add click events to breadcrumb links
        breadcrumb.querySelectorAll("a").forEach((link) => {
            link.addEventListener("click", (e) => {
                e.preventDefault();
                this.navigateToFolder(e.target.dataset.folder);
            });
        });
    }

    // Context menu methods are now handled by ContextMenu component
    // These methods are kept for backward compatibility if needed
    hideContextMenu() {
        if (this.contextMenu) {
            this.contextMenu.hide();
        }
    }

    async copyToFolder(targetFolder) {
        if (this.contextMenu) {
            await this.contextMenu.copyToFolder(targetFolder);
        }
    }

    async moveToFolder(targetFolder) {
        if (this.contextMenu) {
            await this.contextMenu.moveToFolder(targetFolder);
        }
    }

    showToast(type, message) {
        if (typeof window.showToast === "function") {
            window.showToast(message, type);
        } else {
            alert(message);
        }
    }
}

// Export the class for module usage
export { MediaLibrary };

// Make MediaLibrary available globally
window.MediaLibrary = MediaLibrary;
