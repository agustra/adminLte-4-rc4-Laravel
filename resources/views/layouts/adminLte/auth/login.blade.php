<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name')) | Log in</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // AdminLTE 4 Native Theme Initialization
        (function() {
            const savedMode = localStorage.getItem('themeMode') || 'system';
            let actualTheme;

            if (savedMode === 'system') {
                const hour = new Date().getHours();
                actualTheme = (hour >= 18 || hour < 6) ? 'dark' : 'light';
            } else {
                actualTheme = savedMode;
            }

            document.documentElement.setAttribute('data-bs-theme', actualTheme);
        })();
    </script>

    @include('layouts.adminLte._head')

</head>

<style>
    img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
    }
</style>

<body class="hold-transition login-page">

    <div class="img-circle elevation-2">
        <a href="javascript:void(0)">
            <img src="{{ url('/media/' . config('settings.app_logo')) }}" alt="logo.png" width="150">
        </a>
    </div>

    <div class="login-box">
        <div class="login-logo">
            @yield('title', config('app.name'))
        </div>
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>

                <form action="{{ url('admin/login') }}" id="formLogin" method="post" autocomplete="off"
                    class="formLogin needs-validation" novalidate>
                    @csrf
                    <!-- Email Address -->
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" id="email" name="email" :value="old('email')"
                            required autofocus placeholder="Email">
                        @if ($errors->has('email'))
                            <div class="invalid-feedback">
                                {{ $errors->first('email') }}
                            </div>
                        @endif
                        <span class="help-block with-errors"></span>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" id="password" name="password" required
                            autocomplete="current-password" placeholder="Password">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @if ($errors->has('password'))
                            <div class="invalid-feedback">
                                {{ $errors->first('password') }}
                            </div>
                        @endif
                        <span class="help-block with-errors"></span>
                    </div>

                    <!-- Remember Me -->
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember_me" name="remember">
                                <label for="remember_me">
                                    Remember Me
                                </label>
                            </div>
                        </div>

                        <!-- Tombol login -->
                        <div class="col-4">
                            <button class="btn btn-primary ml-3 btn-login" id="btn-login">
                                {{ __('Log in') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @vite('resources/js/script.js')

    <script type="module">
        // Make showToast globally available for this page
        window.showToast = showToast;

        // Validation functions
        const validateEmail = (email) => {
            if (!email) return "❌ Email harus diisi";
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailPattern.test(email) ? null : "❌ Format email tidak valid";
        };

        const validatePassword = (password) => {
            return password ? null : "❌ Password harus diisi";
        };

        // Redirect handler
        const handleRedirect = async () => {
            const lastVisited = localStorage.getItem("lastVisited");
            let redirectUrl = "/";

            if (lastVisited && lastVisited !== '/login') {
                try {
                    const response = await fetch(lastVisited, {
                        method: 'HEAD'
                    });
                    if (response.ok || response.status === 302) {
                        redirectUrl = lastVisited;
                    } else {
                        localStorage.removeItem("lastVisited");
                        console.log("🧹 Removed invalid URL:", lastVisited);
                    }
                } catch (error) {
                    localStorage.removeItem("lastVisited");
                    console.log("🧹 Removed inaccessible URL:", lastVisited);
                }
            }

            console.log("✅ Redirecting to:", redirectUrl);
            window.location.href = redirectUrl;
        };

        // Login handler
        const handleLogin = async (e) => {
            e.preventDefault();

            const form = document.querySelector(".formLogin");
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            // Validation
            const emailError = validateEmail(email);
            const passwordError = validatePassword(password);

            if (emailError) return showToast(emailError, "error");
            if (passwordError) return showToast(passwordError, "error");

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const axiosClient = axios.create({
                    baseURL: "/",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "Content-Type": "application/json",
                    },
                });
                
                // Add response interceptor
                axiosClient.interceptors.response.use(
                    (response) => {
                        // Success response
                        return response;
                    },
                    (error) => {
                        // Handle error responses
                        if (error.response) {
                            const { status, data } = error.response;
                            
                            if (status === 422 && data.errors) {
                                // Validation errors
                                const firstError = Object.values(data.errors)[0][0];
                                showToast(firstError, "error");
                            } else if (status === 401) {
                                showToast("❌ Email atau password salah", "error");
                            } else if (status === 429) {
                                showToast("❌ Terlalu banyak percobaan login. Coba lagi nanti.", "error");
                            } else {
                                const message = data.message || "❌ Login gagal";
                                showToast(message, "error");
                            }
                        } else if (error.request) {
                            showToast("❌ Tidak dapat terhubung ke server", "error");
                        } else {
                            showToast("❌ Terjadi kesalahan", "error");
                        }
                        
                        return Promise.reject(error);
                    }
                );
                
                const formData = new FormData(form);
                const response = await axiosClient.post(form.getAttribute("action"), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = response.data;
                const token = data.access_token;

                if (!token) {
                    const errorMessage = data.message || "❌ Login gagal";
                    return showToast(errorMessage, "error");
                }

                // Success
                localStorage.setItem("token", token);
                showToast("✅ Login berhasil!", "success");

                setTimeout(handleRedirect, 500);
            } catch (error) {
                // Error sudah ditangani oleh interceptor
                console.error("❌ Login error:", error);
            }
        };

        // Password toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Login event listener
            document.getElementById("btn-login").addEventListener("click", handleLogin);

            // Password toggle
            document.querySelectorAll('.toggle-password').forEach(function(button) {
                button.addEventListener('click', function() {
                    const input = this.closest('.input-group').querySelector('input');
                    const icon = this.querySelector('i');

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    }
                });
            });
        });
    </script>

</body>

</html>
