<!-- Media Toolbar -->
<div class="media-toolbar">
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-success btn-sm" id="createFolder">
            <i class="fas fa-folder-plus"></i> New Folder
        </button>
        <select class="form-select form-select-sm" id="collectionFilter" style="width: auto;">
            <option value="">All Collections</option>
            <option value="avatar">Avatars</option>
            <option value="documents">Documents</option>
            <option value="images">Images</option>
        </select>
        <select class="form-select form-select-sm" id="typeFilter" style="width: auto;">
            <option value="">All Types</option>
            <option value="image">Images</option>
            <option value="video">Videos</option>
            <option value="audio">Audio</option>
            <option value="application">Documents</option>
        </select>
        <div class="position-relative">
            <input type="search" class="form-control form-control-sm" id="searchMedia" placeholder="Search media..." style="width: 200px;">
            <small class="text-muted position-absolute" id="searchScope" style="top: 100%; left: 0; font-size: 10px; display: none;"></small>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-danger btn-sm" id="deleteSelected" style="display: none;">
            <i class="fas fa-trash"></i> Delete Selected
        </button>
        <span class="text-muted" id="mediaCount">0 items</span>
    </div>
</div>