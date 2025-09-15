@php
    $imageSrc = $user->avatar_url ?? asset('storage/filemanager/images/public/avatar-default.webp');
@endphp

<div class="modal-content">
    <form enctype="multipart/form-data" action="{{ $user->id ? url('api/users/' . $user->id) : url('api/users') }}"
        method="post" class="FormAction" id="FormAction">
        @if ($user->id)
            @method('put')
        @endif
        @csrf

        <div class="loading">

        </div>

        <div class="modal-header">
            <h5 class="modal-title" id="ModalLabel"></i></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">❌</button>
        </div>
        <div class="modal-body">
            <div class="row">

                <div class="row">
                    <!-- Name -->
                    <div class="col-md-6">
                        <x-forms.input label="Name" name="name" id="name" :useEmoji="true" emoji="👤"
                            value="{{ old('name', $user->name ?? '') }}" />
                    </div>

                    <!-- Email -->
                    <div class="col-md-6">
                        <x-forms.input label="Email" name="email" id="email" type="email" :useEmoji="true"
                            emoji="📧" value="{{ old('email', $user->email ?? '') }}" />
                    </div>
                </div>

                <!-- Password Fields -->
                <div class="row mt-2">
                    @if ($user->id)
                        <!-- Old Password -->
                        <div class="col-md-4">
                            <x-forms.input label="Old Password" name="old_password" id="old_password" type="password"
                                :useEmoji="true" emoji="🔒" value="" />
                        </div>
                    @endif

                    <!-- New Password -->
                    <div class="col-md-4">
                        <x-forms.input label="Password" name="password" id="password" type="password" :useEmoji="true"
                            emoji="🔑" value="" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="col-md-4">
                        <x-forms.input label="Confirm Password" name="password_confirmation" id="password_confirmation"
                            type="password" :useEmoji="true" emoji="🔑" value="" />
                    </div>
                </div>

                <div class="col-md-12 mt-2">
                    <x-forms.tomSelect label="Roles" name="roles" id="roles" :useEmoji="true" emoji="👥"
                        :multiple="true" class="role-select" :value="$user->roles->pluck('id')->toArray()" />
                </div>

                <!-- Permission Display (Read-only) -->
                <div class="col-md-12 mt-2">
                    <label class="form-label">🔑 Permissions (Role-based)</label>
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i>
                            Permissions otomatis berdasarkan role yang dipilih.
                            Untuk mengubah permissions, edit role di halaman Role Management.
                        </small>
                    </div>
                    <div id="permission-display" class="border rounded p-2 bg-light">
                        <div id="permission-badges" class="d-flex flex-wrap gap-1">
                            <span class="badge bg-secondary">Permissions akan muncul berdasarkan role</span>
                        </div>
                    </div>
                </div>


                <!-- Avatar Upload -->
                <div class="form-group col-md-12 mt-3">
                    <label class="form-label fw-bold">📷 Profile Avatar</label>

                    <div class="d-flex justify-content-center mb-3">
                        <div class="avatar-upload-container position-relative">
                            <!-- Main Avatar Display -->
                            <div class="avatar-wrapper position-relative" id="selectAvatarBtn"
                                style="width: 200px; height: 200px;">
                                <img src="{{ $imageSrc }}" alt="User Avatar" class="avatar-preview shadow-lg"
                                    id="avatarPreview"
                                    style="width: 200px; height: 200px; border-radius: 25px; object-fit: cover; border: 4px solid #e9ecef; transition: all 0.3s ease; cursor: pointer;">

                                <!-- Hover Overlay -->
                                <div class="avatar-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                                    style="background: linear-gradient(135deg, rgba(0,123,255,0.8), rgba(108,117,125,0.8)); border-radius: 21px; opacity: 0; transition: all 0.3s ease; cursor: pointer;"
                                    id="avatarOverlay">
                                    <div class="text-center text-white">
                                        <i class="fas fa-camera fa-3x mb-2"></i>
                                        <div class="fw-bold">Change Photo</div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="avatar-actions position-absolute" style="bottom: -10px; right: -10px;">
                                    <div class="btn-group-vertical shadow-sm">
                                        <button type="button" class="btn btn-primary btn-sm rounded-circle"
                                            id="selectAvatarBtn" title="Select from Media Library"
                                            style="width: 40px; height: 40px;">
                                            <i class="fas fa-images"></i>
                                        </button>
                                        @if ($user->id)
                                            <button type="button" class="btn btn-danger btn-sm rounded-circle mt-1"
                                                class="btn-delete-avatar" data-id="{{ $user->id }}"
                                                title="Remove Avatar" style="width: 40px; height: 40px;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Upload Info -->
                            <div class="text-center mt-3">
                                <div class="badge bg-light text-dark px-3 py-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Click to select from Media Library
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        Recommended: 400x400px, JPG/PNG/WebP
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden input for avatar URL -->
                    <input type="hidden" name="avatar" id="avatarInput"
                        value="{{ old('avatar', $user->profile_photo_path && !str_starts_with($user->profile_photo_path, 'avatars/') ? asset('storage/' . $user->profile_photo_path) : '') }}">
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Close</button>
                <x-button id="btnAction">Simpan</x-button>
            </div>
    </form>
</div>

<style>
    .avatar-wrapper:hover .avatar-preview {
        transform: scale(1.05);
        border-color: #007bff !important;
    }

    .avatar-wrapper:hover .avatar-overlay {
        opacity: 1 !important;
    }

    .avatar-actions .btn {
        backdrop-filter: blur(10px);
        border: 2px solid white;
        transition: all 0.3s ease;
    }

    .avatar-actions .btn:hover {
        transform: scale(1.1);
    }

    .avatar-overlay {
        cursor: pointer;
    }
</style>
