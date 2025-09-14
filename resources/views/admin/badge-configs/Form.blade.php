<div class="modal-content">
    <form action="{{ $config->id ? url('api/badge-configs/' . $config->id) : url('api/badge-configs') }}" method="post"
        class="FormAction" id="FormAction">
        @if ($config->id)
            @method('put')
        @endif
        @csrf

        <div class="loading"></div>

        <div class="modal-header">
            <h5 class="modal-title" id="ModalLabel">{{ $config->id ? 'Edit Badge Config' : 'Create Badge Config' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">❌</button>
        </div>

        <div class="modal-body">
            <!-- Badge Configuration Guide -->
            <div class="alert alert-info mb-3">
                <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Panduan Badge Configuration</h6>
                <div class="row">
                    <div class="col-md-6">
                        <small>
                            <strong>🔗 Menu URL:</strong> URL menu yang akan menampilkan badge<br>
                            <strong>📦 Model Class:</strong> Model yang akan dihitung datanya<br>
                            <strong>📅 Date Field:</strong> Field tanggal untuk filter hari ini
                        </small>
                    </div>
                    <div class="col-md-6">
                        <small>
                            <strong>✅ Status:</strong> Aktif/nonaktif badge<br>
                            <strong>📝 Description:</strong> Keterangan konfigurasi<br>
                            <strong>🎯 Contoh:</strong> /admin/users → App\Models\User → created_at
                        </small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    @component('components.forms.input', [
                        'label' => 'Menu URL',
                        'name' => 'menu_url',
                        'id' => 'menu_url',
                        'useIcon' => false,
                        'useEmoji' => true,
                        'emoji' => '🔗',
                        'value' => old('menu_url', $config->menu_url ?? ''),
                        'isReadOnly' => false,
                        'placeholder' => '/admin/users',
                    ])
                    @endcomponent
                    <small class="text-muted">Contoh: <code>/admin/users</code>, <code>/dashboard</code></small>
                </div>

                <div class="col-md-6 mb-3">
                    <x-forms.tomSelect label="Model Class" id="model_class" name="model_class" :useEmoji="true"
                        emoji="📦" value="{{ $modelClassValue }}" :options="$modelClassOptions" />
                    <small class="text-muted">Model yang akan dihitung badge-nya. Pilih dari list atau ketik custom model class.</small>
                </div>

                <div class="col-md-6 mb-3">
                    <x-forms.tomSelect label="Date Fields" id="date_fields" name="date_fields" :useEmoji="true"
                        emoji="📅" :value="$currentFields" :multiple="true" :options="$dateFieldOptions" />
                    <small class="text-muted">Pilih atau ketik field tanggal yang akan dihitung untuk badge. Bisa
                        multiple field.</small>
                </div>

                <div class="col-md-6 mb-3">
                    <x-forms.tomSelect label="Status" id="is_active" name="is_active" :useEmoji="true" emoji="📊"
                        value="{{ $statusValue }}" :options="[1 => '✅ Active', 0 => '❌ Inactive']" />
                    <small class="text-muted">Status aktif/nonaktif badge</small>
                </div>

                <div class="col-md-12">
                    @component('components.forms.input', [
                        'label' => 'Description',
                        'name' => 'description',
                        'id' => 'description',
                        'useIcon' => false,
                        'useEmoji' => true,
                        'emoji' => '📝',
                        'value' => old('description', $config->description ?? ''),
                        'isReadOnly' => false,
                        'placeholder' => 'Keterangan konfigurasi badge...',
                    ])
                    @endcomponent
                    <small class="text-muted">Keterangan optional untuk konfigurasi ini</small>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Close</button>
            <x-button id="btnAction">Simpan</x-button>
        </div>
    </form>
</div>
