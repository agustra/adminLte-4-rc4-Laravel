<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="{{ url('/dashboard') }}" class="brand-link">
            @php
                $appLogo = config('settings.app_logo');
                $logoUrl = $appLogo ? url('media/' . $appLogo) : asset('img/logo.png');
            @endphp
            <img src="{{ $logoUrl }}" alt="AdminLTE Logo"
                class="brand-image opacity-75 shadow">
            <span class="brand-text fw-light">
                {{ strtoupper(config('app.name')) }}
            </span>
        </a>
    </div>
    <div class="sidebar-wrapper" data-overlayscrollbars="host">
        <div class="os-size-observer">
            <div class="os-size-observer-listener"></div>
        </div>
        <div class="" data-overlayscrollbars-viewport="scrollbarHidden overflowXHidden overflowYScroll"
            tabindex="-1"
            style="margin-right: -16px; margin-bottom: -16px; margin-left: 0px; top: -8px; right: auto; left: -8px; width: calc(100% + 16px); padding: 8px;">

            <nav class="mt-2">
                <!--begin::Sidebar Menu-->
                {!! App\Services\MenuBuilder::build() !!}
                <!--end::Sidebar Menu--> 
 
            </nav>
        </div>
    </div>
</aside>
