import axiosClient from "../apiService/axiosClient.js";
import { MediaLibrary } from "../../media/MediaLibrary.js";

export async function openMediaLibrary(editor, elementId) {
    // console.log("Opening media library for editor:", elementId);
    showSpinner();

    try {
        const response = await axiosClient.get("/media-library/modal");

        // Buat container untuk modal
        const modalContainer = document.createElement("div");
        modalContainer.innerHTML = response.data;
        document.body.appendChild(modalContainer);

        // Cari modal dari response
        const modal = modalContainer.querySelector(".modal");
        if (modal) {
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();

            // 🔥 Inisialisasi MediaLibrary instance
            const mediaLibrary = new MediaLibrary({
                mode: "picker",
                allowUpload: true,
                allowEdit: true,
                allowDelete: true,
                multiSelect: true,
                acceptedTypes: [
                    "image/*",
                    "video/*",
                    "audio/*",
                    "application/*",
                ],
                folder: "",
                collection: "",
            });

            // console.log("Modal opened successfully");

            // Setup event untuk tombol/aksi dalam modal
            setupModalMediaSelection(modal, editor, modalInstance);

            // Dengarkan event "mediaSelected" sekali saja
            document.addEventListener(
                "mediaSelected",
                (e) => {
                    handleMediaSelection(e.detail, editor);
                    modalInstance.hide();
                },
                { once: true }
            );

            // Cleanup modal ketika ditutup
            modal.addEventListener("hidden.bs.modal", () => {
                document.body.removeChild(modalContainer);
            });
        }
    } catch (error) {
        console.error("Error loading media modal:", error);
        showToast("Failed to load media library", "error");
    } finally {
        hideSpinner();
    }
}

export function setupModalMediaSelection(modal, editor, modalInstance) {
    const selectBtn = modal.querySelector("#selectMediaForSettings");
    let selectedMedia = null;

    // Handle media item clicks
    const observer = new MutationObserver(() => {
        const mediaItems = modal.querySelectorAll(
            ".media-item:not(.folder-item)"
        );

        mediaItems.forEach((item) => {
            item.addEventListener(
                "click",
                (e) => {
                    if (
                        e.target.closest("button") ||
                        e.target.type === "checkbox"
                    )
                        return;

                    // Clear selections
                    mediaItems.forEach((i) => i.classList.remove("selected"));
                    item.classList.add("selected");

                    // Get media data
                    const mediaId = item.dataset.id;
                    selectedMedia = {
                        id: mediaId,
                        url: item.dataset.url || item.querySelector("img")?.src,
                        name:
                            item.dataset.name || item.querySelector("img")?.alt,
                    };

                    if (selectBtn) {
                        selectBtn.disabled = false;
                    }
                },
                { once: true }
            );
        });
    });

    const mediaGrid = modal.querySelector("#mediaGrid");
    if (mediaGrid) {
        observer.observe(mediaGrid, { childList: true, subtree: true });
    }

    // Handle select button click
    if (selectBtn) {
        selectBtn.addEventListener("click", () => {
            if (selectedMedia) {
                handleMediaSelection(selectedMedia, editor);
                modalInstance.hide();
            }
        });
    }
}

export function handleMediaSelection(selectedMedia, editor) {
    // console.log("Media selected:", selectedMedia);

    if (selectedMedia && selectedMedia.url) {
        // Insert image into CKEditor
        editor.model.change((writer) => {
            const imageElement = writer.createElement("imageBlock", {
                src: selectedMedia.url,
                alt: selectedMedia.name || "",
            });

            editor.model.insertContent(imageElement);
        });

        showToast("Image inserted successfully", "success");
    }
}

export function showSpinner() {
    if (spinner) {
        spinner.classList.remove("visually-hidden");
    }
}

export function hideSpinner() {
    if (spinner) {
        spinner.classList.add("visually-hidden");
    }
}
