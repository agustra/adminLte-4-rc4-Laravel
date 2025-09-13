@extends('layouts.app')

@section('title', 'users')

@section('css')
    <!-- Option 2: Standalone (Zero Dependencies) -->
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.5/modern-table.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.5/responsive.css" rel="stylesheet">

    <!-- Cropper CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">

    @include('media.partials.styles')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header gradient-card d-flex justify-content-between align-items-center">
                <h2 class="text-white mb-0">👥 Data Users</h2>
            </div>
            <div class="card-body">

                <table id="table-users" class="table table-striped table-hover table-bordered table-sm">
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
