@extends('layouts.app')

@section('title', 'My Files')

@php
    // Check if user has filemanager read permission
    if (!auth()->user()->can('read filemanager')) {
        abort(403, 'You do not have permission to access file manager');
    }
@endphp

@section('content-header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">My Files</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">My Files</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-folder-fill"></i>
                            Personal File Manager
                        </h3>
                        <div class="card-tools">
                            <span class="badge bg-success">Personal Space</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <!-- Tabs -->
                        <ul class="nav nav-tabs" id="fileManagerTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="images-tab" data-bs-toggle="tab" data-bs-target="#images" type="button" role="tab">
                                    <i class="bi bi-images"></i> My Images
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="files-tab" data-bs-toggle="tab" data-bs-target="#files" type="button" role="tab">
                                    <i class="bi bi-files"></i> My Files
                                </button>
                            </li>
                        </ul>
                        
                        <!-- Tab Content -->
                        <div class="tab-content" id="fileManagerTabContent">
                            <div class="tab-pane fade show active" id="images" role="tabpanel">
                                <div style="height: 550px;">
                                    <iframe src="/user-filemanager?type=image" style="width: 100%; height: 100%; border: none;"
                                        id="lfm-iframe-images">
                                    </iframe>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="files" role="tabpanel">
                                <div style="height: 550px;">
                                    <iframe src="/user-filemanager?type=file" style="width: 100%; height: 100%; border: none;"
                                        id="lfm-iframe-files">
                                    </iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">
                            <i class="bi bi-folder-fill"></i>
                            <strong>Personal Workspace:</strong> Upload, organize and manage your personal files. Each user has their own private space.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        #lfm-iframe-images, #lfm-iframe-files {
            border: none;
        }
        
        .nav-tabs .nav-link {
            border-bottom: 1px solid #dee2e6;
        }
        
        .nav-tabs .nav-link.active {
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
        }
        
        .tab-content {
            border-left: 1px solid #dee2e6;
            border-right: 1px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
        }
    </style>
@endsection