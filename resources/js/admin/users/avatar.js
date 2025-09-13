// Avatar Component - Menangani fungsi avatar
import { MediaLibrary } from "../../media/MediaLibrary.js";
import { Delete } from "@components/helpers/delete.js";
window.initializeAvatar = function () {
    const selectBtn = document.getElementById("selectAvatarBtn");
    if (selectBtn) {
        selectBtn.addEventListener("click", function (e) {
            e.preventDefault();
            openAvatarModal();
        });
    }

    // Initialize delete avatar functionality
    initializeDeleteAvatar();
};

async function openAvatarModal() {
    try {
        const response = await fetch("/media-library/modal", {
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                )?.content,
                Accept: "application/json",
            },
        });

        const modalContainer = document.createElement("div");
        modalContainer.innerHTML = await response.text();
        document.body.appendChild(modalContainer);

        const modal = modalContainer.querySelector(".modal");
        if (modal) {
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();

            // Wait for modal to be fully shown before initializing MediaLibrary
            modal.addEventListener("shown.bs.modal", function () {
                if (typeof MediaLibrary !== "undefined") {
                    new MediaLibrary();
                }
                setupAvatarSelection(modal, modalInstance);
            });

            modal.addEventListener("hidden.bs.modal", function () {
                document.body.removeChild(modalContainer);
            });
        }
    } catch (error) {
        console.error("Error loading media modal:", error);
        if (typeof toastr !== "undefined") {
            toastr.error("Gagal memuat media library");
        }
    }
}

function setupAvatarSelection(modal, modalInstance) {
    const selectBtn = modal.querySelector("#selectMediaForSettings");
    let selectedMedia = null;

    function attachMediaItemListeners() {
        const mediaItems = modal.querySelectorAll(
            ".media-item:not(.folder-item)"
        );

        mediaItems.forEach(function (item) {
            if (!item.hasAttribute("data-avatar-listener")) {
                item.setAttribute("data-avatar-listener", "true");
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
                updateAvatar(selectedMedia);
                modalInstance.hide();
            }
        });
    }
}

function updateAvatar(selectedMedia) {
    const avatarInput = document.getElementById("avatarInput");
    const avatarPreview = document.getElementById("avatarPreview");

    if (selectedMedia && selectedMedia.url) {
        if (avatarInput) {
            avatarInput.value = selectedMedia.url;
        }

        if (avatarPreview) {
            avatarPreview.src = selectedMedia.url;
        }

        if (typeof toastr !== "undefined") {
            toastr.success("Avatar berhasil dipilih dari Media Library");
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
                avatarPreview.src = "/avatar/avatar-default.jpg";
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
