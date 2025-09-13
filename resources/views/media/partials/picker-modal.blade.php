<!-- Media Picker Modal for CKEditor/Other Components -->
<div class="modal fade" id="{{ $modalId ?? 'mediaPickerModal' }}" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-images me-2"></i>{{ $title ?? 'Select Media' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="height: 70vh;">
                @include('media.partials.container', [
                    'title' => false,
                    'hideViewToggle' => true,
                    'hideUpload' => $hideUpload ?? false,
                    'hideNavigation' => $hideNavigation ?? false,
                    'hideToolbar' => $hideToolbar ?? false
                ])
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                @if(!isset($hideSelectButton) || !$hideSelectButton)
                <button type="button" class="btn btn-primary" id="{{ $selectButtonId ?? 'selectMediaBtn' }}" disabled>
                    <i class="fas fa-check me-1"></i>{{ $selectButtonText ?? 'Select Media' }}
                </button>
                @endif
            </div>
        </div>
    </div>
</div>