@extends('layouts.app')

@section('title', 'menus')

@section('css')
    <!-- ModernTable CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.9/modern-table.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.9/responsive.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.9/themes.css" rel="stylesheet">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header gradient-card">
                <h2 class="text-white">🔗 Menu Management</h2>
            </div>
            <div class="card-body">
                <!-- EasyDataTable - Super Simple! -->
                <table id="table-menus" class="table table-striped table-hover table-bordered table-sm">
                    <!-- Dynamic headers and content will be loaded here -->
                </table>
            </div>
        </div>



        @include('components.modal.Modal')
    </div>
@endsection

@section('js')
    <script>
        // Define permissions globally - available for menus.js
        // window.permissions = {
        //     canDelete: @json(dynamiccan('MenuController', 'delete')),
        //     canCreate: @json(dynamiccan('MenuController', 'create')),
        //     canEdit: @json(dynamiccan('MenuController', 'edit')),
        //     canView: @json(dynamiccan('MenuController', 'read'))
        // };
    </script>
@endsection
