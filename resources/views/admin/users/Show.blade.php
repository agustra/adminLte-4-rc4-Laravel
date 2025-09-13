

<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>


<div class="modal-content">
    <div class="card mb-3 border-0 shadow-none">
        <div class="d-flex justify-content-center align-items-center bg-warning card-img-top" style="height: 200px;">
            <div class="text-center">
                <div class="rounded-circle d-flex justify-content-center align-items-center bg-white shadow"
                    style="width: 100px; height: 100px; overflow: hidden;">
                    <img src="{{ $user->avatar_url }}" alt="avatar" class="img-fluid rounded-circle"
                        style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <h2 class="mt-3">{{ ucfirst($user->name) }}</h2>
            </div>
        </div>
        <div class="card-body">

            <div class="permissions-section mt-4 px-3">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-shield-check text-success me-2 fs-5"></i>
                    <h4 class="card-title mb-0 text-primary">Permissions Access</h4>
                </div>
                
                <div class="row g-3">
                    @foreach ($categoryPermissions as $category => $permissions)
                        @php
                            $hasPermissionsInCategory = false;
                            foreach ($permissions as $permission) {
                                if ($user->hasPermissionTo($permission)) {
                                    $hasPermissionsInCategory = true;
                                    break;
                                }
                            }
                        @endphp

                        @if ($hasPermissionsInCategory)
                            <div class="col-md-6 col-lg-4">
                                <div class="permission-card border rounded-3 p-3 h-100 bg-light">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-folder2-open text-warning me-2"></i>
                                        <h6 class="mb-0 fw-bold text-dark">{{ __(ucfirst($category)) }}</h6>
                                    </div>
                                    <div class="permission-list">
                                        @foreach ($permissions as $permission)
                                            @if ($user->hasPermissionTo($permission))
                                                <div class="d-flex align-items-center mb-1">
                                                    <i class="bi bi-check-circle-fill text-success me-2" style="font-size: 0.8rem;"></i>
                                                    <span class="text-muted small">
                                                        {{ is_string($permission) ? $permission : $permission->name }}
                                                    </span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- <div class="card bg-light border-0 rounded-3 p-4 mx-auto" style="max-width: 450px;">
            </div> --}}
        </div>
    </div>

    <div class="d-flex justify-content-end" style="padding-right: 1rem; padding-bottom: 1rem;">
        <img src="{{ url('/icons/back.png') }}" alt="Icon" data-bs-dismiss="modal"
            style="width: 30px; height: auto; margin-right: 10px; cursor: pointer; transition: transform 0.2s ease, opacity 0.2s ease;">
    </div>
</div>
