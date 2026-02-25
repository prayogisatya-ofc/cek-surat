<header class="topbar">
    <div class="with-vertical">
        <nav class="navbar navbar-expand-lg p-0">
            <ul class="navbar-nav">
                <li class="nav-item nav-icon-hover-bg rounded-circle ms-n2">
                    <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
            </ul>

            <div class="d-block d-lg-none py-4">
                <a href="{{ route('dashboard') }}" class="text-nowrap logo-img">
                    <img src="{{ asset('assets/images/logos/dark-logo.png') }}" width="160" class="dark-logo" alt="Logo-Dark" />
                    <img src="{{ asset('assets/images/logos/light-logo.png') }}" width="160" class="light-logo"
                        alt="Logo-light" />
                </a>
            </div>
            <a class="navbar-toggler nav-icon-hover-bg rounded-circle p-0 mx-0 border-0" href="javascript:void(0)"
                data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav"
                aria-expanded="false" aria-label="Toggle navigation">
                <i class="ti ti-dots fs-7"></i>
            </a>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="d-flex align-items-center justify-content-between">
                    <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">
                        <li class="nav-item nav-icon-hover-bg rounded-circle">
                            <a class="nav-link moon dark-layout" href="javascript:void(0)">
                                <i class="ti ti-moon moon"></i>
                            </a>
                            <a class="nav-link sun light-layout" href="javascript:void(0)">
                                <i class="ti ti-sun sun"></i>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link pe-0" href="javascript:void(0)" id="drop1" aria-expanded="false">
                                <div class="d-flex align-items-center">
                                    <div class="overflow-hidden rounded-circle">
                                        <div class="ratio ratio-1x1" style="height: 35px; width: 35px">
                                            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=5D87FF&color=fff" class="rounded-circle" width="35" height="35" alt="Profil" />
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up"
                                aria-labelledby="drop1">
                                <div class="profile-dropdown position-relative" data-simplebar>
                                    <div class="py-3 px-7 pb-0">
                                        <h5 class="mb-0 fs-5 fw-semibold">Profil Pengguna</h5>
                                    </div>
                                    <div class="d-flex align-items-center py-9 mx-7 border-bottom">
                                        <div class="overflow-hidden rounded-circle">
                                            <div class="ratio ratio-1x1" style="width: 80px; height: 80px">
                                                <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=5D87FF&color=fff" class="rounded-circle" width="80" height="80" alt="Profil" />
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="mb-1 fs-4 fw-semibold">{{ Auth::user()->name }}</h5>
                                            <span class="d-block text-capitalize">Administrator</span>
                                        </div>
                                    </div>
                                    <div class="d-grid py-4 px-7 pt-8">
                                        <form action="{{ route('logout') }}" method="post" id="logout-form">
                                            @csrf
                                        </form>
                                        <button type="submit" class="btn btn-primary" form="logout-form">Keluar</button>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</header>
