<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" translate="no" data-bs-theme="light">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // Global configuration
        window.APP_CONFIG = {
            baseUrl: '{{ config('app.url') }}',
            apiUrl: '{{ config('app.url') }}/api',
            csrfToken: '{{ csrf_token() }}'
        };

        // Immediately invoked function to set the theme on initial load to prevent FOUC
        (function() {
            try {
                const theme = localStorage.getItem('darkMode') === 'enabled' ? 'dark' : 'light';
                document.documentElement.setAttribute('data-bs-theme', theme);
            } catch (e) {}
        })();
    </script>

    @include('layouts.adminLte._head')

    @vite('resources/css/app.css')

    @include('admin.menus.css')

    @yield('css')
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary app-loaded" data-page="@yield('title')">
    {{-- UI components now bundled in script.js --}}

    @include('layouts.adminLte._body')
    @include('layouts.adminLte._script')

    @if (session('notification'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var notification = @json(session('notification'));
            });
        </script>
    @endif

    @vite('resources/js/admin/admin.js')

    @yield('js')

    @stack('js-modal')

    {{-- DOM-dependent scripts loaded before closing body --}}
    @vite('resources/js/script.js')

</body>

</html>
