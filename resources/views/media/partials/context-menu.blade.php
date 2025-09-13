<!-- Context Menu -->
<div id="contextMenu" class="dropdown-menu" style="display: none; position: absolute; z-index: 1000; max-width: 300px; max-height: 400px;">
    <div id="mediaContextMenu">
        <a class="dropdown-item" href="#" id="copyToRoot">
            <i class="fas fa-copy"></i> Copy to Root
        </a>
        <a class="dropdown-item" href="#" id="copyToFolder">
            <i class="fas fa-folder"></i> Copy to Folder...
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="#" id="moveToRoot">
            <i class="fas fa-arrows-alt"></i> Move to Root
        </a>
        <a class="dropdown-item" href="#" id="moveToFolder">
            <i class="fas fa-folder-open"></i> Move to Folder...
        </a>
    </div>
    <div id="folderContextMenu" style="display: none;">
        <a class="dropdown-item" href="#" id="renameFolder">
            <i class="fas fa-edit"></i> Rename Folder
        </a>
        <a class="dropdown-item" href="#" id="moveFolderToRoot">
            <i class="fas fa-home"></i> Move to Root
        </a>
        <a class="dropdown-item" href="#" id="moveFolderTo">
            <i class="fas fa-folder-open"></i> Move to Folder...
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item text-danger" href="#" id="deleteFolder">
            <i class="fas fa-trash"></i> Delete Folder
        </a>
    </div>
</div>

<!-- Folder Selection Modal -->
<div class="modal fade" id="folderSelectionModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="folderModalTitle">Select Folder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="search" class="form-control form-control-sm" id="folderSearch" placeholder="Search folders...">
                </div>
                <div id="folderList" style="max-height: 300px; overflow-y: auto;">
                    <!-- Folders will be populated here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>