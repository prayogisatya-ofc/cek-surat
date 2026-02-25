@extends('app')

@section('title', 'Dashboard')

@push('style')
    <style>
    .btn-white {
        background: #fff;
        color: #5D87FF;
        border: 1px solid #fff;
    }
    .btn-white:hover {
        background: #f1f3f9;
        color: #4570EA;
    }
    .fs-7 {
        font-size: 1.5rem !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="card w-100 bg-primary-subtle overflow-hidden shadow-none mb-4">
        <div class="card-body position-relative">
            <div class="row align-items-center">
                <div class="col-sm-7">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle overflow-hidden me-3 shadow-sm" style="width: 50px; height: 50px; border: 3px solid #fff;">
                            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=5D87FF&color=fff" 
                                 alt="user-img" class="img-fluid">
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1">Selamat Datang, {{ Auth::user()->name }}! 👋</h4>
                            <p class="text-dark mb-0 opacity-75">Pantau progres pembuatan surat.</p>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mt-4">
                        <a href="{{ route('pengajuan.index') }}" class="btn btn-primary">
                            <i class="ti ti-files me-1"></i> Tracking Surat
                        </a>
                        <a href="" class="btn btn-white">
                            <i class="ti ti-users me-1"></i> Data Warga
                        </a>
                    </div>
                </div>
                <div class="col-sm-5 d-none d-sm-block">
                    <div class="welcome-bg-img text-end">
                        <img src="{{ asset('assets/images/backgrounds/banner.png') }}" alt="welcome" class="img-fluid mb-n4" style="max-height: 200px;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted fw-medium mb-1">Total Surat</p>
                            <h3 class="fw-bold mb-0 text-secondary">{{ number_format($total) }}</h3>
                        </div>
                        <div class="bg-secondary-subtle text-secondary rounded-2 p-2">
                            <i class="ti ti-files fs-7"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted fw-medium mb-1">Total Diterima</p>
                            <h3 class="fw-bold mb-0 text-info">{{ number_format($diterima) }}</h3>
                        </div>
                        <div class="bg-info-subtle text-info rounded-2 p-2">
                            <i class="ti ti-file-download fs-7"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted fw-medium mb-1">Total Proses</p>
                            <h3 class="fw-bold mb-0 text-warning">{{ number_format($diproses) }}</h3>
                        </div>
                        <div class="bg-warning-subtle text-warning rounded-3 p-2">
                            <i class="ti ti-file-time fs-7"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted fw-medium mb-1">Total Selesai</p>
                            <h3 class="fw-bold mb-0 text-success">{{ number_format($selesai) }}</h3>
                        </div>
                        <div class="bg-success-subtle text-success rounded-3 p-2">
                            <i class="ti ti-file-check fs-7"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection