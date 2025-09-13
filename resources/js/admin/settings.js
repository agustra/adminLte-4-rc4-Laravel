import { MediaLibrary } from "../media/MediaLibrary.js";
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
    initializeMediaPicker();
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
window.openMediaPicker = function (fieldName) {
    window.currentMediaField = fieldName;
    openMediaLibrary({
        inputId: fieldName,
        previewId: fieldName + "_preview",
    });
};

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

// Initialize media picker
function initializeMediaPicker() {
    // Media picker is already initialized globally above
    console.log("📷 Media picker ready");
}

async function openMediaLibrary(config) {
    try {
        const response = await axiosClient.get("/media-library/modal", {
            headers: {
                Accept: "text/html",
            },
        });

        const modalContainer = document.createElement("div");
        modalContainer.innerHTML = response.data;
        document.body.appendChild(modalContainer);

        const modal = modalContainer.querySelector(".modal");
        if (modal) {
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();

            // Wait for modal to be fully shown
            modal.addEventListener("shown.bs.modal", function () {
                if (typeof MediaLibrary !== "undefined") {
                    new MediaLibrary();
                }
                setupMediaSelection(modal, config, modalInstance);
            });

            modal.addEventListener("hidden.bs.modal", function () {
                document.body.removeChild(modalContainer);
            });
        }
    } catch (error) {
        console.error("Error loading media modal:", error);
        showToast("Failed to load media library", "error");
    }
}

function setupMediaSelection(modal, config, modalInstance) {
    const selectBtn = modal.querySelector("#selectMediaForSettings");
    let selectedMedia = null;

    function attachMediaItemListeners() {
        const mediaItems = modal.querySelectorAll(
            ".media-item:not(.folder-item)"
        );

        mediaItems.forEach(function (item) {
            if (!item.hasAttribute("data-settings-listener")) {
                item.setAttribute("data-settings-listener", "true");
                item.addEventListener("click", function (e) {
                    if (
                        e.target.closest("button") ||
                        e.target.type === "checkbox"
                    )
                        return;

                    mediaItems.forEach(function (i) {
                        i.classList.remove("selected");
                    });
                    item.classList.add("selected");

                    selectedMedia = {
                        id: item.dataset.id,
                        url: item.dataset.url || item.querySelector("img")?.src,
                        name:
                            item.dataset.name || item.querySelector("img")?.alt,
                    };

                    if (selectBtn) selectBtn.disabled = false;
                });
            }
        });
    }

    // Attach listeners to existing items
    setTimeout(attachMediaItemListeners, 1000);

    // Observer for new items
    const observer = new MutationObserver(attachMediaItemListeners);
    const mediaGrid = modal.querySelector("#mediaGrid");
    if (mediaGrid) {
        observer.observe(mediaGrid, { childList: true, subtree: true });
    }

    if (selectBtn) {
        selectBtn.addEventListener("click", function () {
            if (selectedMedia) {
                handleMediaSelection(selectedMedia, config);
                modalInstance.hide();
            }
        });
    }
}

function handleMediaSelection(selectedMedia, config) {
    const input = document.getElementById(config.inputId);
    const previewContainer = document.getElementById(config.previewId);

    if (selectedMedia && selectedMedia.url) {
        // Extract filename from URL
        let cleanFilename = selectedMedia.name;
        if (selectedMedia.url) {
            const urlParts = selectedMedia.url.split("/media/");
            if (urlParts.length > 1) {
                cleanFilename = urlParts[1];
            }
        }

        // Update input field
        if (input) {
            input.value = cleanFilename;
        }

        // Update preview
        if (previewContainer) {
            previewContainer.innerHTML = `<img src="${selectedMedia.url}" alt="Preview" class="img-thumbnail" style="max-height: 100px;">`;
        }

        showToast("Media selected successfully", "success");
    }
}

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
    console.log("Settings: DOM ready, initializing...");
    initializeSettings();
    console.log("Settings: Initialization complete");
});

// Export functions
export { initializeSettings, openMediaLibrary };

// Export default function untuk dynamic loading
export default function initSettingsModule() {
    console.log("⚙️ Settings module initialized successfully!");
}
