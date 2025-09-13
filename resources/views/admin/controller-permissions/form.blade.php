<div class="modal-content">
    <form
        action="{{ $results->id ? url('api/controller-permissions', $results->id) : url('api/controller-permissions') }}"
        method="post" class="FormAction" id="FormAction">
        @if ($results->id)
            @method('put')
        @endif
        @csrf

        <div class="modal-header">
            <h5 class="modal-title">{{ $results->id ? 'Edit' : 'Tambah' }} Mapping Permission</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="mb-3">
                    <x-forms.input label="Controller" type="text" name="controller" id="controller" :useEmoji="true"
                        emoji="🎮" value="{{ old('controller', $results->controller ?? '') }}"
                        placeholder="Contoh: RolesController" />
                </div>
                <div class="mb-3">
                    @php
                        $currentMethod = old('method', $results->method ?? '');
                        // Get all unique methods from database
                        $methodOptions = \App\Models\ControllerPermission::distinct()
                            ->pluck('method', 'method')
                            ->sort()
                            ->toArray();
                    @endphp
                    <label class="form-label">⚙️ Method</label>
                    <select class="form-select" name="method" id="method">
                        <option value="">Pilih Method</option>
                        @foreach ($methodOptions as $value => $label)
                            <option value="{{ $value }}" {{ $currentMethod == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Method yang tersedia dari database</small>
                </div>
                <div class="mb-3">
                    @php
                        $currentPermissions = old('permissions', $results->permissions ?? []);
                        // Debug: tampilkan data
                        // dd($currentPermissions, $results->permissions);
                    @endphp
                    <label class="form-label">🔐 Permissions</label>
                    <select class="form-select form-select-sm permissions_validat" name="permissions[]" id="permissions"
                        data-name="permissions" data-placeholder="Pilih Permissions" multiple>
                        @foreach ($permissions as $permission)
                            @php
                                $isSelected =
                                    is_array($currentPermissions) && in_array($permission->name, $currentPermissions);
                            @endphp
                            <option value="{{ $permission->name }}" {{ $isSelected ? 'selected' : '' }}>
                                {{ $permission->name }}
                            </option>
                        @endforeach
                    </select>
                    {{-- Debug info --}}
                    <small class="text-muted">Current: {{ json_encode($currentPermissions) }}</small>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_active" value="1"
                            {{ old('is_active', $results->is_active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label">✅ Aktif</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <x-button id="btnAction">Simpan</x-button>
        </div>
    </form>
</div>
