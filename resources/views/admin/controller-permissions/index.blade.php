@extends('layouts.app')

@section('title', 'controller-permissions')

@section('css')
    <!-- Option 2: Standalone (Zero Dependencies) -->
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.5/modern-table.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.5/responsive.css" rel="stylesheet">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header gradient-card d-flex justify-content-between align-items-center">
                <h2 class="text-white mb-0">🔐 Dynamic Permission Management</h2>
                <div>
                    {{-- Tombol dikelola oleh JavaScript EasyDataTable --}}
                    {{-- Contoh penggunaan @dynamiccan: --}}
                    {{-- @dynamiccan('ControllerPermissionController', 'create') --}}
                        {{-- <button type="button" class="btn btn-light btn-sm" id="btnTambah"> --}}
                            {{-- <i class="fas fa-plus"></i> Tambah Mapping --}}
                        {{-- </button> --}}
                    {{-- @enddynamiccan --}}
                </div>
            </div>
            <div class="card-body">
                <!-- Action Buttons -->
                <div class="mb-3">
                    {{-- Tombol bulk delete dikelola oleh JavaScript EasyDataTable --}}
                    {{-- Contoh penggunaan @dynamiccan untuk bulk operations: --}}
                    {{-- @dynamiccan('ControllerPermissionController', 'destroy') --}}
                        {{-- <button type="button" class="btn btn-danger btn-sm" id="btnDeleteSelected" style="display: none;"> --}}
                            {{-- <i class="fas fa-trash"></i> Hapus Terpilih --}}
                        {{-- </button> --}}
                    {{-- @enddynamiccan --}}
                </div>

                <table id="table-controller-permissions" class="table table-striped table-hover table-bordered table-sm">
                    <!-- Dynamic headers and content will be loaded here -->
                </table>
            </div>
        </div>
    </div>

    @include('components.modal.Modal')
@endsection

@section('js')
    <script>
        // Inject permissions ke JavaScript
        window.meta = window.meta || {};
        window.meta.permissions = @json($controllerPermissions ?? []);
    </script>
@endsection
