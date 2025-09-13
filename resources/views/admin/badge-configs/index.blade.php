@extends('layouts.app')

@section('title', 'badge-configs')

@section('css')
    <!-- Option 2: Standalone (Zero Dependencies) -->
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.5/modern-table.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.5/responsive.css" rel="stylesheet">
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Info Alert -->
        <div class="alert alert-info mb-3">
            <h6 class="alert-heading"><i class="fas fa-info-circle"></i> 🏷️ Badge Configuration</h6>
            <small>
                Kelola badge otomatis untuk menu sidebar. Badge akan muncul saat ada data baru hari ini.
                <br><strong>Contoh:</strong> Menu "Users" akan menampilkan badge jika ada user baru yang dibuat hari ini.
            </small>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">🏷️ Data Badge Configuration</h3>
            </div>
            <div class="card-body">
                <!-- EasyDataTable - Super Simple! -->
                <table id="table-badge-configs" class="table table-striped table-hover table-bordered table-sm">
                    <!-- Dynamic headers and content will be loaded here -->
                </table>
            </div>
        </div>
    </div>

    @include('components.modal.Modal')
@endsection

@section('js')
@endsection
