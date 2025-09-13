@extends('layouts.app')

@section('title', 'permissions')

@section('css')
    <!-- Option 2: Standalone (Zero Dependencies) -->
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.5/modern-table.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.5/responsive.css" rel="stylesheet">
@endsection

@section('content')

    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">🔐 Data Permissions</h3>
            </div>
            <div class="card-body">
                <!-- EasyDataTable - Super Simple! -->
                <table id="table-permissions" class="table table-striped table-hover table-bordered table-sm">
                    <!-- Dynamic headers and content will be loaded here -->
                </table>
            </div>
        </div>
    </div>

    @include('components.modal.Modal')

    <div class="modal" id="createTomselectModal" tabindex="-1">
        <div class="modal-dialog">

        </div>
    </div>


@endsection

@section('js')

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        // Test API endpoint
        axios.get('/api/roles', {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    )?.content,
                },
            })
            .then(function(response) {
                console.log('✅ API Response:', response);
            })
            .catch(function(error) {
                console.error('❌ API Error:', error);
            });
    </script>

    {{-- <script type="module">
        import axiosClient from "/js/components/apiService/axiosClient.js";
        import {
            ModernTable
        } from "https://cdn.jsdelivr.net/npm/modern-table-js@1.0.6/core/ModernTable.js";


        axiosClient.get('api/users');
        console.log(`Bearer ${localStorage.getItem("token")}`);





        const table = new ModernTable('#table-permissions', {
            // Data source (simple)
            // api: '/api/permissions',

            // Data source (with auth and callbacks)
            api: {
                url: '/api/permissions',
                method: 'GET',
                timeout: 30000,
                // headers: {
                //     'Authorization': 'Bearer YOUR_TOKEN',
                //     'Content-Type': 'application/json'
                // },
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    )?.content
                },
                beforeSend: function(params) {
                    // Show loading, modify params, etc.
                },
                success: function(data, status, response) {
                    // Handle successful response
                },
                error: function(error, status, message) {
                    console.log('message', message);

                    // Handle errors
                },
                complete: function() {
                    // Always runs (cleanup, hide loading, etc.)
                }
            },

            // Columns configuration
            columns: [{
                    data: 'name',
                    title: 'Name',
                    orderable: true
                },
                {
                    data: 'email',
                    title: 'Email'
                },
                {
                    data: 'status',
                    title: 'Status',
                    render: (data) => `<span class="badge">${data}</span>`
                }
            ],

            // Features
            paging: true,
            pageLength: 10,
            searching: true,
            columnSearch: false, // Individual column search
            ordering: true,
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
    </script> --}}

@endsection
