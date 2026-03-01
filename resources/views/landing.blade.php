<!DOCTYPE html>
<html lang="id" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.ico') }}" />
    <meta property="og:image" content="{{ asset('assets/images/logos/desa-logo.png') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons/tabler-icons.css') }}">

    <title>Desa Banding Agung - Portal Resmi</title>
</head>

<body class="link-sidebar bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4 py-3 sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#">
                <i class="ti ti-building-community fs-5 me-2 align-middle"></i>
                Desa Banding Agung
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link active" href="#">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="https://bandingagung-talangpadang.sipdeskel.id/pages/home/home.aspx">Profil Desa</a></li>
                    <li class="nav-item"><a class="nav-link" href="#layanan">Layanan</a></li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a class="btn btn-primary px-3 py-2" href="{{ route('login') }}">Masuk</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div id="main-wrapper">
        <div class="container">
            
            <div class="card bg-primary-subtle shadow-none position-relative overflow-hidden mb-5">
                <div class="card-body px-4 py-5">
                    <div class="row align-items-center">
                        <div class="col-lg-8 col-md-7">
                            <span class="badge bg-primary mb-3">Portal Layanan Publik</span>
                            <h2 class="fw-bold mb-3">Selamat Datang di Website Resmi <br> Pemerintahan Desa Banding Agung</h2>
                            <p class="text-muted mb-4 fs-4">
                                Wujudkan pelayanan publik yang transparan, cepat, dan mudah diakses oleh seluruh warga desa.
                            </p>
                            <div class="d-flex gap-2">
                                <a href="#layanan" class="btn btn-primary px-4">Jelajahi Layanan</a>
                                <a href="https://bandingagung-talangpadang.sipdeskel.id/pages/home/home.aspx" class="btn btn-outline-primary px-4">Tentang Desa</a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-5 d-none d-md-block">
                            <div class="text-end">
                                <img src="{{ asset('assets/images/backgrounds/banner.png') }}" class="img-fluid" style="max-height: 250px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="layanan" class="mb-5">
                <h4 class="fw-semibold mb-4 text-center">Layanan Mandiri Warga</h4>
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="card shadow-sm border-0 hover-effect">
                            <div class="card-body text-center py-4">
                                <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex p-3 mb-3">
                                    <i class="ti ti-mail-search fs-7"></i>
                                </div>
                                <h5 class="fw-semibold">Cek Status Surat</h5>
                                <p class="text-muted small">Lacak progres pengajuan surat administrasi Anda menggunakan NIK.</p>
                                <a href="{{ url('/cek-surat') }}" class="btn bg-primary-subtle text-primary mt-2 w-100">Cek Sekarang</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="card shadow-sm border-0">
                            <div class="card-body text-center py-4">
                                <div class="bg-success-subtle text-success rounded-circle d-inline-flex p-3 mb-3">
                                    <i class="ti ti-file-pencil fs-7"></i>
                                </div>
                                <h5 class="fw-semibold">Pengajuan Surat</h5>
                                <p class="text-muted small">Ajukan pembuatan SKU, Surat Domisili, dan lainnya secara online.</p>
                                <a href="{{ route('pengajuan.create') }}" class="btn bg-primary-subtle text-primary mt-2 w-100">Buat Surat</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="card shadow-sm border-0">
                            <div class="card-body text-center py-4">
                                <div class="bg-warning-subtle text-warning rounded-circle d-inline-flex p-3 mb-3">
                                    <i class="ti ti-speakerphone fs-7"></i>
                                </div>
                                <h5 class="fw-semibold">Lapor / Pengaduan</h5>
                                <p class="text-muted small">Sampaikan keluhan atau aspirasi Anda langsung ke pihak desa.</p>
                                <a href="{{ route('pengaduan.create') }}" class="btn bg-primary-subtle text-primary mt-2 w-100">Lapor Sekarang</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <footer class="bg-white border-top py-4 mt-auto">
        <div class="container text-center text-muted small">
            &copy; {{ date('Y') }} Pemerintahan Desa Banding Agung.
        </div>
    </footer>

    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme/app.init.js') }}"></script>
    <script src="{{ asset('assets/js/theme/theme.js') }}"></script>
    <script src="{{ asset('assets/js/theme/app.min.js') }}"></script>
    </body>

</html>