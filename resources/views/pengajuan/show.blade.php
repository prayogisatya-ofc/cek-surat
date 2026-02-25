@extends('app')

@section('title', 'Detail Surat')

@section('content')
    <div class="container-fluid">
        <div class="card bg-primary-subtle shadow-none position-relative overflow-hidden mb-4">
            <div class="card-body px-4 py-3">
                <div class="row align-items-center">
                    <div class="col-9">
                        <h4 class="fw-semibold mb-8">Detail Surat</h4>
                        <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: '/'">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a class="text-muted text-decoration-none" href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a class="text-muted text-decoration-none"
                                        href="{{ route('pengajuan.index') }}">Pengajuan Surat</a>
                                </li>
                                <li class="breadcrumb-item active text-primary" aria-current="page">Detail Surat</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-3">
                        <div class="text-end mb-n5">
                            <img src="{{ asset('assets/images/backgrounds/banner.png') }}" class="img-fluid"
                                style="width: 180px">
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
                        <h5 class="fw-semibold mb-2">{{ $pengajuan->judul_surat }}</h5>
                        <div class="text-muted">
                            Jenis: <span class="fw-semibold">{{ $pengajuan->jenis_surat }}</span>
                        </div>
                        <div class="text-muted mt-1">
                            Status:
                            @php
                                $badge = match ($pengajuan->status) {
                                    'Diterima' => 'bg-info-subtle text-info',
                                    'Diproses' => 'bg-warning-subtle text-warning',
                                    'Ditolak' => 'bg-danger-subtle text-danger',
                                    'Selesai' => 'bg-success-subtle text-success',
                                    default => 'bg-secondary-subtle text-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ $pengajuan->status }}</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('pengajuan.edit', $pengajuan->id) }}" class="btn btn-outline-primary">
                            <i class="ti ti-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('pengajuan.index') }}" class="btn bg-primary-subtle text-primary">
                            Kembali
                        </a>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="fw-semibold mb-2">Data Warga</div>
                        <div class="d-flex flex-column gap-1">
                            <div>
                                <span class="text-muted">Nama:</span>
                                <span class="fw-semibold">{{ $pengajuan->warga->nama }}</span>
                            </div>
                            <div>
                                <span class="text-muted">NIK:</span>
                                <span class="fw-semibold">{{ $pengajuan->warga->nik }}</span>
                            </div>
                            <div>
                                <span class="text-muted">Tanggal Lahir:</span>
                                <span class="fw-semibold">
                                    {{ optional($pengajuan->warga->tanggal_lahir)->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="fw-semibold mb-2">Informasi Pengajuan</div>
                        <div class="d-flex flex-column gap-1">
                            <div>
                                <span class="text-muted">Dibuat:</span>
                                <span class="fw-semibold">{{ $pengajuan->created_at?->format('d/m/Y H:i') }}</span>
                            </div>
                            <div>
                                <span class="text-muted">Terakhir Update:</span>
                                <span class="fw-semibold">{{ $pengajuan->updated_at?->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="fw-semibold mb-3">Update Status & Catatan</h5>

                <form action="{{ route('pengajuan.status', $pengajuan->id) }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Status<span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                @foreach (['Diterima', 'Diproses', 'Ditolak', 'Selesai'] as $s)
                                    <option value="{{ $s }}"
                                        {{ old('status', $pengajuan->status) === $s ? 'selected' : '' }}>
                                        {{ $s }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Deskripsi / Catatan<span class="text-danger">*</span></label>
                            <textarea name="deskripsi" rows="3" class="form-control @error('deskripsi') is-invalid @enderror"
                                placeholder="Contoh: Sedang proses verifikasi berkas, menunggu tanda tangan, kades dinas luar, dll.">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i> Simpan Update
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="fw-semibold mb-3">Riwayat Proses</h5>

                @if (($pengajuan->histories ?? collect())->count() === 0)
                    <div class="text-muted">Belum ada riwayat.</div>
                @else
                    <div class="d-flex flex-column gap-3">
                        @foreach ($pengajuan->histories as $h)
                            <div class="p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div class="fw-semibold">Catatan</div>
                                    <div class="text-muted small">{{ $h->created_at?->format('d/m/Y H:i') }}</div>
                                </div>
                                <div class="mt-2">
                                    {!! nl2br(e($h->deskripsi)) !!}
                                </div>
                                <div class="text-muted small mt-2">
                                    @if ($h->user)
                                        Oleh: {{ $h->user->name }} ({{ $h->user->username }})
                                    @else
                                        Oleh: -
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
