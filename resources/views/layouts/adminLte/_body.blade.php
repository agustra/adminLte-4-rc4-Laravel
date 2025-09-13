<div class="app-wrapper">

    @include('layouts.adminLte._navbar')

    @include('layouts.adminLte._sidebar')

    {{-- <main class="app-main"> --}}
    <main class="app-main" id="main" tabindex="-1">
        <div class="app-content-header">
            {{-- <div class="container-fluid"> 
                <div class="row">
                    <div class="col-sm-6">
                        <h4 class="mb-0">
                            @yield('title')
                            {{ isset($title) ? $title : '' }}
                        </h4>

                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Dashboard
                            </li>
                        </ol>
                    </div>
                </div> 
            </div> --}}
        </div>
        <div class="app-content">
            {{-- <div class="container-fluid">
                
            </div> --}}
            @yield('content')
            {{ isset($slot) ? $slot : null }}

        </div>
    </main>

    @include('layouts.adminLte._footer')

    <div class="sidebar-overlay"></div>
</div>
