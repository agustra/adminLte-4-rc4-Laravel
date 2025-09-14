<div class="modal-content">
    <form action="{{ $menu->id ? url('api/menus/' . $menu->id) : url('api/menus') }}" method="post" class="FormAction"
        id="FormAction">
        @if ($menu->id)
            @method('put')
        @endif
        @csrf

        <div class="loading"></div>

        <div class="modal-header">
            <h5 class="modal-title" id="ModalLabel">{{ $menu->id ? 'Edit Menu' : 'Create Menu' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">❌</button>
        </div>

        <div class="modal-body">
            <!-- Menu Management Guide -->
            <div class="alert alert-info mb-3">
                <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Panduan Kelola Menu</h6>
                <div class="row">
                    <div class="col-md-6">
                        <small>
                            <strong>📋 Name:</strong> Nama menu yang tampil di sidebar<br>
                            <strong>🔗 URL:</strong> Link tujuan (gunakan <code>#</code> untuk parent menu)<br>
                            <strong>🎨 Icon:</strong> Icon FontAwesome (contoh: <code>fas fa-home</code>)<br>
                            <strong>🔐 Permission:</strong> Hak akses untuk menampilkan menu
                        </small>
                    </div>
                    <div class="col-md-6">
                        <small>
                            <strong>📁 Parent Menu:</strong> Pilih parent untuk submenu, kosongkan untuk root menu<br>
                            <strong>🔢 Order:</strong> Urutan tampil menu (angka kecil = atas)<br>
                            <strong>🏷️ Badge:</strong> Otomatis berdasarkan data baru hari ini<br>
                            <strong>✅ Active:</strong> Status aktif/nonaktif menu
                        </small>
                    </div>
                </div>
                <hr class="my-2">
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <strong>💡 Tips:</strong><br>
                            • Root menu: kosongkan Parent Menu<br>
                            • Submenu: pilih Parent Menu<br>
                            • URL <code>#</code> untuk menu parent saja
                        </small>
                    </div>
                    <div class="col-md-6">
                        <small class="text-success">
                            <strong>📝 Contoh:</strong><br>
                            • <strong>Dashboard:</strong> URL <code>/dashboard</code>, No Parent<br>
                            • <strong>Users:</strong> URL <code>/admin/users</code>, Parent: Management<br>
                            • <strong>Management:</strong> URL <code>#</code>, No Parent
                        </small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <x-forms.input label="Name" name="name" id="name" :useEmoji="true" emoji="📋"
                        value="{{ old('name', $menu->name ?? '') }}" />
                </div>

                <div class="col-md-6 mb-3">
                    <x-forms.input label="URL" name="url" id="url" :useEmoji="true" emoji="🔗"
                        value="{{ old('url', $menu->url ?? '') }}" />
                    <small class="text-muted">Contoh: <code>/admin/users</code> atau <code>#</code> untuk parent
                        menu</small>
                </div>


                {{-- @dump($menu->permission_id) --}}
                <div class="col-md-6 mb-3">
                    <x-forms.tomSelect label="roles" id="roles" name="roles" :useEmoji="true" emoji="🔐"
                        value="{{ $menu->role_id ?? '' }}" :multiple="true" />
                </div>

                <div class="col-md-6 mb-3">
                    <x-forms.tomSelect label="Permission" id="permission" name="permission" :useEmoji="true"
                        emoji="🔐" value="{{ $menu->permission_id ?? '' }}" :multiple="true" />
                </div>

                <div class="col-md-6 mb-3">
                    <x-forms.tomSelect label="Parent Menu" id="parent_id" name="parent_id" :useEmoji="true"
                        emoji="📁" value="{{ $menu->parent_id ?? 'aktif' }}" :options="$parentMenus" />
                </div>

                <div class="col-md-6 mb-3">
                    <x-forms.input label="Order" name="order" id="order" type="number" :useEmoji="true"
                        emoji="🔢" value="{{ old('order', $menu->order ?? 0) }}" />
                    <small class="text-muted">Urutan tampil menu (0 = paling atas)</small>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="icon" class="mb-2">🎨 Icon</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">
                                <span>🎨</span>
                            </span>
                            <input type="text" class="form-control" id="icon" name="icon"
                                value="{{ old('icon', $menu->icon ?? '') }}" placeholder="fas fa-home">
                            <button type="button" class="btn btn-outline-secondary" id="icon-picker-btn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">Preview: </small>
                            <i id="icon-preview" class="{{ old('icon', $menu->icon ?? 'fas fa-home') }}"></i>
                        </div>
                        <small class="text-muted">Contoh: <code>fas fa-home</code>, <code>fas fa-users</code></small>

                        <!-- Icon Picker Dropdown -->
                        <div id="icon-picker" class="card mt-2"
                            style="display: none; max-height: 300px; overflow-y: auto;">
                            <div class="card-body">
                                <h6>Popular Icons:</h6>
                                <div class="row" id="popular-icons">
                                    <!-- Icons will be populated by JS -->
                                </div>
                                <hr>
                                <small class="text-muted">
                                    <strong>Examples:</strong><br>
                                    • <code>fas fa-home</code> - Solid home icon<br>
                                    • <code>far fa-circle</code> - Regular circle icon<br>
                                    • <code>fab fa-github</code> - Brand GitHub icon<br>
                                    <a href="https://fontawesome.com/icons" target="_blank">View all FontAwesome
                                        icons</a>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <x-forms.tomSelect label="Status" id="is_active" name="is_active" :useEmoji="true" emoji="📊"
                        value="{{ $menu->is_active ?? 'aktif' }}" :options="['aktif' => '✅ Aktif', 'inaktif' => '❌ Inaktif']" />
                </div>

                <!-- Badge System Information -->
                <div class="col-md-12">
                    <div class="alert alert-success alert-sm py-2">
                        <small>
                            <i class="fas fa-magic"></i> <strong>Badge Otomatis:</strong>
                            Badge akan muncul otomatis saat ada data baru hari ini pada menu yang sesuai
                            (Users, Roles, Permissions, Menus). Tidak perlu setting manual.
                        </small>
                    </div>
                </div>


            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Close</button>
            <x-button id="btnAction">Simpan</x-button>
        </div>
    </form>
</div>
