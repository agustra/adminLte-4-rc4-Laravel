export function ActionButton(params) {
    const buttons = [];

    if (params.actions?.can_show) {
        buttons.push(`
            <button class="btn btn-sm btn-info mx-1 buttonShow" data-id="${params.id}" title="Lihat Detail">
                <i class="fas fa-eye"></i>
            </button>
        `);
    }

    if (params.actions?.can_edit) {
        buttons.push(`
            <button class="btn btn-sm btn-warning mx-1 buttonUpdate" data-id="${params.id}" title="Edit Data">
                <i class="fas fa-edit"></i>
            </button>
        `);
    }

    if (params.actions?.can_delete) {
        buttons.push(`
            <button class="btn btn-sm btn-danger mx-1 btn-delete" data-id="${params.id}" title="Hapus Data">
                <i class="fas fa-trash"></i>
            </button>
        `);
    }

    return `<div class="d-flex flex-nowrap justify-content-center">${buttons.join(
        ""
    )}</div>`;
}
