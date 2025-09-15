@extends('layouts.app')

@section('title', 'My Profile')

@section('content-header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">My Profile</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Profile Info -->
            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img class="profile-user-img img-fluid img-circle"
                                src="{{ $user->avatar_url }}"
                                alt="User profile picture" id="profile-avatar"
                                onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&color=ffffff&background=6c757d&size=150'">
                        </div>
                        <h3 class="profile-username text-center">{{ $user->name }}</h3>
                        <p class="text-muted text-center">{{ $user->email }}</p>

                        @if ($user->bio)
                            <p class="text-center">{{ $user->bio }}</p>
                        @endif

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Phone</b> <span class="float-right">{{ $user->phone ?? 'Not set' }}</span>
                            </li>
                            <li class="list-group-item">
                                <b>Member Since</b> <span class="float-right">{{ $user->created_at->format('M Y') }}</span>
                            </li>
                        </ul>

                        <button type="button" class="btn btn-primary btn-block" onclick="openFileManager()">
                            <i class="bi bi-camera"></i> Change Avatar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Profile</h3>
                    </div>
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="bio">Bio</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="3">{{ old('bio', $user->bio) }}</textarea>
                                @error('bio')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <input type="hidden" name="profile_photo_path" id="profile_photo_path"
                                value="{{ $user->profile_photo_path }}">
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .profile-user-img {
            width: 100px;
            height: 100px;
            border: 3px solid #adb5bd;
            margin: 0 auto;
            padding: 3px;
        }

        .img-circle {
            border-radius: 50%;
        }
    </style>
@endsection

@section('js')
    <script>
        function openFileManager() {
            // Set global callback before opening popup
            window.SetUrl = function(files) {
                console.log('SetUrl called with:', files);
                
                // Handle array of files or single file
                let fileUrl;
                if (Array.isArray(files) && files.length > 0) {
                    fileUrl = files[0].url; // Get URL from first file object
                } else if (typeof files === 'string') {
                    fileUrl = files; // Direct URL string
                } else if (files && files.url) {
                    fileUrl = files.url; // Single file object
                } else {
                    console.error('Invalid file data:', files);
                    return;
                }
                
                console.log('Extracted URL:', fileUrl);
                
                // Clean the URL (remove domain if it's local storage)
                const cleanUrl = fileUrl.replace(window.location.origin, '');

                // Update hidden input
                document.getElementById('profile_photo_path').value = cleanUrl;

                // Update avatar preview with full URL
                document.getElementById('profile-avatar').src = fileUrl;
                
                console.log('Avatar updated with:', fileUrl);
            };
            
            // Open FileManager popup
            const popup = window.open('/user-filemanager?type=image', 'FileManager',
                'width=1000,height=700,scrollbars=yes,resizable=yes');
                
            // Focus on popup
            if (popup) {
                popup.focus();
            }
        }
    </script>
@endsection
