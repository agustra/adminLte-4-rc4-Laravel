import axiosClient from "@api/axiosClient.js";

/**
 * Context Menu Component for Media Library
 * Handles right-click operations on media items and folders
 */
class ContextMenu {
    constructor(options = {}) {
        this.options = {
            menuId: 'contextMenu',
            mediaMenuId: 'mediaContextMenu',
            folderMenuId: 'folderContextMenu',
            onCopy: null,
            onMove: null,
            onRename: null,
            onDelete: null,
            ...options
        };
        
        this.target = null;
        this.targetType = null; // 'media' or 'folder'
        this.isVisible = false;
        
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Context menu trigger
        document.addEventListener('contextmenu', (e) => this.handleContextMenu(e));
        
        // Hide context menu on click
        document.addEventListener('click', () => this.hide());
        
        // Prevent context menu from closing when clicking inside it
        const contextMenu = document.getElementById(this.options.menuId);
        if (contextMenu) {
            contextMenu.addEventListener('click', (e) => e.stopPropagation());
        }

        // Bind action buttons
        this.bindActionButtons();
    }

    bindActionButtons() {
        // Copy to root
        const copyToRoot = document.getElementById('copyToRoot');
        if (copyToRoot && !copyToRoot.dataset.bound) {
            copyToRoot.addEventListener('click', () => this.copyToFolder(''));
            copyToRoot.dataset.bound = 'true';
        }

        // Copy to folder
        const copyToFolder = document.getElementById('copyToFolder');
        if (copyToFolder && !copyToFolder.dataset.bound) {
            copyToFolder.addEventListener('click', () => this.showFolderModal('copy'));
            copyToFolder.dataset.bound = 'true';
        }

        // Move to root
        const moveToRoot = document.getElementById('moveToRoot');
        if (moveToRoot && !moveToRoot.dataset.bound) {
            moveToRoot.addEventListener('click', () => this.moveToFolder(''));
            moveToRoot.dataset.bound = 'true';
        }

        // Move to folder
        const moveToFolder = document.getElementById('moveToFolder');
        if (moveToFolder && !moveToFolder.dataset.bound) {
            moveToFolder.addEventListener('click', () => this.showFolderModal('move'));
            moveToFolder.dataset.bound = 'true';
        }

        // Move folder to root
        const moveFolderToRoot = document.getElementById('moveFolderToRoot');
        if (moveFolderToRoot && !moveFolderToRoot.dataset.bound) {
            moveFolderToRoot.addEventListener('click', () => this.moveToFolder(''));
            moveFolderToRoot.dataset.bound = 'true';
        }

        // Move folder to another folder
        const moveFolderTo = document.getElementById('moveFolderTo');
        if (moveFolderTo && !moveFolderTo.dataset.bound) {
            moveFolderTo.addEventListener('click', () => this.showFolderModal('moveFolder'));
            moveFolderTo.dataset.bound = 'true';
        }

        // Rename folder
        const renameFolder = document.getElementById('renameFolder');
        if (renameFolder && !renameFolder.dataset.bound) {
            renameFolder.addEventListener('click', () => this.showRenameFolderModal());
            renameFolder.dataset.bound = 'true';
        }

        // Rename folder confirm button
        const renameFolderBtn = document.getElementById('renameFolderBtn');
        if (renameFolderBtn && !renameFolderBtn.dataset.bound) {
            renameFolderBtn.addEventListener('click', () => this.renameFolder());
            renameFolderBtn.dataset.bound = 'true';
        }

        // Delete folder
        const deleteFolder = document.getElementById('deleteFolder');
        if (deleteFolder && !deleteFolder.dataset.bound) {
            deleteFolder.addEventListener('click', () => this.deleteFolder());
            deleteFolder.dataset.bound = 'true';
        }
    }

    /**
     * Handle context menu event
     */
    handleContextMenu(e) {
        const mediaItem = e.target.closest('.media-item:not(.folder-item)');
        const folderItem = e.target.closest('.folder-item');

        if (!mediaItem && !folderItem) {
            this.hide();
            return;
        }

        e.preventDefault();

        if (mediaItem) {
            this.showForMedia(mediaItem, e.clientX, e.clientY);
        } else if (folderItem) {
            this.showForFolder(folderItem, e.clientX, e.clientY);
        }
    }

    /**
     * Show context menu for media item
     */
    async showForMedia(mediaItem, x, y) {
        this.target = parseInt(mediaItem.dataset.id);
        this.targetType = 'media';

        // Show media context menu, hide folder menu
        const mediaMenu = document.getElementById(this.options.mediaMenuId);
        const folderMenu = document.getElementById(this.options.folderMenuId);
        
        if (mediaMenu) mediaMenu.style.display = 'block';
        if (folderMenu) folderMenu.style.display = 'none';

        // Populate folder lists
        await this.populateContextFolders();

        // Show context menu
        this.show(x, y);
    }

    /**
     * Show context menu for folder item
     */
    async showForFolder(folderItem, x, y) {
        this.target = folderItem.dataset.folder;
        this.targetType = 'folder';

        // Show folder context menu, hide media menu
        const mediaMenu = document.getElementById(this.options.mediaMenuId);
        const folderMenu = document.getElementById(this.options.folderMenuId);
        
        if (mediaMenu) mediaMenu.style.display = 'none';
        if (folderMenu) folderMenu.style.display = 'block';

        // Populate folder lists for folder operations
        await this.populateContextFoldersForFolder();

        // Show context menu
        this.show(x, y);
    }

    /**
     * Show context menu at position
     */
    show(x, y) {
        const contextMenu = document.getElementById(this.options.menuId);
        if (!contextMenu) return;

        contextMenu.style.display = 'block';
        contextMenu.style.position = 'fixed';

        // Calculate position to avoid viewport overflow
        const menuWidth = 300; // max-width from CSS
        const menuHeight = 400; // max-height from CSS

        let left = x;
        let top = y;

        // Check right boundary
        if (left + menuWidth > window.innerWidth) {
            left = window.innerWidth - menuWidth - 10;
        }

        // Check bottom boundary
        if (top + menuHeight > window.innerHeight) {
            top = window.innerHeight - menuHeight - 10;
        }

        // Ensure minimum distance from edges
        left = Math.max(10, left);
        top = Math.max(10, top);

        contextMenu.style.left = left + 'px';
        contextMenu.style.top = top + 'px';

        this.isVisible = true;
    }

    /**
     * Hide context menu
     */
    hide() {
        const contextMenu = document.getElementById(this.options.menuId);
        if (contextMenu) {
            contextMenu.style.display = 'none';
        }
        
        this.target = null;
        this.targetType = null;
        this.isVisible = false;
    }

    /**
     * Populate folder lists for media operations
     */
    async populateContextFolders() {
        try {
            const response = await axiosClient.get('/api/media/folders');
            const folders = response.data.data || [];

            const folderHtml = folders.map(folder => `
                <a class="dropdown-item" href="#" data-folder="${folder.path}">
                    <i class="fas fa-folder"></i> ${folder.name}
                </a>
            `).join('');

            // Populate copy section
            const folderListCopy = document.getElementById('folderListCopy');
            if (folderListCopy) {
                folderListCopy.innerHTML = folderHtml;
                folderListCopy.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.copyToFolder(e.target.dataset.folder);
                    });
                });
            }

            // Populate move section
            const folderListMove = document.getElementById('folderListMove');
            if (folderListMove) {
                folderListMove.innerHTML = folderHtml;
                folderListMove.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.moveToFolder(e.target.dataset.folder);
                    });
                });
            }
        } catch (error) {
            console.error('Failed to load folders for context menu');
        }
    }

    /**
     * Populate folder lists for folder operations
     */
    async populateContextFoldersForFolder() {
        try {
            const response = await axiosClient.get('/api/media/folders');
            const folders = response.data.data || [];

            const folderListForFolder = document.getElementById('folderListForFolder');
            if (folderListForFolder) {
                folderListForFolder.innerHTML = folders.map(folder => `
                    <a class="dropdown-item" href="#" data-folder="${folder.path}">
                        <i class="fas fa-folder"></i> ${folder.name}
                    </a>
                `).join('');

                // Add click events
                folderListForFolder.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.moveToFolder(e.target.dataset.folder);
                    });
                });
            }
        } catch (error) {
            console.error('Failed to load folders for folder context menu');
        }
    }

    /**
     * Copy media to folder
     */
    async copyToFolder(targetFolder) {
        // Use saved target if current target is null (after modal)
        const target = this.target || this.savedTarget;
        const targetType = this.targetType || this.savedTargetType;
        
        if (!target) {
            console.error('No target found for copy operation');
            return;
        }

        try {
            if (targetType === 'media') {
                await axiosClient.post('/api/media/copy', {
                    media_id: target,
                    target_folder: targetFolder || '',
                });

                this.showSuccess(`Media copied to ${targetFolder || 'root'} successfully`);
                
                if (this.options.onCopy) {
                    this.options.onCopy(target, targetFolder);
                }
            }

            // Clear saved targets
            this.savedTarget = null;
            this.savedTargetType = null;
            
        } catch (error) {
            this.showError(`Failed to copy media: ${error.response?.data?.message || error.message}`);
        }
    }

    /**
     * Move media/folder to folder
     */
    async moveToFolder(targetFolder) {
        // Use saved target if current target is null (after modal)
        const target = this.target || this.savedTarget;
        const targetType = this.targetType || this.savedTargetType;
        
        if (!target) {
            console.error('No target found for move operation');
            return;
        }

        try {
            if (targetType === 'media') {
                // Move media file
                await axiosClient.post('/api/media/move', {
                    media_id: target,
                    target_folder: targetFolder || '',
                });

                this.showSuccess(`Media moved to ${targetFolder || 'root'} successfully`);
            } else if (targetType === 'folder') {
                // Move folder
                await axiosClient.post('/api/media/folders/move', {
                    source: target,
                    target: targetFolder || '',
                });

                this.showSuccess(`Folder moved to ${targetFolder || 'root'} successfully`);
            }

            if (this.options.onMove) {
                this.options.onMove(target, targetFolder, targetType);
            }

            // Clear saved targets
            this.savedTarget = null;
            this.savedTargetType = null;
            
        } catch (error) {
            this.showError(`Failed to move ${targetType}: ${error.response?.data?.message || error.message}`);
        }
    }

    /**
     * Show rename folder modal
     */
    showRenameFolderModal() {
        if (!this.target) return;

        const currentName = this.target.split('/').pop();
        const newFolderNameInput = document.getElementById('newFolderName');
        if (newFolderNameInput) {
            newFolderNameInput.value = currentName;
        }

        // Store target in modal for later use
        const renameFolderModal = document.getElementById('renameFolderModal');
        if (renameFolderModal) {
            renameFolderModal.dataset.target = this.target;
        }

        // Hide context menu but don't reset target yet
        const contextMenu = document.getElementById(this.options.menuId);
        if (contextMenu) {
            contextMenu.style.display = 'none';
        }

        // Show modal
        if (renameFolderModal) {
            new bootstrap.Modal(renameFolderModal).show();
        }
    }

    /**
     * Rename folder
     */
    async renameFolder() {
        const newFolderNameInput = document.getElementById('newFolderName');
        const renameFolderModal = document.getElementById('renameFolderModal');
        
        const newName = newFolderNameInput?.value.trim();
        const target = renameFolderModal?.dataset.target;

        if (!newName || !target) {
            this.showError('Missing folder name or target');
            return;
        }

        try {
            await axiosClient.post('/api/media/folders/rename', {
                oldPath: target,
                newName: newName,
            });
            
            this.showSuccess('Folder renamed successfully');
            
            // Hide modal
            const modalInstance = bootstrap.Modal.getInstance(renameFolderModal);
            if (modalInstance) {
                modalInstance.hide();
            }
            
            if (this.options.onRename) {
                this.options.onRename(target, newName);
            }
            
            this.target = null;
            this.targetType = null;
        } catch (error) {
            this.showError(error.response?.data?.message || 'Failed to rename folder');
            this.target = null;
            this.targetType = null;
        }
    }

    /**
     * Show success message
     */
    showSuccess(message) {
        if (typeof window.showToast === 'function') {
            window.showToast(message, 'success');
        } else {
            console.log(message);
        }
    }

    /**
     * Show error message
     */
    showError(message) {
        if (typeof window.showToast === 'function') {
            window.showToast(message, 'error');
        } else {
            console.error(message);
            alert(message);
        }
    }

    /**
     * Get current target
     */
    getTarget() {
        return {
            target: this.target,
            type: this.targetType
        };
    }

    /**
     * Check if context menu is visible
     */
    isContextMenuVisible() {
        return this.isVisible;
    }

    /**
     * Delete folder
     */
    async deleteFolder() {
        if (!this.target || this.targetType !== 'folder') return;

        const folderName = this.target.split('/').pop();
        if (!confirm(`Are you sure you want to delete folder "${folderName}" and all its contents?`)) {
            return;
        }

        try {
            await axiosClient.delete('/api/media/folders', {
                data: { folder_path: this.target }
            });

            this.showSuccess('Folder deleted successfully');
            
            if (this.options.onDelete) {
                this.options.onDelete(this.target);
            }

            this.hide();
        } catch (error) {
            this.showError('Failed to delete folder: ' + (error.response?.data?.message || error.message));
        }
    }

    /**
     * Show folder selection modal
     */
    async showFolderModal(action) {
        this.currentAction = action;
        this.savedTarget = this.target;
        this.savedTargetType = this.targetType;
        

        
        // Set modal title
        const modalTitle = document.getElementById('folderModalTitle');
        if (modalTitle) {
            const titles = {
                copy: 'Copy to Folder',
                move: 'Move to Folder', 
                moveFolder: 'Move Folder to'
            };
            modalTitle.textContent = titles[action] || 'Select Folder';
        }

        // Load and populate folders
        await this.populateFolderModal();
        
        // Hide context menu
        this.hide();
        
        // Show modal
        const modal = document.getElementById('folderSelectionModal');
        if (modal) {
            new bootstrap.Modal(modal).show();
        }
    }

    /**
     * Populate folder modal with searchable list
     */
    async populateFolderModal() {
        try {
            const response = await axiosClient.get('/api/media/folders');
            const folders = response.data.data || [];
            
            const folderList = document.getElementById('folderList');
            if (!folderList) return;

            if (folders.length === 0) {
                folderList.innerHTML = '<div class="text-center text-muted p-3">No folders available</div>';
                return;
            }

            // Add root folder option
            let folderHtml = `
                <div class="folder-item d-flex align-items-center p-2 border-bottom" data-folder="" style="cursor: pointer; background-color: #f8f9fa;">
                    <i class="fas fa-home text-primary me-2"></i>
                    <div class="flex-grow-1">
                        <div class="fw-bold">📁 Root Folder</div>
                        <small class="text-muted">Move to main directory</small>
                    </div>
                </div>
            `;

            // Add other folders
            folderHtml += folders.map(folder => `
                <div class="folder-item d-flex align-items-center p-2 border-bottom" data-folder="${folder.path}" style="cursor: pointer;">
                    <i class="fas fa-folder text-warning me-2"></i>
                    <div class="flex-grow-1">
                        <div class="fw-bold">${folder.name}</div>
                        <small class="text-muted">${folder.path}</small>
                    </div>
                    <small class="text-muted">${folder.count || 0} files</small>
                </div>
            `).join('');

            folderList.innerHTML = folderHtml;

            // Add click events
            folderList.querySelectorAll('.folder-item').forEach(item => {
                item.addEventListener('click', () => {
                    const folderPath = item.dataset.folder;
                    this.handleFolderSelection(folderPath);
                });
            });

            // Setup search
            this.setupFolderSearch(folders);
            
        } catch (error) {
            console.error('Failed to load folders for modal:', error);
        }
    }

    /**
     * Setup folder search functionality
     */
    setupFolderSearch(folders) {
        const searchInput = document.getElementById('folderSearch');
        if (!searchInput) return;

        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            const folderList = document.getElementById('folderList');
            
            if (!query) {
                // Show all folders
                folderList.querySelectorAll('.folder-item').forEach(item => {
                    item.style.display = 'flex';
                });
                return;
            }

            // Filter folders
            folderList.querySelectorAll('.folder-item').forEach(item => {
                const folderPath = item.dataset.folder.toLowerCase();
                const folderName = item.querySelector('.fw-bold').textContent.toLowerCase();
                
                if (folderName.includes(query) || folderPath.includes(query)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    /**
     * Handle folder selection from modal
     */
    handleFolderSelection(folderPath) {
        const modal = document.getElementById('folderSelectionModal');
        const modalInstance = bootstrap.Modal.getInstance(modal);
        
        if (modalInstance) {
            modalInstance.hide();
        }

        // Execute action based on current action type
        switch (this.currentAction) {
            case 'copy':
                this.copyToFolder(folderPath);
                break;
            case 'move':
                this.moveToFolder(folderPath);
                break;
            case 'moveFolder':
                this.moveToFolder(folderPath);
                break;
        }
    }

    /**
     * Destroy context menu and cleanup
     */
    destroy() {
        this.hide();
        // Remove event listeners if needed
    }
}

// Auto-initialize if context menu exists
document.addEventListener('DOMContentLoaded', () => {
    const contextMenu = document.getElementById('contextMenu');
    if (contextMenu && !window.contextMenu) {
        window.contextMenu = new ContextMenu();
    }
});

export { ContextMenu };