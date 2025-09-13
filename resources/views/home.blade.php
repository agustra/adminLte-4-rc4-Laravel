@extends('layouts.app')

@section('title', 'Home')

@section('css')

    <!-- Option 2: Standalone (Zero Dependencies) -->
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.5/modern-table.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.5/responsive.css" rel="stylesheet">

@endsection


@section('content')

    <div class="container-fluid">

        <div class="card">
            <div class="card-header">
                <h5>Users Table</h5>
            </div>
            <div class="card-body">
                <table id="usersTable" class="table table-striped">

                </table>
            </div>
        </div>


    </div>




@section('js')
    @vite(['resources/js/home.js'])

@endsection

@endsection
