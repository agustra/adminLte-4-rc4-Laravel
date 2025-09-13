<div class="btn-group" role="group">
    @can('read menus', 'web')
        <button class="btn btn-info btn-sm buttonShow" data-id="{{ $menu->id }}" title="View">
            <i class="fa fa-eye"></i>
        </button>
    @endcan
 
    @can('edit menus', 'web')
        <button class="btn btn-warning btn-sm buttonUpdate" data-id="{{ $menu->id }}" title="Edit">
            <i class="fa fa-edit"></i>
        </button>
    @endcan

    @can('delete menus', 'web')
        <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $menu->id }}" title="Delete">
            <i class="fa fa-trash"></i>
        </button>
    @endcan
</div>
