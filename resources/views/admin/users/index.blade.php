@extends('layouts.app')

@section('title', 'users') <!-- users disini berhubungan dengan cara js diload -->

@section('css')
    <!-- ModernTable CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.9/modern-table.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.9/responsive.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.9/themes.css" rel="stylesheet">

    <!-- Cropper CSS -->
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

{{-- @section('js')
    <!-- JavaScript -->
    <script>
        console.log(localStorage.getItem('token'));
    </script>
    <script type="module">
        import {
            ModernTable
        } from "https://cdn.jsdelivr.net/npm/modern-table-js@1.0.6/core/ModernTable.js";
        // Inject permissions ke JavaScript
        const table = new ModernTable('#table-users', {
            // Data source (with auth and callbacks)

            urlApi: {
                url: '/api/users',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('token'),
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                beforeSend: function(params) {
                    console.log('test', params);

                    // Show loading, modify params, etc.
                },
                success: function(data, status, response) {
                    console.log('response', response);

                    // Handle successful response
                },
                error: function(error, status, message) {
                    // Handle errors
                },
                complete: function() {
                    // Always runs (cleanup, hide loading, etc.)
                }
            },
            columns: [{
                    data: 'name',
                    title: 'Name'
                },
                {
                    data: 'email',
                    title: 'Email'
                },
            ],
            // Features
            paging: true,
            pageLength: 10,
            searching: true,
            columnSearch: false, // Individual column search
            ordering: false,
            select: true,
            responsive: true,

            // UI
            theme: 'auto', // 'light', 'dark', 'auto'
            buttons: ['copy', 'csv', 'excel', 'pdf'],

            // Advanced
            stateSave: true,
            keyboard: true,
            accessibility: true
        });
    </script>
@endsection --}}
