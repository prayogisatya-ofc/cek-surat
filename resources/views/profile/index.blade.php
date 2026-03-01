@extends('app')

@section('title', 'Profil')

@section('content')
    <div class="container-fluid">
        <div class="card bg-primary-subtle shadow-none position-relative overflow-hidden mb-4">
            <div class="card-body px-4 py-3">
                <div class="row align-items-center">
                    <div class="col-9">
                        <h4 class="fw-semibold mb-8">Profil Pengguna</h4>
                        <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: '/'">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a class="text-muted text-decoration-none" href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active text-primary" aria-current="page">Profil</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-3">
                        <div class="text-end mb-n5">
                            <img src="{{ asset('assets/images/backgrounds/banner.png') }}" class="img-fluid" style="width: 180px">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex gap-2 justify-content-start">
                    <i class="ti ti-circle-check-filled text-success fs-6"></i>
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-5 d-flex align-items-stretch">
                <div class="card w-100 shadow-sm">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center mb-3">
                            <div class="overflow-hidden rounded-circle border border-3 border-primary-subtle">
                                <img src="https://ui-avatars.com/api/?name={{ $user->name }}&background=5D87FF&color=fff"
                                    class="rounded-circle" width="96" height="96" alt="Profil" />
                            </div>
                        </div>
                        <h5 class="fw-semibold mb-1">{{ $user->name }}</h5>
                        <div class="text-muted text-capitalize mb-4">Role: {{ $user->role }}</div>

                        <div class="text-start border rounded p-3 bg-light">
                            <div class="small text-muted mb-1">Username</div>
                            <div class="fw-semibold mb-3">{{ $user->username }}</div>

                            @if ($user->isWarga())
                                <div class="small text-muted mb-1">NIK</div>
                                <div class="fw-semibold mb-3">{{ $warga?->nik ?? '-' }}</div>

                                <div class="small text-muted mb-1">Nomor KK</div>
                                <div class="fw-semibold mb-3">{{ $warga?->nomor_kk ?? '-' }}</div>

                                <div class="small text-muted mb-1">RT / RW</div>
                                <div class="fw-semibold mb-3">{{ $warga?->rt ?? '-' }} / {{ $warga?->rw ?? '-' }}</div>

                                <div class="small text-muted mb-1">Dusun</div>
                                <div class="fw-semibold">{{ $warga?->nama_dusun ?? '-' }}</div>
                            @else
                                <div class="small text-muted">Akun admin tidak terhubung ke data warga.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 d-flex align-items-stretch">
                <div class="card w-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-1">Keamanan Akun</h5>
                        <p class="text-muted mb-4">Kelola password untuk keamanan akun login Anda.</p>

                        @if ($user->isWarga())
                            <form action="{{ route('profile.password.update') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Password Saat Ini<span class="text-danger">*</span></label>
                                    <input type="password" name="current_password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        placeholder="Masukkan password saat ini">
                                    @error('current_password')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Password Baru<span class="text-danger">*</span></label>
                                        <input type="password" name="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="Minimal 6 karakter">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Konfirmasi Password Baru<span class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" class="form-control"
                                            placeholder="Ulangi password baru">
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-lock-check me-1"></i> Update Password
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-info mb-0">
                                Ubah password akun admin dilakukan dari menu <strong>Data Admin</strong>.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
