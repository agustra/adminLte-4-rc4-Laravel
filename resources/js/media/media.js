import "@tables/EasyDataTable.js";
import { createEasyTableBulkDelete } from "@tables/easyTableBulkDelete.js";
import { Delete } from "@helpers/delete.js";
import { showModal } from "@handlers/modalHandler.js";
import { fetchAxios } from "@handlers/fetchAxios.js";

// Initialize table
let table;

document.addEventListener("DOMContentLoaded", function () {
    // Check if table element exists before initializing
    const tableElement = document.querySelector("#table-media");
    if (!tableElement) {
        console.log('Media table not found, skipping DataTable initialization');
        return;
    }
    const Config = {
        urlWeb: "/media-library/",
        urlApi: "/api/media-management",
        deleteMultipleUrl: "/api/media-management/multiple/delete/",
    };

    // Konfigurasi tabel
    const tableConfig = {
        selector: "#table-media",
        apiUrl: Config.urlApi,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        select: true,
        order: { column: 1, dir: "desc" },
        responsive: true,
    };

    // Konfigurasi kolom
    const columns = [
        {
            data: "DT_RowIndex",
            title: "No",
            orderable: false,
            searchable: false,
        },
        {
            data: "url",
            title: "Preview",
            orderable: false,
            searchable: false,
            render: (data, type, row) => {
                if (row.mime_type && row.mime_type.startsWith("image/")) {
                    return `<img src="${data}" alt="Preview" width="40" height="40" 
                            style="object-fit: cover; border-radius: 4px; cursor: pointer;" 
                            onclick="showMediaPreview('${data}', '${row.mime_type}', '${row.name}')">`;
                } else {
                    return `<i class="fas fa-file text-muted" style="font-size: 24px;"></i>`;
                }
            },
        },
        { data: "name", title: "Name", orderable: true },
        { data: "file_name", title: "File", orderable: true },
        {
            data: "collection",
            title: "Collection",
            orderable: false,
            render: (data) => `<span class="badge bg-primary">${data || 'default'}</span>`,
        },
        {
            data: "mime_type",
            title: "Type",
            orderable: false,
            render: (data) => {
                const type = data ? data.split("/")[0] : "unknown";
                const colors = {
                    image: "success",
                    video: "info", 
                    audio: "warning",
                    application: "secondary",
                    text: "dark",
                };
                return `<span class="badge bg-${colors[type] || "secondary"}">${type}</span>`;
            },
        },
        { data: "size", title: "Size", orderable: true },
        {
            data: "model_type",
            title: "Model",
            orderable: false,
            render: (data, type, row) => `${data || 'Media'} #${row.model_id || row.id}`,
        },
        {
            data: "created_at",
            title: "Created",
            orderable: true,
            render: (data) => new Date(data).toLocaleDateString("id-ID"),
        },
        {
            data: "actions",
            title: "Action",
            className: "text-center",
            orderable: false,
            searchable: false,
            render: (data, type, row) => data || "",
        },
    ];

    // Konfigurasi tombol
    const buttons = [
        {
            id: "btnTambah",
            text: '<i class="fas fa-plus"></i> Add Media',
            className: "btn btn-primary btn-sm me-1",
            style: "display: none;",
            action: () => showModal(`${Config.urlWeb}create`, "create"),
        },
        {
            id: "btnDeleteSelected", 
            text: '<i class="fas fa-trash"></i> Delete',
            className: "btn btn-danger btn-sm me-1",
            style: "display: none;",
            action: () => bulkDelete?.execute(),
        },
        "copy",
        "csv", 
        "excel",
        "pdf",
        "print",
    ];

    // Inisialisasi EasyDataTable
    table = new EasyDataTable(tableConfig.selector, {
        apiUrl: Config.urlApi,
        columns: columns,
        pageLength: tableConfig.pageLength,
        lengthMenu: tableConfig.lengthMenu,
        select: tableConfig.select,
        order: tableConfig.order,
        responsive: tableConfig.responsive,
        buttons: buttons,
        onDataLoaded: (data) => {
            handlePermissionButtons(data.meta?.permissions);
        },
    });

    function handlePermissionButtons(permissions) {
        const tambahBtn = document.querySelector("#btnTambah");
        const hapusBtn = document.querySelector("#btnDeleteSelected");

        const perms = permissions || {};

        if (tambahBtn) {
            if (perms.create) {
                tambahBtn.style.display = "block";
                tambahBtn.disabled = false;
            } else {
                tambahBtn.style.display = "none";
                tambahBtn.disabled = true;
            }
        }

        if (hapusBtn) {
            if (perms.delete) {
                hapusBtn.style.display = "none"; // Hide initially, show when rows selected
                hapusBtn.disabled = false;
            } else {
                hapusBtn.style.display = "none";
                hapusBtn.disabled = true;
            }
        }

        window.currentPermissions = perms;
    }

    // Delete functionality
    Delete({
        buttonSelector: ".btn-delete",
        deleteUrl: "/api/media-management",
        tableSelector: tableConfig.selector,
        onDeleteSuccess: () => {
            table.reload();
            table.clearSelection();
        },
    });

    // Inisialisasi bulk delete
    const bulkDelete = createEasyTableBulkDelete({
        tableInstance: table,
        deleteUrl: Config.deleteMultipleUrl,
        confirmMessage: "Yakin ingin menghapus {count} media terpilih?",
        onDeleteSuccess: (deletedIds) =>
            console.log("Bulk delete berhasil:", deletedIds),
    });

    // Event listeners
    document.body.addEventListener("click", (e) => {
        const editBtn = e.target.closest(".buttonUpdate");
        const showBtn = e.target.closest(".buttonShow");
        const saveBtn = e.target.matches("#btnAction");
        const createBtn = e.target.closest(".btnTambah");

        if (createBtn) {
            e.preventDefault();
            showModal(`${Config.urlWeb}create`, "create");
        } else if (editBtn) {
            e.preventDefault();
            const url = `${Config.urlWeb}${editBtn.dataset.id}/edit`;
            showModal(url, "edit");
        } else if (showBtn) {
            e.preventDefault();
            const url = `${Config.urlWeb}${showBtn.dataset.id}`;
            showModal(url, "show");
        } else if (saveBtn) {
            e.preventDefault();
            const form = document.querySelector(".FormAction");
            if (form) {
                fetchAxios(
                    {
                        url: form.action,
                        method: form.method,
                        data: new FormData(form),
                    },
                    "simpan",
                    () => table.reload()
                );
            }
        }
    });
});

// Function untuk show media preview
window.showMediaPreview = function (url, mimeType, name) {
    const modal = new bootstrap.Modal(
        document.getElementById("mediaPreviewModal")
    );
    const content = document.getElementById("media-preview-content");

    if (mimeType.startsWith("image/")) {
        content.innerHTML = `<img src="${url}" alt="${name}" class="img-fluid" style="max-height: 500px;">`;
    } else if (mimeType.startsWith("video/")) {
        content.innerHTML = `<video controls class="w-100" style="max-height: 500px;">
                               <source src="${url}" type="${mimeType}">
                               Browser Anda tidak mendukung video.
                             </video>`;
    } else if (mimeType.startsWith("audio/")) {
        content.innerHTML = `<audio controls class="w-100">
                               <source src="${url}" type="${mimeType}">
                               Browser Anda tidak mendukung audio.
                             </audio>`;
    } else {
        content.innerHTML = `<div class="text-center">
                               <i class="fas fa-file fa-5x text-muted mb-3"></i>
                               <p>File: ${name}</p>
                               <a href="${url}" target="_blank" class="btn btn-primary">
                                 <i class="fas fa-download"></i> Download
                               </a>
                             </div>`;
    }

    modal.show();
};