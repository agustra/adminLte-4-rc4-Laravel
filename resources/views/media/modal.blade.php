<!-- Media Library Modal - Exact same components as main page -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-images me-2"></i>{{ $title }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Use exact same container as main media page -->
                @include('media.partials.container', [
                    'title' => false,
                    'hideViewToggle' => false,
                    'hideUpload' => false,
                    'hideNavigation' => false,
                    'hideToolbar' => false,
                ])
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="selectMediaForSettings" disabled>
                    <i class="fas fa-check me-1"></i>Select Media
                </button>
            </div>
        </div>
    </div>
    @include('media.partials.context-menu')
    @include('media.partials.modals')
    @include('media.partials.picker-modal')
</div>
