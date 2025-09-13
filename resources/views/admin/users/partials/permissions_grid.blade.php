@php
    $categories = array_keys($categoryPermissions);
@endphp

<div class="row g-3" style="padding-left: 1rem;">
    <h4 class="card-title mb-2">Permissions Access</h4>

    @foreach ($categories as $category)
        @php
            $permissions = $categoryPermissions[$category];
        @endphp
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="form-group border rounded p-3" style="min-height: 250px;">
                <label class="form-check">
                    <strong><i class="bi bi-three-dots"></i> {{ ucfirst($category) }} <i
                            class="bi bi-three-dots"></i></strong>
                </label>
                <div class="row g-2 mt-2">
                    @foreach ($permissions as $permission)
                        @php
                            $hasPermission = $user->hasPermissionTo($permission);
                        @endphp
                        <div class="form-check col-12 d-flex align-items-start">
                            <input type="checkbox" class="form-check-input me-2" name="permissions[]"
                                value="{{ $permission }}" {{ $hasPermission ? 'checked' : '' }}>
                            <label
                                class="form-check-label">{{ is_string($permission) ? $permission : $permission->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>
