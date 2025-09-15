const apiCall = getApiConfig(); // Memanggil dengan parameter yang sesuai

export function getApiConfig() {
    return {
        api: {
            url: generalConfig.urlApi,
            headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                )?.content,
            },
            beforeSend: (params) => Object.assign(params, getApiParams()),
            success: (response) => {
                const permissions = response?.meta?.permissions;
                if (permissions) handlePermissionButtons(permissions);
            },
        },
    };
}

export const buttonConfigs = [
    "copy",
    {
        extend: "csv",
        text: "Export",
        className: "btn btn-success btn-sm btn-csv",
        filename: `users-export-${today}`,
        exportColumns: ["name", "email"],
        titleAttr: "Export data as CSV file (Excel compatible)",
    },
    {
        extend: "pdf",
        text: "PDF",
        className: "btn btn-danger btn-sm btn-pdf",
        filename: `users-report-${today}`,
        orientation: "landscape",
        pageSize: "A4",
        exportColumns: ["name", "email", "status"],
        titleAttr: "Export data as PDF file",
    },
    {
        extend: "print",
        text: "Print",
        className: "btn btn-warning btn-sm btn-print",
        orientation: "portrait",
        exportColumns: ["name", "email"],
        titleAttr: "Print selected columns with custom styling",
    },
    "colvis",
    {
        text: 'Delete Bulk (<span class="selected-count">0</span>)',
        className: "btn btn-danger btn-sm btn-delete",
        enabled: false,
        attr: { id: "delete-selected-btn", style: "display: none;" },
        action: handleBulkDelete,
    },
];

export function getColumnsConfig() {
    return [
        {
            data: "DT_RowIndex",
            title: "No",
            orderable: false,
            searchable: false,
        },
        {
            data: "avatar_url",
            title: "Avatar",
            orderable: false,
            searchable: false,
            render: (_, __, row) => {
                const avatarUrl =
                    row.avatar_url || "/storage/filemanager/images/public/avatar-default.webp";
                return `<img src="${avatarUrl}" alt="Avatar" width="40" height="40" style="border-radius: 50%;">`;
            },
        },
        { data: "name", title: "Name", orderable: true },
        { data: "email", title: "Email", orderable: true },
        {
            data: "roles",
            title: "Roles",
            orderable: false,
            render: (_, __, row) =>
                row.roles?.length
                    ? row.roles
                          .map(
                              (role) =>
                                  `<span class="badge bg-primary me-1">${role.name}</span>`
                          )
                          .join("")
                    : "-",
        },
        {
            data: "permissions_count",
            title: "Permissions",
            orderable: false,
            render: (_, __, row) =>
                formatPermissionsColumn(row.permissions ?? [], row, "user"),
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
            render: (_, __, row) => ActionButton(row),
        },
    ];
}
