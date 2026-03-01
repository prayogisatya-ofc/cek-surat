@extends('app')

@section('title', 'Data Warga')

@section('content')
    <div class="container-fluid" id="app">
        <div class="card bg-primary-subtle shadow-none position-relative overflow-hidden mb-4">
            <div class="card-body px-4 py-3">
                <div class="row align-items-center">
                    <div class="col-9">
                        <h4 class="fw-semibold mb-8">Data Warga</h4>
                        <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: '/'">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a class="text-muted text-decoration-none" href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active text-primary" aria-current="page">Data Warga</li>
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
                            <input type="search" class="form-control" placeholder="Cari nik / no kk / nama..." name="q"
                                value="{{ request('q') }}">
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex justify-content-end align-items-center gap-2">
                                <span class="small text-muted me-2" id="selected-count"></span>
                                <button type="button" class="btn btn-danger" id="bulk-delete-btn" disabled>
                                    <i class="ti ti-trash me-1 ms-n1"></i> Hapus Terpilih
                                </button>
                                <a href="{{ route('warga.import.page') }}" class="btn btn-outline-primary">
                                    <i class="ti ti-file-import me-1 ms-n1"></i> Import Warga
                                </a>
                                <a href="{{ route('warga.create') }}" class="btn btn-primary">
                                    <i class="ti ti-plus me-1 ms-n1"></i> Tambah Warga
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table w-100 text-nowrap">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 48px">
                                    <input type="checkbox" class="form-check-input" id="select-all-warga">
                                </th>
                                <th class="text-center" style="width: 60px">No</th>
                                <th>NIK</th>
                                <th>No KK</th>
                                <th>Nama Lengkap</th>
                                <th>L/P</th>
                                <th>Tanggal Lahir</th>
                                <th>RT/RW</th>
                                <th>Dusun</th>
                                <th>Agama</th>
                                <th>Pekerjaan</th>
                                <th>Status Kawin</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($wargas as $item)
                                <tr class="align-middle">
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input bulk-warga-item" value="{{ $item->id }}">
                                    </td>
                                    <td class="text-center">
                                        {{ ($wargas->currentPage() - 1) * $wargas->perPage() + $loop->iteration }}</td>
                                    <td>{{ $item->nik }}</td>
                                    <td>{{ $item->nomor_kk ?? '-' }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->jenis_kelamin === 'LAKI-LAKI' ? 'L' : ($item->jenis_kelamin === 'PEREMPUAN' ? 'P' : '-') }}</td>
                                    <td>{{ optional($item->tanggal_lahir)->format('d/m/Y') }}</td>
                                    <td>{{ ($item->rt ?? '-') . '/' . ($item->rw ?? '-') }}</td>
                                    <td>{{ $item->nama_dusun ?? '-' }}</td>
                                    <td>{{ $item->agama ?? '-' }}</td>
                                    <td>{{ $item->pekerjaan ?? '-' }}</td>
                                    <td>{{ $item->status_kawin ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('warga.edit', $item->id) }}" class="btn btn-warning btn-sm"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form action="{{ route('warga.destroy', $item->id) }}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus warga ini?')">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center">Belum ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $wargas->links() }}

                <form id="bulkDeleteForm" action="{{ route('warga.bulk-delete') }}" method="post" class="d-none">
                    @csrf
                    <div id="bulkDeleteInputs"></div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            const selectAll = document.getElementById('select-all-warga');
            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
            const selectedCount = document.getElementById('selected-count');
            const bulkDeleteForm = document.getElementById('bulkDeleteForm');
            const bulkDeleteInputs = document.getElementById('bulkDeleteInputs');
            const items = Array.from(document.querySelectorAll('.bulk-warga-item'));

            function updateSelectionState() {
                const selected = items.filter((item) => item.checked);
                const selectedLength = selected.length;
                const totalLength = items.length;

                bulkDeleteBtn.disabled = selectedLength === 0;
                selectedCount.textContent = selectedLength > 0 ? `${selectedLength} dipilih` : '';

                if (!selectAll) return;
                selectAll.checked = totalLength > 0 && selectedLength === totalLength;
                selectAll.indeterminate = selectedLength > 0 && selectedLength < totalLength;
            }

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    items.forEach((item) => {
                        item.checked = selectAll.checked;
                    });
                    updateSelectionState();
                });
            }

            items.forEach((item) => {
                item.addEventListener('change', updateSelectionState);
            });

            bulkDeleteBtn.addEventListener('click', function() {
                const selected = items.filter((item) => item.checked).map((item) => item.value);
                if (selected.length === 0) return;

                if (!confirm(`Hapus ${selected.length} data warga yang dipilih?`)) {
                    return;
                }

                bulkDeleteInputs.innerHTML = '';
                selected.forEach((id) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    bulkDeleteInputs.appendChild(input);
                });

                bulkDeleteForm.submit();
            });
        })();
    </script>
@endpush
