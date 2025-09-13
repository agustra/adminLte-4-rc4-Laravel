// js/components/ckeditor/CKEditorMediaLibrary.js

import { openMediaLibrary } from "../media-picker/openMediaLibrary.js";

export const editors = new Map();

export async function initializeCKEditor(elementId) {
    const element = document.getElementById(elementId);
    if (!element) {
        console.warn(`CKEditor: Element with id "${elementId}" not found`);
        return null;
    }

    try {
        // Hancurkan editor lama kalau ada
        if (editors.has(elementId)) {
            const oldEditor = editors.get(elementId); 
            if (oldEditor) {
                await oldEditor.destroy().catch((err) => {
                    console.warn(
                        `Error destroying old CKEditor for ${elementId}:`,
                        err
                    );
                });
            }
            editors.delete(elementId);
        }

        // Buat editor baru
        const editor = await ClassicEditor.create(element, {
            toolbar: [
                "heading",
                "|",
                "bold",
                "italic",
                "link",
                "|",
                "bulletedList",
                "numberedList",
                "|",
                "outdent",
                "indent",
                "|",
                "blockQuote",
                "insertTable",
                "|",
                "undo",
                "redo",
            ],
            image: {
                toolbar: [
                    "imageTextAlternative",
                    "imageStyle:inline",
                    "imageStyle:block",
                    "imageStyle:side",
                ],
            },
        });

        // Simpan instance
        editors.set(elementId, editor);

        // Tambahkan tombol custom kalau ada
        if (typeof addMediaLibraryButton === "function") {
            addMediaLibraryButton(editor, elementId);
        }

        return editor;
    } catch (error) {
        console.error(
            `❌ Failed to initialize CKEditor for "${elementId}":`,
            error
        );
        return null;
    }
}

export function addMediaLibraryButton(editor, elementId) {
    // Tambahkan tombol custom ke toolbar CKEditor
    setTimeout(() => {
        const toolbar = editor.ui.view.toolbar;
        if (!toolbar || !toolbar.element) {
            console.warn("CKEditor toolbar not found");
            return;
        }

        const buttonElement = createMediaButtonElement(editor, elementId);
        const toolbarItems =
            toolbar.element.querySelector(".ck-toolbar__items");

        if (toolbarItems && buttonElement) {
            toolbarItems.appendChild(buttonElement);
        }
    }, 500);
}

export function createMediaButtonElement(editor, elementId) {
    const button = document.createElement("button");
    button.type = "button";
    button.className = "ck ck-button ck-off";
    button.innerHTML = `
            <svg class="ck ck-icon ck-button__icon" viewBox="0 0 20 20" style="width: 20px; height: 20px;">
                <rect x="2" y="3" width="16" height="12" rx="2" fill="none" stroke="currentColor" stroke-width="1.5"/>
                <circle cx="7" cy="7" r="1.5" fill="currentColor"/>
                <path d="m12 11-2-2-3 3-2-2-3 3" stroke="currentColor" stroke-width="1.5" fill="none"/>
            </svg>
            <span class="ck ck-button__label">Media</span>
        `;
    button.title = "Open Media Library";

    button.addEventListener("click", () => {
        if (typeof openMediaLibrary === "function") {
            openMediaLibrary(editor, elementId);
        }
    });

    return button;
}

export function syncCKEditorData() {
    const syncedIds = [];
    editors.forEach((editor, elementId) => {
        const element = document.getElementById(elementId);
        if (element) {
            element.value = editor.getData();
            syncedIds.push(elementId);
        }
    });
    return syncedIds;
}