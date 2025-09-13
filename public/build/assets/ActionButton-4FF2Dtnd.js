function e(t){var i,a,s;const n=[];return(i=t.actions)!=null&&i.can_show&&n.push(`
            <button class="btn btn-sm btn-info mx-1 buttonShow" data-id="${t.id}" title="Lihat Detail">
                <i class="fas fa-eye"></i>
            </button>
        `),(a=t.actions)!=null&&a.can_edit&&n.push(`
            <button class="btn btn-sm btn-warning mx-1 buttonUpdate" data-id="${t.id}" title="Edit Data">
                <i class="fas fa-edit"></i>
            </button>
        `),(s=t.actions)!=null&&s.can_delete&&n.push(`
            <button class="btn btn-sm btn-danger mx-1 btn-delete" data-id="${t.id}" title="Hapus Data">
                <i class="fas fa-trash"></i>
            </button>
        `),`<div class="d-flex flex-nowrap justify-content-center">${n.join("")}</div>`}export{e as A};
