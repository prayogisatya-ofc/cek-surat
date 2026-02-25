@extends('app')

@section('title', 'Pengajuan Surat')

@section('content')
    <div class="container-fluid" id="app">
        <div class="card bg-primary-subtle shadow-none position-relative overflow-hidden mb-4">
            <div class="card-body px-4 py-3">
                <div class="row align-items-center">
                    <div class="col-9">
                        <h4 class="fw-semibold mb-8">Pengajuan Surat</h4>
                        <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: '/'">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a class="text-muted text-decoration-none" href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active text-primary" aria-current="page">Pengajuan Surat</li>
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

        <div class="card">
            <div class="card-body">
                <form action="" method="get" class="mb-4 pb-4 border-bottom">
                    <div class="row row-gap-3">
                        <div class="col-md-4">
                            <input type="search" class="form-control" placeholder="Cari pengajuan surat..." name="q"
                                value="{{ request('q') }}">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="status">
                                <option value="">Semua Status</option>
                                @foreach (['Diterima', 'Diproses', 'Ditolak', 'Selesai'] as $s)
                                    <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>
                                        {{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('pengajuan.create') }}" class="btn btn-primary">
                                    <i class="ti ti-plus me-1 ms-n1"></i> Tambah Surat
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
                                <th>Warga</th>
                                <th>Jenis Surat</th>
                                <th>Judul Surat</th>
                                <th>Status</th>
                                <th>Dibuat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pengajuan as $item)
                                <tr class="align-middle">
                                    <td class="text-center">
                                        {{ ($pengajuan->currentPage() - 1) * $pengajuan->perPage() + $loop->iteration }}
                                    </td>
                                    <td>
                                        <div style="font-weight:700" class="mb-1">{{ $item->warga->nama }}</div>
                                        <div style="color:#6b7280;font-size:13px">NIK: {{ $item->warga->nik }}</div>
                                    </td>
                                    <td>{{ $item->jenis_surat }}</td>
                                    <td>{{ $item->judul_surat }}</td>
                                    <td>
                                        @php
                                            $badge = match ($item->status) {
                                                'Diterima' => 'bg-info-subtle text-info',
                                                'Diproses' => 'bg-warning-subtle text-warning',
                                                'Ditolak' => 'bg-danger-subtle text-danger',
                                                'Selesai' => 'bg-success-subtle text-success',
                                                default => 'bg-secondary-subtle text-secondary',
                                            };
                                        @endphp
                                        <span class="badge fs-2 {{ $badge }}">{{ $item->status }}</span>
                                    </td>
                                    <td>{{ $item->created_at->format('d/m/Y, H:i') }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('pengajuan.show', $item->id) }}"
                                                class="btn btn-success btn-sm" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Detail">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('pengajuan.edit', $item->id) }}"
                                                class="btn btn-warning btn-sm" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form action="{{ route('pengajuan.destroy', $item->id) }}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus surat ini?')">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $pengajuan->links() }}
            </div>
        </div>
    </div>
@endsection
