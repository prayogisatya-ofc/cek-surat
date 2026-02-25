<aside class="left-sidebar with-vertical">
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="{{ route('dashboard') }}" class="text-nowrap logo-img">
                <img src="{{ asset('assets/images/logos/dark-logo.png') }}" class="dark-logo" width="160"
                    alt="Logo-Dark" />
                <img src="{{ asset('assets/images/logos/light-logo.png') }}" class="light-logo" width="160"
                    alt="Logo-light" />
            </a>
            <a href="javascript:void(0)" class="sidebartoggler ms-auto text-decoration-none fs-5 d-block d-xl-none">
                <i class="ti ti-x"></i>
            </a>
        </div>

        <nav class="sidebar-nav scroll-sidebar" data-simplebar>
            <ul id="sidebarnav">
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Umum</span>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}" id="get-url" aria-expanded="false">
                        <span><i class="ti ti-home"></i></span>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>

                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Master Data</span>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                        href="{{ route('admin.index') }}" id="get-url" aria-expanded="false">
                        <span><i class="ti ti-user-shield"></i></span>
                        <span class="hide-menu">Data Admin</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('warga.*') ? 'active' : '' }}"
                        href="{{ route('warga.index') }}" id="get-url" aria-expanded="false">
                        <span><i class="ti ti-users"></i></span>
                        <span class="hide-menu">Data Warga</span>
                    </a>
                </li>

                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Surat</span>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('pengajuan.*') ? 'active' : '' }}"
                        href="{{ route('pengajuan.index') }}" id="get-url" aria-expanded="false">
                        <span><i class="ti ti-files"></i></span>
                        <span class="hide-menu">Pengajuan Surat</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="fixed-profile p-3 mx-4 mb-2 bg-primary-subtle rounded mt-3">
            <div class="hstack gap-3">
                <div class="d-flex align-items-center">
                    <div class="overflow-hidden rounded-circle">
                        <div class="ratio ratio-1x1" style="height: 35px; width: 35px">
                            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=5D87FF&color=fff"
                                class="rounded-circle" width="35" height="35" alt="Profil" />
                        </div>
                    </div>
                </div>
                <div class="john-title text-nowrap text-truncate">
                    <h6 class="mb-0 fs-4 fw-semibold text-truncate">{{ Auth::user()->name }}</h6>
                    <span class="fs-2 text-capitalize">Administrator</span>
                </div>
                <button class="border-0 bg-transparent text-primary ms-auto" tabindex="0" type="submit"
                    form="logout-form" aria-label="logout" data-bs-toggle="tooltip" data-bs-placement="top"
                    data-bs-title="logout">
                    <i class="ti ti-power fs-6"></i>
                </button>
            </div>
        </div>
    </div>
</aside>
