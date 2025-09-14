@extends('layouts.app')

@section('title', 'Dashboard')

@section('css')
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
@endsection

@section('js')
@endsection
