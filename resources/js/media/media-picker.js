import { showModal } from "@handlers/modalHandler.js";

/**
 * Media Picker Utility
 * Untuk membuka media library dalam mode picker
 */
class MediaPicker {
    constructor(options = {}) {
        this.options = {
            multiple: false,
            allowedTypes: [], // ['image', 'video', 'audio', 'document']
            onSelect: null,
            modalId: 'mediaPickerModal',
            ...options
        };
        
        this.selectedMedia = [];
        this.init();
    }

    init() {
        // Listen for media selection events
        window.addEventListener('message', (event) => {
            if (event.data.type === 'MEDIA_SELECTED') {
                this.handleMediaSelection(event.data.media);
            }
        });
    }

    /**
     * Open media picker modal
     */
    open() {
        const modalUrl = `/media-library?mode=picker&multiple=${this.options.multiple}&types=${this.options.allowedTypes.join(',')}`; 
        showModal(modalUrl, 'picker');
    }

    /**
     * Handle media selection from iframe
     */
    handleMediaSelection(media) {
        if (this.options.multiple) {
            this.selectedMedia.push(media);
        } else {
            this.selectedMedia = [media];
        }

        if (this.options.onSelect && typeof this.options.onSelect === 'function') {
            this.options.onSelect(this.options.multiple ? this.selectedMedia : media);
        }

        // Close modal
        const modal = document.getElementById(this.options.modalId);
        if (modal) {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
            }
        }
    }

    /**
     * Clear selected media
     */
    clear() {
        this.selectedMedia = [];
    }

    /**
     * Get selected media
     */
    getSelected() {
        return this.options.multiple ? this.selectedMedia : this.selectedMedia[0] || null;
    }
}

/**
 * Quick function to open media picker
 */
function openMediaPicker(options = {}) {
    return new Promise((resolve) => {
        const picker = new MediaPicker({
            ...options,
            onSelect: (media) => {
                resolve(media);
            }
        });
        picker.open();
    });
}

/**
 * Initialize media picker buttons
 */
function initializeMediaPickerButtons() {
    document.querySelectorAll('[data-media-picker]').forEach(button => {
        if (button.dataset.bound) return; // Prevent double binding
        
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            
            const options = {
                multiple: button.dataset.multiple === 'true',
                allowedTypes: button.dataset.types ? button.dataset.types.split(',') : [],
            };
            
            try {
                const selectedMedia = await openMediaPicker(options);
                
                // Trigger custom event
                const event = new CustomEvent('mediaSelected', {
                    detail: { media: selectedMedia, button }
                });
                button.dispatchEvent(event);
                
                // If target input specified, update it
                const targetInput = button.dataset.target;
                if (targetInput) {
                    const input = document.querySelector(targetInput);
                    if (input) {
                        if (options.multiple) {
                            input.value = selectedMedia.map(m => m.url).join(',');
                        } else {
                            input.value = selectedMedia.url;
                        }
                        
                        // Trigger change event
                        input.dispatchEvent(new Event('change'));
                    }
                }
                
                // If preview element specified, update it
                const previewElement = button.dataset.preview;
                if (previewElement && !options.multiple) {
                    const preview = document.querySelector(previewElement);
                    if (preview) {
                        if (selectedMedia.mime_type && selectedMedia.mime_type.startsWith('image/')) {
                            preview.innerHTML = `<img src="${selectedMedia.url}" alt="${selectedMedia.name}" class="img-fluid" style="max-height: 200px;">`;
                        } else {
                            preview.innerHTML = `<p>Selected: ${selectedMedia.name}</p>`;
                        }
                    }
                }
                
            } catch (error) {
                console.error('Media picker error:', error);
            }
        });
        
        button.dataset.bound = 'true';
    });
}

// Auto-initialize on DOM ready
document.addEventListener('DOMContentLoaded', initializeMediaPickerButtons);

// Re-initialize when new content is loaded (for dynamic content)
document.addEventListener('contentLoaded', initializeMediaPickerButtons);

export { MediaPicker, openMediaPicker, initializeMediaPickerButtons };