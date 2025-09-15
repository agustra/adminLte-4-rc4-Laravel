@extends('layouts.app')

@section('title', 'Admin File Manager')

@php
    // Redirect users without filemanager menu permission
    if (!auth()->user()->can('menu filemanager')) {
        redirect()->route('user.filemanager.index')->send();
    }
@endphp

@section('content-header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Admin File Manager</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Admin File Manager</li>
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
                            <i class="bi bi-files"></i>
                            Admin File Manager
                        </h3>
                        <div class="card-tools">
                            <span class="badge bg-danger">Admin Access</span>
                            <span class="badge bg-info">All Files</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <!-- Admin Tabs -->
                        <ul class="nav nav-tabs" id="adminFileManagerTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="system-images-tab" data-bs-toggle="tab" data-bs-target="#system-images" type="button" role="tab">
                                    <i class="bi bi-gear-fill"></i> System Images
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="system-files-tab" data-bs-toggle="tab" data-bs-target="#system-files" type="button" role="tab">
                                    <i class="bi bi-file-earmark-text"></i> System Files
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="user-monitor-tab" data-bs-toggle="tab" data-bs-target="#user-monitor" type="button" role="tab">
                                    <i class="bi bi-people-fill"></i> User Monitoring
                                </button>
                            </li>
                        </ul>
                        
                        <!-- Admin Tab Content -->
                        <div class="tab-content" id="adminFileManagerTabContent">
                            <div class="tab-pane fade show active" id="system-images" role="tabpanel">
                                <div style="height: 550px;">
                                    <iframe src="/admin/system-filemanager?type=image" style="width: 100%; height: 100%; border: none;"
                                        id="system-lfm-iframe-images">
                                    </iframe>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="system-files" role="tabpanel">
                                <div style="height: 550px;">
                                    <iframe src="/admin/system-filemanager?type=file" style="width: 100%; height: 100%; border: none;"
                                        id="system-lfm-iframe-files">
                                    </iframe>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="user-monitor" role="tabpanel">
                                <!-- Sub-tabs for User Monitoring -->
                                <ul class="nav nav-pills nav-fill bg-light p-2" id="userMonitorTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="user-images-tab" data-bs-toggle="tab" data-bs-target="#user-images" type="button" role="tab">
                                            <i class="bi bi-images"></i> User Images
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="user-files-tab" data-bs-toggle="tab" data-bs-target="#user-files" type="button" role="tab">
                                            <i class="bi bi-files"></i> User Files
                                        </button>
                                    </li>
                                </ul>
                                
                                <!-- User Monitor Tab Content -->
                                <div class="tab-content" id="userMonitorTabContent">
                                    <div class="tab-pane fade show active" id="user-images" role="tabpanel">
                                        <div style="height: 500px;">
                                            <iframe src="/admin/filemanager?type=image" class="user-monitor-iframe" style="width: 100%; height: 100%; border: none;"
                                                id="monitor-lfm-iframe-images" onload="hidePublicFolder(this)">
                                            </iframe>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="user-files" role="tabpanel">
                                        <div style="height: 500px;">
                                            <iframe src="/admin/filemanager?type=file" class="user-monitor-iframe" style="width: 100%; height: 100%; border: none;"
                                                id="monitor-lfm-iframe-files" onload="hidePublicFolder(this)">
                                            </iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">
                            <i class="bi bi-shield-check"></i>
                            <strong>System Files:</strong> Logo, banners, templates | <strong>User Monitoring:</strong> All user folders with clear names
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        #system-lfm-iframe-images, #system-lfm-iframe-files, 
        #monitor-lfm-iframe-images, #monitor-lfm-iframe-files {
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
        
        /* Nested tabs styling */
        #userMonitorTabs .nav-link {
            border-radius: 0.375rem;
            margin: 0 2px;
        }
        
        #userMonitorTabs .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }
        
        /* Hide public folder in user monitoring */
        .user-monitor-iframe {
            border: none;
        }
    </style>
@endsection

@section('js')
    <script>
        function hidePublicFolder(iframe) {
            try {
                // Wait for iframe to load completely
                setTimeout(function() {
                    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                    
                    // Hide public folder in tree view
                    const treeItems = iframeDoc.querySelectorAll('#tree a');
                    treeItems.forEach(function(item) {
                        if (item.textContent.trim() === 'public') {
                            item.style.display = 'none';
                        }
                    });
                    
                    // Hide public folder in main content
                    const contentItems = iframeDoc.querySelectorAll('#content a');
                    contentItems.forEach(function(item) {
                        const nameDiv = item.querySelector('.item_name');
                        if (nameDiv && nameDiv.textContent.trim() === 'public') {
                            item.style.display = 'none';
                        }
                    });
                    
                    // Re-run after any navigation
                    const observer = new MutationObserver(function() {
                        setTimeout(function() {
                            hidePublicFolder(iframe);
                        }, 100);
                    });
                    
                    const targetNode = iframeDoc.querySelector('#content');
                    if (targetNode) {
                        observer.observe(targetNode, { childList: true, subtree: true });
                    }
                }, 1000);
            } catch (e) {
                // Cross-origin or other errors - ignore
                console.log('Cannot access iframe content:', e);
            }
        }
    </script>
@endsection
