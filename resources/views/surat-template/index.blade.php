@extends('app')

@section('title', 'Template Surat')

@section('content')
    <div class="container-fluid">
        <div class="card bg-primary-subtle shadow-none position-relative overflow-hidden mb-4">
            <div class="card-body px-4 py-3">
                <div class="row align-items-center">
                    <div class="col-9">
                        <h4 class="fw-semibold mb-8">Template Surat</h4>
                        <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: '/'">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a class="text-muted text-decoration-none" href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active text-primary" aria-current="page">Template Surat</li>
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
                <form method="get" class="row g-3 mb-4 pb-4 border-bottom">
                    <div class="col-md-6">
                        <input type="search" class="form-control" name="q" placeholder="Cari template atau nomor jenis"
                            value="{{ $q }}">
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('surat-template.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i> Tambah Template
                            </a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table text-nowrap align-middle">
                        <thead>
                            <tr>
                                <th style="width: 60px" class="text-center">No</th>
                                <th>Nama Template</th>
                                <th>Nomor Jenis</th>
                                <th>Placeholder</th>
                                <th>Status</th>
                                <th>Dipakai</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($templates as $template)
                                <tr>
                                    <td class="text-center">
                                        {{ ($templates->currentPage() - 1) * $templates->perPage() + $loop->iteration }}
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $template->nama }}</div>
                                        @if ($template->deskripsi)
                                            <div class="small text-muted">{{ \Illuminate\Support\Str::limit($template->deskripsi, 80) }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $template->nomor_jenis }}</td>
                                    <td>{{ count($template->placeholders ?? []) }} field</td>
                                    <td>
                                        @if ($template->is_active)
                                            <span class="badge bg-success-subtle text-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>{{ $template->pengajuan_count }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('surat-template.edit', $template->id) }}"
                                                class="btn btn-warning btn-sm" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form action="{{ route('surat-template.destroy', $template->id) }}" method="post"
                                                onsubmit="return confirm('Hapus template ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Belum ada template surat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $templates->links() }}
            </div>
        </div>
    </div>
@endsection
