// js/components/ckeditor/CKEditorManager.js

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

        // CKEditor ready

        return editor;
    } catch (error) {
        console.error(
            `❌ Failed to initialize CKEditor for "${elementId}":`,
            error
        );
        return null;
    }
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