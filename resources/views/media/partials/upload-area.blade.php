<!-- Upload Area -->
<div class="upload-area" id="uploadArea">
    <div class="upload-content">
        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
        <h5>Drop files here or click to upload</h5>
        <p class="text-muted">Supported formats: Images, Videos, Audio, Documents</p>
        <input type="file" id="fileInput" multiple accept="*/*" style="display: none;">
        <button type="button" class="btn btn-primary">
            <i class="fas fa-plus"></i> Select Files
        </button>
    </div>
    <div class="upload-progress" style="display: none;">
        <div class="progress mb-2">
            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
        </div>
        <small class="text-muted">Uploading...</small>
    </div>
</div>