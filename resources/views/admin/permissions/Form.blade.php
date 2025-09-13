@php
    $title = $permission->id ? 'Edit' : 'Tambah';
@endphp

<div class="modal-content">
    <form action="{{ $permission->id ? url('api/permissions', $permission->id) : url('api/permissions') }}" method="post"
        class="FormAction" id="FormAction">
        @if ($permission->id)
            @method('put')
        @endif
        @csrf

        <div class="modal-header">
            <h5 class="modal-title">{{ $title }} Permission</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="mb-3">
                    <x-forms.input label="Permission Name" name="name" id="name" :useEmoji="true"
                        emoji="🔐" value="{{ old('name', $permission->name ?? '') }}" />
                </div>

                <div class="mb-3">
                    <x-forms.tomSelect label="Assign to Roles" id="role" name="roles" :useEmoji="true"
                        emoji="👥" value="{{ $permission->roles->pluck('id')->implode(',') }}" 
                        :multiple="true" />
                    <small class="form-text text-muted">Pilih role yang akan memiliki permission ini</small>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <x-button id="btnAction">Simpan</x-button>
        </div>
    </form>
</div>
