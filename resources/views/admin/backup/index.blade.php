@extends('layouts.app')

@section('title', 'backup')

@section('css')
    <!-- Option 2: Standalone (Zero Dependencies) -->
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.5/modern-table.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.5/responsive.css" rel="stylesheet">
@endsection

@section('content')
    <div class="container-fluid">
        <h4 class="title-content">Manajemen Backup</h4>

        <!-- Tab Navigation -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary backup-tab" data-tab="local">
                        <i class="fas fa-server"></i> Local Storage
                        <span class="badge bg-primary ms-1" id="local-count">0</span>
                    </button>
                    <button class="btn btn-outline-primary backup-tab" data-tab="google">
                        <i class="fab fa-google-drive"></i> Google Drive
                        <span class="badge bg-primary ms-1" id="google-count">0</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header gradient-card">
                        <h2 class="text-white">🔄 Manajemen Backup</h2>
                    </div>
                    <div class="card-body p-4">
                        <div class="tab-content">
                            <!-- Local Storage Tab -->
                            <div class="tab-pane" id="local-tab">
                                <table id="localTable" class="table table-hover">
                                    <!-- Dynamic content will be loaded here -->
                                </table>
                            </div>

                            <!-- Google Drive Tab -->
                            <div class="tab-pane" id="google-tab">
                                <table id="googleTable" class="table table-hover">
                                    <!-- Dynamic content will be loaded here -->
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('components.modal.Modal')
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    {{-- <script>
        // Test API endpoints
        console.log('🧪 Testing backup endpoints...');

        // Test actual counts endpoint
        axios.get('/api/backup-counts', {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    )?.content,
                },
            })
            .then(function(response) {
                console.log('✅ Counts API Response:', response.data);
            })
            .catch(function(error) {
                console.error('❌ Counts API Error:', error);
            });
    </script> --}}
@endsection
