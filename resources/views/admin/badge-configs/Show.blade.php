<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">🏷️ Detail Badge Configuration</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">❌</button>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="fw-bold">🔗 Menu URL:</label>
                    <p class="form-control-plaintext">{{ $config->menu_url }}</p>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="fw-bold">📦 Model Class:</label>
                    <p class="form-control-plaintext"><code>{{ $config->model_class }}</code></p>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="fw-bold">📅 Date Field:</label>
                    <p class="form-control-plaintext"><code>{{ $config->date_field }}</code></p>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="fw-bold">✅ Status:</label>
                    <p class="form-control-plaintext">
                        <span class="badge {{ $config->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $config->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </p>
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="form-group mb-3">
                    <label class="fw-bold">📝 Description:</label>
                    <p class="form-control-plaintext">{{ $config->description ?: '-' }}</p>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="fw-bold">📅 Created At:</label>
                    <p class="form-control-plaintext">{{ $config->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="fw-bold">🔄 Updated At:</label>
                    <p class="form-control-plaintext">{{ $config->updated_at->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Close</button>
    </div>
</div>