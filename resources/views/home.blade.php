@extends('layouts.app')

@section('title', 'Home')

@section('css')

    <!-- ModernTable CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.9/modern-table.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.9/responsive.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.9/themes.css" rel="stylesheet">

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
