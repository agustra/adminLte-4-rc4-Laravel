@extends('layouts.app')

@section('title', 'roles') <!-- roles disini berhubungan dengan cara js diload -->

@section('css')
    <!-- Option 2: Standalone (Zero Dependencies) -->
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.5/modern-table.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.5/responsive.css" rel="stylesheet">

@endsection

@section('content')

    <div class="container-fluid">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">💰 Data Roles</h3>
            </div>
            <div class="card-body">
                <!-- EasyDataTable - Super Simple! -->
                <table id="table-roles" class="table table-striped table-hover table-bordered table-sm">
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
        axios.get(`${window.APP_CONFIG?.apiUrl || '/api'}/roles`, {
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



@endsection
