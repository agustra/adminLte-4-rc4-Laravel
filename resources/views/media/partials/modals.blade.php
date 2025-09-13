<!-- Media Detail Modal -->
<div class="modal fade" id="mediaDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Media Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div id="mediaPreview" class="text-center mb-3"></div>
                        <div id="imageEditor" style="display: none;">
                            <img id="cropperImage" style="max-width: 100%;">
                            <div class="mt-2">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary" id="cropFree" title="Free Crop">
                                        <i class="fas fa-crop"></i> Free
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="cropSquare" title="Square Crop">
                                        <i class="fas fa-square"></i> 1:1
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="cropCircle" title="Circle Crop">
                                        <i class="fas fa-circle"></i> Circle
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="crop16x9" title="16:9 Crop">
                                        <i class="fas fa-rectangle-landscape"></i> 16:9
                                    </button>
                                </div>
                                <div class="btn-group btn-group-sm ms-2" role="group">
                                    <button type="button" class="btn btn-outline-secondary" id="rotateLeft" title="Rotate Left">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="rotateRight" title="Rotate Right">
                                        <i class="fas fa-redo"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="flipH" title="Flip Horizontal">
                                        <i class="fas fa-arrows-alt-h"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="flipV" title="Flip Vertical">
                                        <i class="fas fa-arrows-alt-v"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <form id="mediaEditForm">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" class="form-control" id="mediaTitle">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alt Text</label>
                                <input type="text" class="form-control" id="mediaAlt">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="mediaDescription" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Collection</label>
                                <select class="form-select" id="mediaCollection">
                                    <option value="default">Default</option>
                                    <option value="avatar">Avatar</option>
                                    <option value="documents">Documents</option>
                                    <option value="images">Images</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">
                                    <strong>File:</strong> <span id="mediaFileName"></span><br>
                                    <strong>Size:</strong> <span id="mediaFileSize"></span><br>
                                    <strong>Type:</strong> <span id="mediaFileType"></span><br>
                                    <strong>Uploaded:</strong> <span id="mediaUploadDate"></span>
                                </small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" id="editImage" style="display: none;">Edit Image</button>
                <button type="button" class="btn btn-success" id="applyEdit" style="display: none;">Apply Changes</button>
                <button type="button" class="btn btn-outline-secondary" id="cancelEdit" style="display: none;">Cancel Edit</button>
                <button type="button" class="btn btn-danger" id="deleteMedia">Delete</button>
                <button type="button" class="btn btn-primary" id="saveMedia">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Folder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createFolderForm">
                    <div class="mb-3">
                        <label for="folderName" class="form-label">Folder Name</label>
                        <input type="text" class="form-control" id="folderName" required>
                        <div class="form-text">Enter a name for the new folder</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="createFolderBtn">Create Folder</button>
            </div>
        </div>
    </div>
</div>

<!-- Rename Folder Modal -->
<div class="modal fade" id="renameFolderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rename Folder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="renameFolderForm">
                    <div class="mb-3">
                        <label for="newFolderName" class="form-label">New Folder Name</label>
                        <input type="text" class="form-control" id="newFolderName" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="renameFolderBtn">Rename</button>
            </div>
        </div>
    </div>
</div>