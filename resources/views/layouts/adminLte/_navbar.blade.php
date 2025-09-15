@php
    $imageSrc = Auth::user()->avatar_url;
@endphp


<nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
    <div class="container-fluid"> <!--begin::Start Navbar Links-->
        <ul class="navbar-nav">
            <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i
                        class="bi bi-list"></i> </a> </li>
            <li class="nav-item d-none d-md-block"> <a href="#" class="nav-link">Home</a> </li>
            <li class="nav-item d-none d-md-block"> <a href="#" class="nav-link">Contact</a> </li>
        </ul> <!--end::Start Navbar Links--> <!--begin::End Navbar Links-->
        <ul class="navbar-nav ms-auto"> <!--begin::Navbar Search-->


            <li class="nav-item dropdown mt-1 me-2" id="idFilterSingleDate"></li>
            <li class="nav-item dropdown idfiltertgl mt-1 me-2" id="idFilterDateRange"></li>
            <li class="nav-item dropdown idfiltertgl mt-1 me-2" id="idSingleDate"></li>

            <li class="nav-item dropdown mt-1">
                <select id="theme-dropdown" class="form-select form-select-sm"
                    style="width: auto; border: none; background: transparent;">
                    <option value="light">☀️ Light</option>
                    <option value="dark">🌙 Dark</option>
                    <option value="system">🔄 Auto</option>
                </select>
            </li>

            <li class="nav-item px-0">
                <a class="nav-link" href="{{ route('clear.cache') }}">
                    <i class="fas fa-sync"></i>
                </a>
            </li>

            <li class="nav-item px-0">
                <a class="nav-link" href="#" data-lte-toggle="fullscreen"> <i data-lte-icon="maximize"
                        class="bi bi-arrows-fullscreen"></i> <i data-lte-icon="minimize" class="bi bi-fullscreen-exit"
                        style="display: none;"></i>
                </a>
            </li>

            <li class="nav-item dropdown user-menu"> <a href="#" class="nav-link dropdown-toggle"
                    data-bs-toggle="dropdown"> <img src="{{ $imageSrc }}" class="user-image rounded-circle shadow"
                        alt="User Image">
                    <span class="d-none d-md-inline">
                        {{ Ucfirst(Auth::user()->name) }}
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end"> <!--begin::User Image-->
                    <li class="user-header text-bg-primary"> <img src="{{ $imageSrc }}"
                            class="rounded-circle shadow" alt="User Image">
                        <p>
                            {{ Ucfirst(Auth::user()->name) }} - {{ Auth::user()->getRoleNames()->first() ?? 'User' }}
                            <small>Member since {{ Auth::user()->created_at->format('M Y') }}</small>
                        </p>
                    </li> <!--end::User Image--> <!--begin::Menu Body-->
                    <li class="user-body"> <!--begin::Row-->
                        <div class="row">
                            <div class="col-4 text-center"> <a href="{{ route('user.filemanager.index') }}"
                                    style="white-space: nowrap; font-size: 0.9em;">My Media</a> </div>
                            <div class="col-4 text-center"> <a href="#"
                                    style="white-space: nowrap; font-size: 0.9em;">Sales</a> </div>
                            <div class="col-4 text-center"> <a href="#"
                                    style="white-space: nowrap; font-size: 0.9em;">Friends</a> </div>
                        </div> <!--end::Row-->
                    </li> <!--end::Menu Body--> <!--begin::Menu Footer-->
                    <li class="user-footer"> <a href="{{ route('profile.index') }}"
                            class="btn btn-default btn-flat">Profile</a>
                        <a href="#" class="btn btn-default btn-flat float-end"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Signout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                    <!--end::Menu Footer-->
                </ul>
            </li> <!--end::User Menu Dropdown-->
        </ul> <!--end::End Navbar Links-->
    </div> <!--end::Container-->
</nav> <!--end::Header--> <!--begin::Sidebar-->
