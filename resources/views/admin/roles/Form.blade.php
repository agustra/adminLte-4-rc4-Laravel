@php
    $title = $role->id ? 'Edit' : 'Tambah';
@endphp


<div class="modal-content">
    <form action="{{ $role->id ? url('api/roles', $role->id) : url('api/roles') }}" method="post" class="FormAction"
        id="FormAction">
        @if ($role->id)
            @method('put')
        @endif
        @csrf


        <div class="loading position-absolute top-50 start-50 translate-middle" style="z-index: 10; ">

        </div>

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{ $title }} Role</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="mb-3">
                    <x-forms.input label="Role" name="name" id="role" :useEmoji="true"
                        emoji="📝" value="{{ old('name', $role->name ?? '') }}" />
                </div>

                <div class="mb-3">
                    <x-forms.tomSelect label="Permissions" id="permissions" name="permissions" :useEmoji="true"
                        emoji="🔑" value="{{ $role->permissions->pluck('id')->implode(',') }}" 
                        :multiple="true" />
                    <small class="form-text text-muted">
                        <i class="bi bi-info-circle"></i>
                        Pilih permissions yang akan diberikan kepada role ini.
                    </small>
                </div>

                <!-- Permission Badge Display -->
                <div class="mt-3" id="permission-display">
                    <h6>Selected Permissions:</h6>
                    <div id="permission-badges" class="d-flex flex-wrap gap-1"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <x-button id="btnAction">Simpan</x-button>
        </div>
    </form>
</div>
