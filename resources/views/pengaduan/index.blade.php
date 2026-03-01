@extends('app')

@section('title', 'Lapor / Pengaduan')

@section('content')
    <div class="container-fluid">
        <div class="card bg-primary-subtle shadow-none position-relative overflow-hidden mb-4">
            <div class="card-body px-4 py-3">
                <div class="row align-items-center">
                    <div class="col-9">
                        <h4 class="fw-semibold mb-8">Lapor / Pengaduan</h4>
                        <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: '/'">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a class="text-muted text-decoration-none" href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active text-primary" aria-current="page">Pengaduan</li>
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

        <div class="card">
            <div class="card-body">
                @php
                    $isAdmin = Auth::user()->isAdmin();
                @endphp

                <form action="" method="get" class="mb-4 pb-4 border-bottom">
                    <div class="row row-gap-3">
                        <div class="col-md-4">
                            <input type="search" class="form-control" placeholder="Cari judul, kategori, isi, warga..." name="q"
                                value="{{ $q }}">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="status">
                                <option value="">Semua Status</option>
                                @foreach (['Baru', 'Diproses', 'Selesai', 'Ditolak'] as $s)
                                    <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('pengaduan.create') }}" class="btn btn-primary">
                                    <i class="ti ti-plus me-1 ms-n1"></i> Buat Pengaduan
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table w-100 text-nowrap">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 60px">No</th>
                                <th>Pengaduan</th>
                                @if ($isAdmin)
                                    <th>Warga</th>
                                @endif
                                <th>Status</th>
                                <th>Ditangani</th>
                                <th>Dibuat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pengaduan as $item)
                                <tr class="align-middle">
                                    <td class="text-center">
                                        {{ ($pengaduan->currentPage() - 1) * $pengaduan->perPage() + $loop->iteration }}
                                    </td>
                                    <td>
                                        <div class="fw-semibold mb-1">{{ $item->judul }}</div>
                                        <div class="small text-muted mb-1">Kategori: {{ $item->kategori }}</div>
                                    </td>
                                    @if ($isAdmin)
                                        <td>
                                            <div class="fw-semibold mb-1">{{ $item->warga?->nama ?? '-' }}</div>
                                            <div class="small text-muted">NIK: {{ $item->warga?->nik ?? '-' }}</div>
                                        </td>
                                    @endif
                                    <td>
                                        @php
                                            $badge = match ($item->status) {
                                                'Baru' => 'bg-info-subtle text-info',
                                                'Diproses' => 'bg-warning-subtle text-warning',
                                                'Selesai' => 'bg-success-subtle text-success',
                                                'Ditolak' => 'bg-danger-subtle text-danger',
                                                default => 'bg-secondary-subtle text-secondary',
                                            };
                                        @endphp
                                        <span class="badge fs-2 {{ $badge }}">{{ $item->status }}</span>
                                    </td>
                                    <td>
                                        @if ($item->adminPenangan)
                                            <div class="fw-semibold">{{ $item->adminPenangan->name }}</div>
                                            <div class="small text-muted">{{ $item->adminPenangan->username }}</div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('pengaduan.show', $item->id) }}" class="btn btn-success btn-sm"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Detail">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            @if ($isAdmin)
                                                <form action="{{ route('pengaduan.destroy', $item->id) }}" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Hapus"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus pengaduan ini?')">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isAdmin ? 7 : 6 }}" class="text-center">Belum ada data pengaduan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $pengaduan->links() }}
            </div>
        </div>
    </div>
@endsection
