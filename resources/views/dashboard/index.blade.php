@extends('layouts.app')

@section('title', 'Dashboard')

@section('css')

    <!-- ModernTable CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.9/modern-table.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.9/responsive.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/modern-table-js@1.0.9/themes.css" rel="stylesheet">
    <style>
        .stats-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 20px;
        }

        .tech-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 bg-gradient-primary">
                    <div class="card-body py-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-2">Welcome to AdminLTE Laravel</h2>
                                <p class="mb-0 opacity-75">Modern Admin Dashboard with Laravel 12 & AdminLTE 4</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <i class="fas fa-tachometer-alt fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="feature-icon bg-primary text-white me-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 text-primary">{{ $stats['users'] ?? 0 }}</h3>
                                <small class="text-muted">Total Users</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="feature-icon bg-success text-white me-3">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 text-success">{{ $stats['roles'] ?? 0 }}</h3>
                                <small class="text-muted">Roles</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="feature-icon bg-warning text-white me-3">
                                <i class="fas fa-key"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 text-warning">{{ $stats['permissions'] ?? 0 }}</h3>
                                <small class="text-muted">Permissions</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="feature-icon bg-info text-white me-3">
                                <i class="fas fa-bars"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 text-info">{{ $stats['menus'] ?? 0 }}</h3>
                                <small class="text-muted">Menu Items</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Features -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-star text-warning me-2"></i>Key Features
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="feature-icon bg-primary text-white me-3 flex-shrink-0">
                                        <i class="fas fa-users-cog"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">User Management</h6>
                                        <small class="text-muted">CRUD operations, roles & permissions, avatar
                                            management</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="feature-icon bg-success text-white me-3 flex-shrink-0">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">RBAC System</h6>
                                        <small class="text-muted">Role-based access control with Spatie Permission</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="feature-icon bg-info text-white me-3 flex-shrink-0">
                                        <i class="fas fa-bars"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Dynamic Menu</h6>
                                        <small class="text-muted">Real-time sidebar with badges and permissions</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="feature-icon bg-warning text-white me-3 flex-shrink-0">
                                        <i class="fas fa-moon"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Dark Mode</h6>
                                        <small class="text-muted">Light/Dark/Auto with time-based switching</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="feature-icon bg-danger text-white me-3 flex-shrink-0">
                                        <i class="fas fa-images"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Media Library</h6>
                                        <small class="text-muted">File management with WebP conversion</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="feature-icon bg-secondary text-white me-3 flex-shrink-0">
                                        <i class="fas fa-cogs"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Settings</h6>
                                        <small class="text-muted">Application configuration management</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tech Stack -->
        <div class="row mb-4">
            <div class="col-lg-4 mb-3">
                <div class="card border-0 h-100">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-server text-primary me-2"></i>Backend Stack
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-primary tech-badge">Laravel 12</span>
                            <span class="badge bg-secondary tech-badge">PHP 8.2+</span>
                            <span class="badge bg-info tech-badge">MySQL</span>
                            <span class="badge bg-success tech-badge">Laravel Passport</span>
                            <span class="badge bg-warning tech-badge">Spatie Permission</span>
                            <span class="badge bg-danger tech-badge">Spatie MediaLibrary</span>
                            <span class="badge bg-dark tech-badge">Spatie Backup</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card border-0 h-100">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-palette text-success me-2"></i>Frontend Stack
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-primary tech-badge">AdminLTE 4</span>
                            <span class="badge bg-secondary tech-badge">Bootstrap 5</span>
                            <span class="badge bg-primary tech-badge">Modern-table-js</span>
                            <span class="badge bg-info tech-badge">Vanilla JS</span>
                            <span class="badge bg-success tech-badge">Vite</span>
                            <span class="badge bg-warning tech-badge">Bootstrap Icons</span>
                            <span class="badge bg-danger tech-badge">ES6 Modules</span>
                            <span class="badge bg-dark tech-badge">TomSelect</span>
                            <span class="badge bg-primary tech-badge">SweetAlert2</span>
                            <span class="badge bg-secondary tech-badge">Toastr</span>
                            <span class="badge bg-info tech-badge">Air Datepicker</span>
                            <span class="badge bg-success tech-badge">Flatpickr</span>
                            <span class="badge bg-warning tech-badge">Axios</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card border-0 h-100">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-tools text-warning me-2"></i>Development Tools
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-primary tech-badge">Laravel Pint</span>
                            <span class="badge bg-secondary tech-badge">PHPUnit</span>
                            <span class="badge bg-info tech-badge">PHPStan</span>
                            <span class="badge bg-success tech-badge">Rector</span>
                            <span class="badge bg-warning tech-badge">ESLint</span>
                            <span class="badge bg-danger tech-badge">Prettier</span>
                            <span class="badge bg-dark tech-badge">Concurrently</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bolt text-warning me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @can('read users')
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="{{ route('users.index') }}" class="btn btn-outline-primary w-100 py-3">
                                        <i class="fas fa-users mb-2 d-block"></i>
                                        Manage Users
                                    </a>
                                </div>
                            @endcan
                            @can('read roles')
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="{{ route('roles.index') }}" class="btn btn-outline-success w-100 py-3">
                                        <i class="fas fa-user-shield mb-2 d-block"></i>
                                        Manage Roles
                                    </a>
                                </div>
                            @endcan
                            @can('read menus')
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="{{ route('menus.index') }}" class="btn btn-outline-info w-100 py-3">
                                        <i class="fas fa-bars mb-2 d-block"></i>
                                        Manage Menus
                                    </a>
                                </div>
                            @endcan
                            @can('read settings')
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary w-100 py-3">
                                        <i class="fas fa-cogs mb-2 d-block"></i>
                                        Settings
                                    </a>
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="row">
            <div class="col-lg-6 mb-3">
                <div class="card border-0">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle text-info me-2"></i>System Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted d-block">Laravel Version</small>
                                <strong>{{ app()->version() }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">PHP Version</small>
                                <strong>{{ PHP_VERSION }}</strong>
                            </div>
                            <div class="col-6 mt-3">
                                <small class="text-muted d-block">Environment</small>
                                <strong class="text-{{ app()->environment() === 'production' ? 'success' : 'warning' }}">
                                    {{ strtoupper(app()->environment()) }}
                                </strong>
                            </div>
                            <div class="col-6 mt-3">
                                <small class="text-muted d-block">Debug Mode</small>
                                <strong class="text-{{ config('app.debug') ? 'warning' : 'success' }}">
                                    {{ config('app.debug') ? 'ON' : 'OFF' }}
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="card border-0">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line text-success me-2"></i>Recent Activity
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-success rounded-circle me-3" style="width: 8px; height: 8px;"></div>
                            <small class="text-muted">System running smoothly</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-info rounded-circle me-3" style="width: 8px; height: 8px;"></div>
                            <small class="text-muted">{{ $stats['users'] ?? 0 }} users registered</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-warning rounded-circle me-3" style="width: 8px; height: 8px;"></div>
                            <small class="text-muted">{{ $stats['roles'] ?? 0 }} roles configured</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle me-3" style="width: 8px; height: 8px;"></div>
                            <small class="text-muted">{{ $stats['permissions'] ?? 0 }} permissions active</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Table Demo with External API -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-shopping-cart text-primary me-2"></i>Modern Table Demo - External API
            </h5>
            <div class="card-tools">
                <span class="badge bg-info">DummyJSON API</span>
            </div>
        </div>
        <div class="card-body">
            <div id="loading">Loding</div>
            <table id="table-users" class="table table-striped table-hover"></table>
        </div>
    </div>
@endsection

@section('js')

    <!-- Modern Table Demo with DummyJSON API -->
    <script type="module">
        import {
            ModernTable
        } from "https://cdn.jsdelivr.net/npm/modern-table-js@1.0.9/core/ModernTable.js";

        // Setup event listeners BEFORE table initialization
        const table = new ModernTable('#table-users', {
            api: {
                url: '/api/users',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('token'),
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },

                // Before request is sent (like beforeSend)
                beforeSend: function(params) {
                    document.getElementById('loading').style.display = 'block';
                    // Return false to abort request
                },

                // On successful response (like success)
                success: function(data, textStatus, response) {
                    console.log('Request successful:', data);
                },

                // On error (like error)
                error: function(error, textStatus, errorThrown) {
                    console.error('Request failed:', error);
                    alert('Failed to load data');
                    // Return fallback data to prevent table error
                    return {
                        data: [],
                        recordsTotal: 0,
                        recordsFiltered: 0
                    };
                },

                // Always runs (like complete)
                complete: function() {
                    document.getElementById('loading').style.display = 'none';
                    console.log('Request completed');
                },

                // Legacy support
                beforeRequest: function(config) {
                    // Modify request config
                    return config;
                }
            },
            columns: [{
                    data: "DT_RowIndex",
                    title: "No",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'avatar_url',
                    title: 'Avatar',
                    render: (data) =>
                        `<img src="${data}" alt="Avatar" class="rounded-circle" width="40" height="40">`
                },
                {
                    data: 'name',
                    title: 'Name'
                },
                {
                    data: 'email',
                    title: 'Email'
                },
                {
                    data: "actions",
                    title: "Action",
                    className: "text-center",
                    orderable: false,
                    searchable: false,
                    render: (_, __, row) => `
                        <button class="btn btn-sm btn-primary me-1" onclick="editUser(${row.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteUser(${row.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    `
                },
            ],
            buttons: [

                {
                    text: 'Create',
                    className: "btn btn-primary btn-sm btn-create",
                    enabled: false,
                    attr: {
                        id: 'btn-create',
                    },
                    action: function(e, dt, node, config) {
                        alert('Create New Data');
                    }
                },
                {
                    text: 'Delete Bulk (<span class="selected-count">0</span>)',
                    className: "btn btn-danger btn-sm btn-delete",
                    enabled: false,
                    attr: {
                        id: 'btn-bulk-delete',
                        style: "display: block;",
                    },
                    action: function(e, dt, node, config) {
                        const selectedRows = dt.getSelectedRows();
                        if (selectedRows.length > 0) {
                            alert(`Bulk delete ${selectedRows.length} selected users`);
                        } else {
                            alert('No users selected for deletion');
                        }
                    }
                },
                'copy', 'csv', 'excel',
                {
                    extend: "pdf",
                    text: "PDF",
                    className: "btn btn-danger btn-sm btn-pdf",
                    filename: 'Users',
                    orientation: "landscape",
                    pageSize: "A4",
                    exportColumns: ['avatar_url', 'name', 'email'],
                    titleAttr: "Export data as PDF file",
                },
                {
                    extend: "print",
                    text: "Print",
                    className: "btn btn-warning btn-sm btn-print",
                    orientation: "portrait",
                    exportColumns: ['avatar_url', 'name', 'email'],
                    titleAttr: "Print selected columns with custom styling",
                },
                "colvis"
            ],

            serverSide: true,

            // Features
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            order: [
                [2, "desc"]
            ], // name column
            ordering: true,
            searching: true,
            columnSearch: true,
            paging: true,
            select: true,
            responsive: true,

            // UX
            theme: "auto",
            keyboard: true,
            accessibility: true,

            // State
            stateSave: true,
            stateDuration: 3600,

            filters: [{
                    column: 'date',
                    type: 'date',
                    label: 'Registration Date',
                    placeholder: 'Select date'
                },
                {
                    column: 'start_date',
                    type: 'date',
                    label: 'From Date',
                    placeholder: 'Start date'
                },
                {
                    column: 'end_date',
                    type: 'date',
                    label: 'To Date',
                    placeholder: 'End date'
                },
                {
                    column: 'year',
                    type: 'select',
                    label: 'Year',
                    options: [{
                            value: '',
                            text: 'All Years'
                        },
                        {
                            value: '2024',
                            text: '2024'
                        },
                        {
                            value: '2023',
                            text: '2023'
                        },
                        {
                            value: '2022',
                            text: '2022'
                        }
                    ]
                },
                {
                    type: 'clear',
                    label: 'Clear',
                    className: 'btn btn-outline-secondary btn-sm'
                }
            ],

            // Called after table initialization
            initComplete: function(data, meta) {
                console.log('Table initialized with:', data.length, 'rows');
            },

            // Called BEFORE every table draw/redraw
            preDrawCallback: function(settings) {
                console.log('About to render:', settings.data.length, 'rows');
                // Show loading, validate data, preprocessing
                // Return false to cancel rendering
                return true;
            },

            // Called after every table draw/redraw
            drawCallback: function(settings) {
                console.log('Table drawn with:', settings.data.length, 'rows');
                // Re-bind events, apply styling, initialize tooltips, etc.
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                    new bootstrap.Tooltip(el);
                });
            },

            // Called when row DOM element is created
            createdRow: function(row, data, dataIndex) {
                // Add data attributes, CSS classes, event listeners
                row.setAttribute('data-user-id', data.id);
                if (data.role === 'admin') {
                    row.classList.add('admin-row');
                }
            },

            // Called for each row during rendering
            rowCallback: function(row, data, index) {
                // Apply conditional styling, modify row content
                if (data.status === 'inactive') {
                    row.classList.add('table-warning');
                }
            },

            // Called to manipulate header after each draw
            headerCallback: function(thead, data, start, end, display) {
                // Update header with dynamic info
                const nameHeader = thead.querySelector('th[data-column="1"]');
                if (nameHeader) {
                    const activeCount = data.filter(user => user.status === 'active').length;
                    nameHeader.title = `${activeCount} active users in current page`;
                }
            },

            // Called to manipulate footer after each draw
            footerCallback: function(row, data, start, end, display) {
                if (row) {
                    const total = data.length;
                    const active = data.filter(item => item.status === 'active').length;
                    row.innerHTML = `
                <tr>
                    <th colspan="3">Summary:</th>
                    <th>Active: ${active}</th>
                    <th>Total: ${total}</th>
                    <th colspan="2"></th>
                </tr>
            `;
                }
            },

            // Called to generate custom info text
            infoCallback: function(settings, start, end, max, total, pre) {
                const percentage = total > 0 ? Math.round((total / max) * 100) : 0;
                return `
            <div class="d-flex justify-content-between">
                <span>Menampilkan ${start} sampai ${end} dari ${total} data</span>
                <span class="badge bg-info">${percentage}% data ditampilkan</span>
            </div>
        `;
            },

            // Custom state loading (override built-in)
            // stateLoadCallback: function(settings) {
            //     const state = JSON.parse(localStorage.getItem('customTableState'));
            //     if (state) {
            //         // Example: Always reset page to 1 (exclude paging from state)
            //         state.page = 1;
            //         return state;
            //     }
            //     return null;
            // },

            // Custom state saving (override built-in)
            stateSaveCallback: function(settings, data) {
                // Add custom metadata
                const enhancedState = {
                    ...data,
                    timestamp: new Date().toISOString(),
                    userAgent: navigator.userAgent
                };
                localStorage.setItem('customTableState', JSON.stringify(enhancedState));
            },

            // Row click handler
            onRowClick: function(rowData, index, event) {
                console.log('Row clicked:', rowData);
            },

            // Selection change handler
            onSelectionChange: function(selectedRows) {
                console.log('Selection changed:', selectedRows.length, 'rows');
            },

            // Error handler
            onError: function(error) {
                console.error('Table error:', error);
            }
        });



        // Events - Setup AFTER table creation but BEFORE any data loading
        table.on('initComplete', function(data, meta) {
            console.log('🎉 initComplete event fired:', data);
        });

        table.on('selectionChange', function(selectedRows) {
            console.log('🔄 selectionChange event fired:', selectedRows);
        });

        table.on('error', function(error) {
            console.log('❌ error event fired:', error);
        });

        // Action handlers
        window.editUser = function(id) {
            alert(`Edit user with ID: ${id}`);
        };

        window.deleteUser = function(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                alert(`Delete user with ID: ${id}`);
            }
        };
    </script>
@endsection
