<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Menu Details</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-content">
    <div class="card mb-3 border-0 shadow-none">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th width="200">Name</th>
                    <td>{{ $menu->name }}</td>
                </tr>
                <tr>
                    <th>URL</th>
                    <td>{{ $menu->url ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Icon</th>
                    <td>
                        @if($menu->icon)
                            <i class="{{ $menu->icon }}"></i> {{ $menu->icon }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Permission</th>
                    <td>{{ $menu->permission ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Parent Menu</th>
                    <td>{{ $menu->parent ? $menu->parent->name : '-' }}</td>
                </tr>
                <tr>
                    <th>Order</th>
                    <td>{{ $menu->order }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge {{ $menu->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $menu->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $menu->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
                <tr>
                    <th>Updated At</th>
                    <td>{{ $menu->updated_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            </table>
            
            @if($menu->children->count() > 0)
                <h5 class="mt-4">Child Menus</h5>
                <ul class="list-group">
                    @foreach($menu->children as $child)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $child->name }}
                            <span class="badge {{ $child->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $child->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
    
    <div class="d-flex justify-content-end" style="padding-right: 1rem; padding-bottom: 1rem;">
        <img src="{{ url('/icons/back.png') }}" alt="Icon" data-bs-dismiss="modal"
            style="width: 30px; height: auto; margin-right: 10px; cursor: pointer; transition: transform 0.2s ease, opacity 0.2s ease;">
    </div>
</div>