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
                <div class="col-md-6">
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

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="model_class">📦 Model Class</label>
                        <select class="form-select" id="model_class_select" onchange="toggleModelInput()">
                            <option value="">-- Pilih Model --</option>
                            @foreach ($availableModels as $class => $name)
                                <option value="{{ $class }}"
                                    {{ old('model_class', $config->model_class ?? '') == $class ? 'selected' : '' }}>
                                    {{ $name }} ({{ $class }})
                                </option>
                            @endforeach
                            <option value="custom">✏️ Input Manual</option>
                        </select>

                        <input type="text" class="form-control mt-2" id="model_class_input" name="model_class"
                            value="{{ old('model_class', $config->model_class ?? '') }}"
                            placeholder="App\Models\YourModel" style="display: none;">

                        <small class="text-muted">Model yang akan dihitung badge-nya. Pilih dari list atau input
                            manual.</small>
                    </div>
                </div>

                <script>
                    function toggleModelInput() {
                        const select = document.getElementById('model_class_select');
                        const input = document.getElementById('model_class_input');

                        if (select.value === 'custom') {
                            select.style.display = 'none';
                            input.style.display = 'block';
                            input.focus();
                        } else {
                            input.value = select.value;
                        }
                    }

                    // Initialize on load
                    document.addEventListener('DOMContentLoaded', function() {
                        const input = document.getElementById('model_class_input');
                        const select = document.getElementById('model_class_select');

                        if (input.value && !select.querySelector(`option[value="${input.value}"]`)) {
                            // Custom model, show input
                            select.style.display = 'none';
                            input.style.display = 'block';
                        }
                    });
                </script>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date_fields">📅 Date Fields</label>
                        @php
                            $currentFields = old(
                                'date_fields',
                                $config->date_field
                                    ? (is_array($config->date_field)
                                        ? $config->date_field
                                        : explode(',', $config->date_field))
                                    : ['created_at'],
                            );
                        @endphp

                        <select class="form-select date-fields-select" name="date_fields[]" id="date_fields"
                            data-placeholder="Pilih atau ketik field tanggal..." multiple>
                            @foreach (['created_at', 'updated_at', 'deleted_at', 'published_at', 'start_date', 'end_date', 'date', 'birth_date', 'hire_date'] as $field)
                                <option value="{{ $field }}"
                                    {{ in_array($field, $currentFields) ? 'selected' : '' }}>
                                    {{ $field }}
                                </option>
                            @endforeach
                            @foreach ($currentFields as $field)
                                @if (
                                    !in_array($field, [
                                        'created_at',
                                        'updated_at',
                                        'deleted_at',
                                        'published_at',
                                        'start_date',
                                        'end_date',
                                        'date',
                                        'birth_date',
                                        'hire_date',
                                    ]))
                                    <option value="{{ $field }}" selected>{{ $field }}</option>
                                @endif
                            @endforeach
                        </select>

                        <small class="text-muted">Pilih atau ketik field tanggal yang akan dihitung untuk badge. Bisa
                            multiple field.</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="is_active">✅ Status</label>
                        <select class="form-select" id="is_active" name="is_active">
                            <option value="1"
                                {{ old('is_active', $config->is_active ?? true) ? 'selected' : '' }}>Active</option>
                            <option value="0"
                                {{ old('is_active', $config->is_active ?? true) ? '' : 'selected' }}>Inactive</option>
                        </select>
                        <small class="text-muted">Status aktif/nonaktif badge</small>
                    </div>
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
