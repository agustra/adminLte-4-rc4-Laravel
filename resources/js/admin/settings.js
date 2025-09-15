import axiosClient from "@api/axiosClient.js";

console.log("⚙️ Settings module loading...");

// Global variables
const spinner = document.getElementById("spinner");

function showToast(message, type) {
    if (typeof window.showToast === "function") {
        window.showToast(message, type);
    } else if (typeof toastr !== "undefined") {
        toastr[type](message);
    } else {
        console.log(`${type.toUpperCase()}: ${message}`);
        alert(message);
    }
}

// Attach form event immediately when file loads
setTimeout(() => {
    const form = document.getElementById("settingsForm");
    if (form) {
        form.onsubmit = window.submitForm;
    }
}, 100);

// Initialize settings page
function initializeSettings() {
    initializeSettingsForm();
    initializeFileManagerPicker();
}

// Initialize settings form
function initializeSettingsForm() {
    const form = document.getElementById("settingsForm");
    if (!form) return;

    // Remove any existing onsubmit attribute
    form.removeAttribute("onsubmit");

    // Attach event handler
    form.onsubmit = window.submitForm;
}

// Global functions (available immediately)
window.openFileManager = function (fieldName) {
    console.log('🔍 Opening FileManager for field:', fieldName);
    window.currentInputId = fieldName;
    
    // Open FileManager popup for system images (public folder)
    const popup = window.open(
        '/admin/system-filemanager?type=image',
        'FileManager',
        'width=1000,height=700,scrollbars=yes,resizable=yes'
    );
    
    console.log('📂 FileManager popup opened:', popup);
    
    // Check if popup opened successfully
    if (!popup || popup.closed) {
        console.error('❌ Failed to open FileManager popup');
        showToast('Failed to open FileManager. Please check popup blocker.', 'error');
        return;
    }
    
    // Listen for file selection from FileManager
    window.SetUrl = function(urls, file_path) {
        console.log('📁 FileManager callback received:', { urls, file_path });
        
        // Handle FileManager object format
        const fileObj = Array.isArray(urls) ? urls[0] : urls;
        const url = fileObj?.url || fileObj;
        console.log('🔗 File object:', fileObj);
        console.log('🔗 Extracted URL:', url);
        
        const inputElement = document.getElementById(window.currentInputId);
        console.log('🎯 Target input element:', inputElement);
        
        if (inputElement && url) {
            // Extract file path from URL
            let finalPath = file_path;
            if (!finalPath && url) {
                // Extract path from URL: /storage/filemanager/images/public/logo.png
                const urlParts = url.split('/storage/');
                if (urlParts.length > 1) {
                    finalPath = urlParts[1];
                }
            }
            
            console.log('📂 Final path to save:', finalPath);
            
            // Set the file path
            inputElement.value = finalPath || url;
            console.log('✅ Input value set to:', inputElement.value);
            
            // Update preview with full URL
            updateLogoPreview(window.currentInputId, url);
            
            // Close popup
            popup.close();
            
            // Clear stored input ID
            delete window.currentInputId;
            
            showToast('Logo selected successfully', 'success');
        } else {
            console.error('❌ Missing data:', { inputElement, url, fileObj });
        }
    };
};

function updateLogoPreview(inputId, url) {
    console.log('🖼️ Updating preview for:', inputId, 'with URL:', url);
    
    const previewDiv = document.getElementById(inputId + '_preview');
    console.log('🎯 Preview div found:', previewDiv);
    
    if (previewDiv) {
        if (url) {
            previewDiv.innerHTML = `
                <img src="${url}" alt="Logo Preview" class="img-thumbnail" style="max-height: 100px;">
                <div class="mt-1">
                    <small class="text-muted">Selected: ${url.split('/').pop()}</small>
                </div>
            `;
            console.log('✅ Preview updated with image');
        } else {
            previewDiv.innerHTML = '<small class="text-muted">No logo selected</small>';
            console.log('⚠️ Preview cleared - no URL provided');
        }
    } else {
        console.error('❌ Preview div not found:', inputId + '_preview');
    }
}

window.submitForm = async function (event) {
    event.preventDefault();
    event.stopPropagation();

    const form = document.getElementById("settingsForm");
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML =
        '<i class="bi bi-arrow-repeat" style="animation: spin 1s linear infinite;"></i> Saving...';

    const formData = new FormData(form);

    try {
        const response = await axiosClient.post(form.action, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        const result = response.data;
        console.log("Settings response:", result);

        if (result.message) {
            showToast(result.message, "success");

            // Show success state briefly then reload
            submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Saved!';
            submitBtn.classList.add("btn-success");
            submitBtn.classList.remove("btn-primary");

            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            throw new Error("No message in response");
        }
    } catch (error) {
        console.error("Error:", error);
        showToast("Error saving settings", "error");

        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
};

// Initialize FileManager picker
function initializeFileManagerPicker() {
    // FileManager picker is already initialized globally above
    console.log("📁 FileManager picker ready");
}

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
    console.log("Settings: DOM ready, initializing...");
    initializeSettings();
    console.log("Settings: Initialization complete");
});

// Export functions
export { initializeSettings };

// Export default function untuk dynamic loading
export default function initSettingsModule() {
    console.log("⚙️ Settings module initialized successfully!");
}
