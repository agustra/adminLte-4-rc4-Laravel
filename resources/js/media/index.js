// Media Library Main Entry Point
import { MediaLibrary } from "./MediaLibrary.js";
import { MediaUpload } from "./media-upload.js";
import { ImageCropper } from "./image-cropper.js";
import { MediaPicker, openMediaPicker, initializeMediaPickerButtons } from "./media-picker.js";
import { ContextMenu } from "./context-menu.js";
import "./media.js";

// Initialize Media Library when DOM is ready
document.addEventListener("DOMContentLoaded", async function () {
    // Check what elements exist
    const mediaGrid = document.getElementById("mediaGrid");
    const uploadArea = document.getElementById("uploadArea");
    const mediaTable = document.getElementById("table-media");
    
    // Initialize MediaLibrary for grid view
    if (mediaGrid || uploadArea) {
        try {
            window.mediaLibrary = new MediaLibrary();
        } catch (error) {
            console.error('Failed to initialize MediaLibrary:', error);
        }
    }
    
    // Initialize DataTable for table view
    if (mediaTable) {
        console.log('Initializing media DataTable...');
        // The media.js will handle table initialization
    }
    
    // Initialize media picker buttons globally
    initializeMediaPickerButtons();
    
    // Make components globally available
    window.MediaLibrary = MediaLibrary;
    window.MediaUpload = MediaUpload;
    window.ImageCropper = ImageCropper;
    window.MediaPicker = MediaPicker;
    window.ContextMenu = ContextMenu;
    window.openMediaPicker = openMediaPicker;
    
    // The media.js file will handle the DataTable initialization automatically
    // if the table element exists
});

// Export for module usage
export { 
    MediaLibrary, 
    MediaUpload, 
    ImageCropper, 
    MediaPicker, 
    ContextMenu, 
    openMediaPicker 
};