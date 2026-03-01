@extends('app')

@section('title', 'Detail Pengaduan')

@section('content')
    <div class="container-fluid">
        <div class="card bg-primary-subtle shadow-none position-relative overflow-hidden mb-4">
            <div class="card-body px-4 py-3">
                <div class="row align-items-center">
                    <div class="col-9">
                        <h4 class="fw-semibold mb-8">Detail Pengaduan</h4>
                        <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: '/'">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a class="text-muted text-decoration-none" href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a class="text-muted text-decoration-none" href="{{ route('pengaduan.index') }}">Pengaduan</a>
                                </li>
                                <li class="breadcrumb-item active text-primary" aria-current="page">Detail Pengaduan</li>
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

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex gap-2 justify-content-start">
                    <i class="ti ti-circle-x-filled text-danger fs-6"></i>
                    {{ session('error') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-start">
                    <div>
                        <h5 class="fw-semibold mb-2">{{ $pengaduan->judul }}</h5>
                        <div class="text-muted mb-1">Kategori: <span class="fw-semibold">{{ $pengaduan->kategori }}</span></div>
                        <div class="text-muted mb-1">Status:
                            @php
                                $badge = match ($pengaduan->status) {
                                    'Baru' => 'bg-info-subtle text-info',
                                    'Diproses' => 'bg-warning-subtle text-warning',
                                    'Selesai' => 'bg-success-subtle text-success',
                                    'Ditolak' => 'bg-danger-subtle text-danger',
                                    default => 'bg-secondary-subtle text-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ $pengaduan->status }}</span>
                        </div>
                        <div class="text-muted">Dibuat: {{ $pengaduan->created_at?->format('d/m/Y H:i') }}</div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('pengaduan.index') }}" class="btn bg-primary-subtle text-primary">Kembali</a>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="fw-semibold mb-2">Data Pelapor</div>
                        <div class="d-flex flex-column gap-1">
                            <div><span class="text-muted">Nama:</span> <span class="fw-semibold">{{ $pengaduan->warga?->nama ?? '-' }}</span></div>
                            <div><span class="text-muted">NIK:</span> <span class="fw-semibold">{{ $pengaduan->warga?->nik ?? '-' }}</span></div>
                            <div><span class="text-muted">Kontak:</span> <span class="fw-semibold">{{ $pengaduan->kontak ?: '-' }}</span></div>
                            <div><span class="text-muted">Lokasi:</span> <span class="fw-semibold">{{ $pengaduan->lokasi ?: '-' }}</span></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="fw-semibold mb-2">Petugas Penangan</div>
                        @if ($pengaduan->adminPenangan)
                            <div class="d-flex flex-column gap-1">
                                <div><span class="text-muted">Nama:</span> <span class="fw-semibold">{{ $pengaduan->adminPenangan->name }}</span></div>
                                <div><span class="text-muted">Username:</span> <span class="fw-semibold">{{ $pengaduan->adminPenangan->username }}</span></div>
                                <div><span class="text-muted">Terakhir update:</span> <span class="fw-semibold">{{ $pengaduan->updated_at?->format('d/m/Y H:i') }}</span></div>
                            </div>
                        @else
                            <div class="text-muted">Belum ada petugas yang menangani.</div>
                        @endif
                    </div>
                </div>

                <hr class="my-4">

                <div class="fw-semibold mb-2">Isi Laporan</div>
                <div class="border rounded p-3 bg-light">
                    {!! nl2br(e($pengaduan->isi_laporan)) !!}
                </div>

                <div class="fw-semibold mb-2 mt-4">Tanggapan</div>
                <div class="border rounded p-3 bg-light">
                    @if ($pengaduan->tanggapan)
                        {!! nl2br(e($pengaduan->tanggapan)) !!}
                    @else
                        <span class="text-muted">Belum ada tanggapan.</span>
                    @endif
                </div>
            </div>
        </div>

        @if (Auth::user()->isAdmin())
            <div class="card">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Proses Pengaduan</h5>

                    <form action="{{ route('pengaduan.status', $pengaduan->id) }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Status<span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    @foreach (['Baru', 'Diproses', 'Selesai', 'Ditolak'] as $s)
                                        <option value="{{ $s }}" {{ old('status', $pengaduan->status) === $s ? 'selected' : '' }}>
                                            {{ $s }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Tanggapan</label>
                                <textarea name="tanggapan" rows="3" class="form-control @error('tanggapan') is-invalid @enderror"
                                    placeholder="Tuliskan tindak lanjut atau hasil verifikasi pengaduan ini.">{{ old('tanggapan', $pengaduan->tanggapan) }}</textarea>
                                @error('tanggapan')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i> Simpan Proses
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection
