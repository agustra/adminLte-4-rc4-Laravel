// Avatar Component - Menangani fungsi avatar dengan FileManager
import { Delete } from "@components/helpers/delete.js";

window.initializeAvatar = function () {
    const selectBtn = document.getElementById("selectAvatarBtn");
    if (selectBtn) {
        selectBtn.addEventListener("click", function (e) {
            e.preventDefault();
            openFileManagerForAvatar();
        });
    }

    // Initialize delete avatar functionality
    initializeDeleteAvatar();
};

function openFileManagerForAvatar() {
    console.log('🖼️ Opening FileManager for avatar selection');
    
    // Store callback for avatar selection
    window.currentAvatarSelection = true;
    
    // Open FileManager popup for user's private images
    const popup = window.open(
        '/user-filemanager?type=image',
        'FileManager',
        'width=1000,height=700,scrollbars=yes,resizable=yes'
    );
    
    console.log('📂 FileManager popup opened for avatar:', popup);
    
    // Check if popup opened successfully
    if (!popup || popup.closed) {
        console.error('❌ Failed to open FileManager popup');
        if (typeof toastr !== "undefined") {
            toastr.error('Failed to open FileManager. Please check popup blocker.');
        }
        return;
    }
    
    // Listen for file selection from FileManager
    window.SetUrl = function(urls, file_path) {
        console.log('📁 FileManager avatar callback received:', { urls, file_path });
        
        if (!window.currentAvatarSelection) return;
        
        // Handle FileManager object format
        const fileObj = Array.isArray(urls) ? urls[0] : urls;
        const url = fileObj?.url || fileObj;
        console.log('🔗 Avatar URL extracted:', url);
        
        if (url) {
            updateAvatar({ url, name: fileObj?.name || 'avatar' });
            
            // Close popup
            popup.close();
            
            // Clear selection flag
            delete window.currentAvatarSelection;
            
            if (typeof toastr !== "undefined") {
                toastr.success('Avatar berhasil dipilih dari FileManager');
            }
        } else {
            console.error('❌ No URL received from FileManager');
        }
    };
}

function updateAvatar(selectedMedia) {
    console.log('🖼️ Updating avatar with:', selectedMedia);
    
    const avatarInput = document.getElementById("avatarInput");
    const avatarPreview = document.getElementById("avatarPreview");

    if (selectedMedia && selectedMedia.url) {
        if (avatarInput) {
            avatarInput.value = selectedMedia.url;
            console.log('✅ Avatar input updated:', selectedMedia.url);
        }

        if (avatarPreview) {
            avatarPreview.src = selectedMedia.url;
            console.log('✅ Avatar preview updated');
        }
    }
}

// Fungsi untuk preview avatar dari file upload
window.previewImage = function () {
    const image = document.querySelector("#avatar");
    const imgPreview = document.querySelector(".img-preview");

    if (image && image.files && image.files[0]) {
        imgPreview.style.display = "block";

        const oFReader = new FileReader();
        oFReader.readAsDataURL(image.files[0]);

        oFReader.onload = function (oFREvent) {
            imgPreview.src = oFREvent.target.result;
        };
    }
};

// Initialize delete avatar functionality
function initializeDeleteAvatar() {
    Delete({
        buttonSelector: ".btn-delete-avatar",
        deleteUrl: "/api/media/avatar/",
        confirmTitle: "Hapus Avatar?",
        confirmText: "Avatar akan dihapus dan diganti dengan avatar default.",
        confirmButtonText: "Ya, Hapus Avatar!",
        onDeleteSuccess: function (userId, response) {
            const avatarPreview = document.getElementById("avatarPreview");
            if (avatarPreview) {
                avatarPreview.src = "/storage/filemanager/images/public/avatar-default.webp";
            }

            const avatarInput = document.getElementById("avatarInput");
            if (avatarInput) {
                avatarInput.value = "";
            }
        },
    });
}

// Global deleteAvatar function for compatibility
window.deleteAvatar = function (userId) {
    const button = document.createElement("button");
    button.className = "btn-delete-avatar";
    button.setAttribute("data-id", userId);
    button.click();
};

// Auto-initialize avatar when modal is shown
document.addEventListener("shown.bs.modal", function (e) {
    const modal = e.target;
    if (modal.id === "modalAction") {
        initializeAvatar();
    }
});
