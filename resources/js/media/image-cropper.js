import axiosClient from "@api/axiosClient.js";

/**
 * Image Cropper Component
 * Handles image editing with Cropper.js integration
 */
class ImageCropper {
    constructor(options = {}) {
        this.options = {
            containerId: 'imageEditor',
            imageId: 'cropperImage',
            saveUrl: '/api/media/upload/file',
            aspectRatio: NaN, // Free crop by default
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 1,
            restore: false,
            guides: true,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
            onSave: null,
            onCancel: null,
            ...options
        };
        
        this.cropper = null;
        this.isEditing = false;
        this.currentItem = null;
        
        this.init();
    }

    init() {
        this.loadCropperJS();
        this.bindEvents();
    }

    /**
     * Load Cropper.js dynamically if not already loaded
     */
    loadCropperJS() {
        if (window.Cropper) return Promise.resolve();
        
        return new Promise((resolve, reject) => {
            const script = document.createElement("script");
            script.src = "https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js";
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    /**
     * Bind event listeners
     */
    bindEvents() {
        // Cropper control buttons
        const controls = {
            cropFree: () => this.setCropMode('free'),
            cropSquare: () => this.setCropMode(1),
            cropCircle: () => this.setCropMode('circle'),
            crop16x9: () => this.setCropMode(16/9),
            rotateLeft: () => this.rotate(-90),
            rotateRight: () => this.rotate(90),
            flipH: () => this.scaleX(-1),
            flipV: () => this.scaleY(-1),
            applyEdit: () => this.save(),
            cancelEdit: () => this.cancel()
        };

        Object.entries(controls).forEach(([id, handler]) => {
            const element = document.getElementById(id);
            if (element && !element.dataset.bound) {
                element.addEventListener('click', handler);
                element.dataset.bound = 'true';
            }
        });
    }

    /**
     * Start editing an image
     */
    async startEdit(imageUrl, item = null) {
        await this.loadCropperJS();
        
        this.currentItem = item;
        this.isEditing = true;

        // Show editor container
        const container = document.getElementById(this.options.containerId);
        if (container) {
            container.style.display = 'block';
        }

        // Set image source
        const image = document.getElementById(this.options.imageId);
        if (image) {
            image.src = imageUrl;
            
            // Initialize cropper after image loads
            image.onload = () => {
                this.initializeCropper(image);
            };
        }

        // Update UI buttons
        this.updateButtons(true);
    }

    /**
     * Initialize Cropper.js
     */
    initializeCropper(image) {
        if (this.cropper) {
            this.cropper.destroy();
        }

        this.cropper = new Cropper(image, {
            aspectRatio: this.options.aspectRatio,
            viewMode: this.options.viewMode,
            dragMode: this.options.dragMode,
            autoCropArea: this.options.autoCropArea,
            restore: this.options.restore,
            guides: this.options.guides,
            center: this.options.center,
            highlight: this.options.highlight,
            cropBoxMovable: this.options.cropBoxMovable,
            cropBoxResizable: this.options.cropBoxResizable,
            toggleDragModeOnDblclick: this.options.toggleDragModeOnDblclick,
        });
    }

    /**
     * Set crop aspect ratio mode
     */
    setCropMode(aspectRatio) {
        if (!this.cropper) return;

        if (aspectRatio === 'free') {
            this.cropper.setAspectRatio(NaN);
            this.removeCropBoxStyling();
        } else if (aspectRatio === 'circle') {
            this.cropper.setAspectRatio(1);
            this.addCircleCropStyling();
        } else {
            this.cropper.setAspectRatio(aspectRatio);
            this.removeCropBoxStyling();
        }
    }

    /**
     * Add circle crop styling
     */
    addCircleCropStyling() {
        const cropBox = document.querySelector('.cropper-crop-box');
        if (cropBox) {
            cropBox.style.borderRadius = '50%';
        }
    }

    /**
     * Remove crop box styling
     */
    removeCropBoxStyling() {
        const cropBox = document.querySelector('.cropper-crop-box');
        if (cropBox) {
            cropBox.style.borderRadius = '0';
        }
    }

    /**
     * Rotate image
     */
    rotate(degrees) {
        if (this.cropper) {
            this.cropper.rotate(degrees);
        }
    }

    /**
     * Scale image horizontally
     */
    scaleX(scaleX) {
        if (this.cropper) {
            this.cropper.scaleX(scaleX);
        }
    }

    /**
     * Scale image vertically
     */
    scaleY(scaleY) {
        if (this.cropper) {
            this.cropper.scaleY(scaleY);
        }
    }

    /**
     * Save cropped image
     */
    async save() {
        if (!this.cropper) return;

        try {
            // Get cropped canvas
            const canvas = this.cropper.getCroppedCanvas({
                width: 800,
                height: 600,
                minWidth: 256,
                minHeight: 256,
                maxWidth: 4096,
                maxHeight: 4096,
                fillColor: '#fff',
                imageSmoothingEnabled: false,
                imageSmoothingQuality: 'high',
            });

            // Convert to blob
            const blob = await new Promise(resolve => {
                canvas.toBlob(resolve, 'image/webp', 0.85);
            });

            // Prepare form data
            const formData = new FormData();
            const fileName = this.currentItem?.file_name || 'edited_image.webp';
            formData.append('file', blob, fileName);
            
            if (this.currentItem?.collection) {
                formData.append('collection', this.currentItem.collection);
            }

            // Upload edited image
            const response = await axiosClient.post(this.options.saveUrl, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });

            // Update preview with new image
            this.updatePreviewAfterSave(response.data);

            // Success callback
            if (this.options.onSave) {
                this.options.onSave(response.data, this.currentItem);
            }

            this.showSuccess('Image updated successfully');
            this.cancel();

        } catch (error) {
            console.error('Save error:', error);
            this.showError('Failed to save image: ' + (error.response?.data?.message || error.message));
        }
    }

    /**
     * Cancel editing
     */
    cancel() {
        this.isEditing = false;

        // Destroy cropper
        if (this.cropper) {
            this.cropper.destroy();
            this.cropper = null;
        }

        // Hide editor container
        const container = document.getElementById(this.options.containerId);
        if (container) {
            container.style.display = 'none';
        }

        // Update UI buttons
        this.updateButtons(false);

        // Cancel callback
        if (this.options.onCancel) {
            this.options.onCancel(this.currentItem);
        }

        this.currentItem = null;
    }

    /**
     * Update preview after successful save
     */
    updatePreviewAfterSave(responseData) {
        // Update modal preview if it exists
        const preview = document.getElementById('mediaPreview');
        if (preview && responseData.url) {
            // Add cache busting parameter to force reload
            const newUrl = responseData.url + '?t=' + Date.now();
            const img = preview.querySelector('img');
            if (img) {
                img.src = newUrl;
            } else {
                preview.innerHTML = `<img src="${newUrl}" alt="${responseData.name || 'Updated image'}" style="max-width: 100%; max-height: 300px; width: auto; height: auto; object-fit: contain; border-radius: 4px;">`;
            }
        }
        
        // Show preview container
        if (preview) {
            preview.style.display = 'block';
        }
    }

    /**
     * Update button visibility
     */
    updateButtons(editing) {
        const buttons = {
            editImage: !editing,
            applyEdit: editing,
            cancelEdit: editing,
            saveMedia: !editing
        };

        Object.entries(buttons).forEach(([id, show]) => {
            const element = document.getElementById(id);
            if (element) {
                element.style.display = show ? 'inline-block' : 'none';
            }
        });
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
     * Check if currently editing
     */
    isCurrentlyEditing() {
        return this.isEditing;
    }

    /**
     * Get current cropper instance
     */
    getCropper() {
        return this.cropper;
    }

    /**
     * Destroy cropper and cleanup
     */
    destroy() {
        if (this.cropper) {
            this.cropper.destroy();
            this.cropper = null;
        }
        this.isEditing = false;
        this.currentItem = null;
    }
}

// Auto-initialize if image editor exists
document.addEventListener('DOMContentLoaded', () => {
    const imageEditor = document.getElementById('imageEditor');
    if (imageEditor && !window.imageCropper) {
        window.imageCropper = new ImageCropper();
    }
});

export { ImageCropper };