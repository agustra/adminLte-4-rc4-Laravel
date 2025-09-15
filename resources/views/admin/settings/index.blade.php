@extends('layouts.app')

@section('title', 'settings')

@section('css')
    <style>
        /* Scoped CSS untuk settings page saja */
        #settingsForm .spin {
            animation: settings-spin 1s linear infinite;
        }

        @keyframes settings-spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        #settingsForm .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
    </style>
@endsection

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Application Settings</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Application Configuration</h3>
                        </div>
                        <form id="settingsForm" method="POST" action="{{ route('settings.store') }}">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <!-- App Name -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="app_name" class="form-label">Application Name</label>
                                            <input type="text" class="form-control" id="app_name" name="app_name"
                                                value="{{ config('settings.app_name', config('app.name')) }}" required>
                                        </div>
                                    </div>

                                    <!-- App Description -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="app_description" class="form-label">Description</label>
                                            <input type="text" class="form-control" id="app_description"
                                                name="app_description" value="{{ config('settings.app_description') }}">
                                        </div>
                                    </div>

                                    <!-- App Logo -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="app_logo" class="form-label">Application Logo</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="app_logo" name="app_logo"
                                                    value="{{ config('settings.app_logo') }}" readonly>
                                                <button type="button" class="btn btn-outline-secondary"
                                                    onclick="openFileManager('app_logo')">
                                                    <i class="bi bi-image"></i> Choose Logo
                                                </button>
                                            </div>
                                            <div id="app_logo_preview" class="mt-2">
                                                @if (config('settings.app_logo'))
                                                    <img src="{{ asset('storage/' . config('settings.app_logo')) }}"
                                                        alt="App Logo" class="img-thumbnail" style="max-height: 100px;">
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- App Version -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="app_version" class="form-label">Version</label>
                                            <input type="text" class="form-control" id="app_version" name="app_version"
                                                value="{{ config('settings.app_version', '1.0.0') }}">
                                        </div>
                                    </div>

                                    <!-- Company Name -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="company_name" class="form-label">Company Name</label>
                                            <input type="text" class="form-control" id="company_name" name="company_name"
                                                value="{{ config('settings.company_name') }}">
                                        </div>
                                    </div>

                                    <!-- Company Address -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="company_address" class="form-label">Company Address</label>
                                            <textarea class="form-control" id="company_address" name="company_address" rows="3">{{ config('settings.company_address') }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Contact Email -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contact_email" class="form-label">Contact Email</label>
                                            <input type="email" class="form-control" id="contact_email"
                                                name="contact_email" value="{{ config('settings.contact_email') }}">
                                        </div>
                                    </div>

                                    <!-- Contact Phone -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contact_phone" class="form-label">Contact Phone</label>
                                            <input type="text" class="form-control" id="contact_phone"
                                                name="contact_phone" value="{{ config('settings.contact_phone') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg"></i> Save Settings
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection


