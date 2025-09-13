@props([
    'action',
    'readPermission' => null,
    'editPermission' => null,
    'deletePermission' => null,
    'guard' => 'web'
])

<div class="btn-group" role="group">
    @if($readPermission)
        @can($readPermission, $guard)
            <button class="btn btn-info btn-sm buttonShow" data-id="{{ $action->id }}" title="View">
                <i class="fa fa-eye"></i>
            </button>
        @endcan
    @endif

    @if($editPermission)
        @can($editPermission, $guard)
            <button class="btn btn-warning btn-sm buttonUpdate" data-id="{{ $action->id }}" title="Edit">
                <i class="fa fa-edit"></i>
            </button>
        @endcan
    @endif

    @if($deletePermission)
        @can($deletePermission, $guard)
            <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $action->id }}" title="Delete">
                <i class="fa fa-trash"></i>
            </button>
        @endcan
    @endif
</div>
