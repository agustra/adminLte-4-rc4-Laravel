<!-- Selected Info -->
<div class="selected-info" id="selectedInfo">
    <strong>Selected:</strong> <span id="selectedCount">0</span> items
    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="clearSelection">Clear Selection</button>
</div>

<!-- Media Grid View -->
<div id="mediaGrid" class="media-grid">
    <div class="text-center py-5">
        <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
        <p class="text-muted mt-2">Loading media...</p>
    </div>
</div>

<!-- Media List View (Hidden by default) -->
<div id="mediaList" style="display: none;">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="30"><input type="checkbox" id="selectAllList"></th>
                    <th>Preview</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Size</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="mediaListBody">
            </tbody>
        </table>
    </div>
</div>