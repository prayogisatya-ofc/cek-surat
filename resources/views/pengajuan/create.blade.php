@extends('app')

@section('title', 'Ajukan Surat')

@section('content')
    <div class="container-fluid">
        <div class="card bg-primary-subtle shadow-none position-relative overflow-hidden mb-4">
            <div class="card-body px-4 py-3">
                <div class="row align-items-center">
                    <div class="col-9">
                        <h4 class="fw-semibold mb-8">Ajukan Surat</h4>
                        <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: '/'">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a class="text-muted text-decoration-none" href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a class="text-muted text-decoration-none" href="{{ route('pengajuan.index') }}">Pengajuan Surat</a>
                                </li>
                                <li class="breadcrumb-item active text-primary" aria-current="page">Ajukan Surat</li>
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

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex gap-2 justify-content-start">
                    <i class="ti ti-circle-x-filled text-danger fs-6"></i>
                    {{ session('error') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @php
            $isAdmin = Auth::user()->isAdmin();
            $wargaLogin = !$isAdmin ? $warga->first() : null;
        @endphp

        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('pengajuan.store') }}" method="post" id="formPengajuan">
                    @csrf

                    <input type="hidden" name="warga_id" id="warga_id" value="{{ old('warga_id', $wargaLogin?->id) }}">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Warga <span class="text-danger">*</span></label>

                            @if ($isAdmin)
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary w-100 text-start" data-bs-toggle="modal"
                                        data-bs-target="#modalWarga">
                                        <span id="warga_display">
                                            @if (old('warga_id'))
                                                Warga terpilih (cek ulang di modal)
                                            @else
                                                Klik untuk cari warga (nama / NIK)
                                            @endif
                                        </span>
                                    </button>

                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#modalWarga">Cari</button>
                                </div>
                            @else
                                <div class="border rounded p-3 bg-light">
                                    <div class="fw-semibold">{{ $wargaLogin?->nama ?? '-' }}</div>
                                    <div class="small text-muted">NIK: {{ $wargaLogin?->nik ?? '-' }}</div>
                                    <div class="small text-muted">Tanggal Lahir:
                                        {{ optional($wargaLogin?->tanggal_lahir)->format('d/m/Y') ?? '-' }}</div>
                                </div>
                            @endif

                            @error('warga_id')
                                <span class="text-danger small d-block mt-1"><strong>{{ $message }}</strong></span>
                            @enderror

                            @if ($isAdmin)
                                <div class="mt-2" id="warga_preview" style="display:none;">
                                    <div class="border rounded p-3 bg-light">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="fw-semibold mb-2" id="prev_nama">-</div>
                                                <div class="small text-muted">NIK: <span id="prev_nik">-</span></div>
                                                <div class="small text-muted">Tanggal Lahir: <span id="prev_tgl">-</span></div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger" id="btnResetWarga">
                                                Hapus Pilihan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jenis Surat / Template <span class="text-danger">*</span></label>
                            <select name="surat_template_id" id="surat_template_id"
                                class="form-select @error('surat_template_id') is-invalid @enderror">
                                <option value="">Pilih jenis surat...</option>
                                @foreach ($templates as $template)
                                    <option value="{{ $template->id }}"
                                        {{ (string) old('surat_template_id', $selectedTemplate?->id) === (string) $template->id ? 'selected' : '' }}>
                                        {{ $template->nama }} (No Jenis: {{ $template->nomor_jenis }})
                                    </option>
                                @endforeach
                            </select>
                            @error('surat_template_id')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                            <div class="small text-muted mt-1">Pilih template untuk menampilkan field pengajuan dinamis.</div>
                        </div>
                    </div>

                    @if ($selectedTemplate)
                        <div class="border rounded p-3 bg-light mt-4">
                            <div class="fw-semibold mb-1">Template Dipilih: {{ $selectedTemplate->nama }}</div>
                            <div class="small text-muted mb-1">Nomor Jenis: {{ $selectedTemplate->nomor_jenis }}</div>
                            @if ($selectedTemplate->deskripsi)
                                <div class="small text-muted">{{ $selectedTemplate->deskripsi }}</div>
                            @endif
                        </div>
                    @endif

                    @if (count($dynamicFields) > 0)
                        <div class="mt-4">
                            <h5 class="fw-semibold mb-3">Data Tambahan Surat</h5>
                            <div class="row g-3">
                                @foreach ($dynamicFields as $field)
                                    @php
                                        $key = $field['key'];
                                        $type = $field['type'] ?? 'text';
                                        $label = $field['label'] ?? \Illuminate\Support\Str::headline($key);
                                        $required = (bool) ($field['required'] ?? false);
                                        $placeholder = $field['placeholder'] ?? '';
                                        $value = old('fields.' . $key, '');
                                    @endphp

                                    <div class="col-md-6">
                                        <label class="form-label">{{ $label }} @if ($required)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>

                                        @if ($type === 'textarea')
                                            <textarea name="fields[{{ $key }}]" rows="3"
                                                class="form-control @error('fields.' . $key) is-invalid @enderror"
                                                placeholder="{{ $placeholder ?: 'Isi ' . $label }}">{{ $value }}</textarea>
                                        @elseif ($type === 'select')
                                            <select name="fields[{{ $key }}]"
                                                class="form-select @error('fields.' . $key) is-invalid @enderror">
                                                <option value="">Pilih {{ $label }}</option>
                                                @foreach (($field['options'] ?? []) as $option)
                                                    <option value="{{ $option }}" {{ (string) $value === (string) $option ? 'selected' : '' }}>
                                                        {{ $option }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="{{ $type === 'number' || $type === 'date' ? $type : 'text' }}"
                                                name="fields[{{ $key }}]" value="{{ $value }}"
                                                class="form-control @error('fields.' . $key) is-invalid @enderror"
                                                placeholder="{{ $placeholder ?: 'Isi ' . $label }}">
                                        @endif

                                        @error('fields.' . $key)
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Ajukan Surat</button>
                        <a href="{{ route('pengajuan.index') }}" class="btn bg-primary-subtle text-primary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($isAdmin)
        <div class="modal fade" id="modalWarga" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title">Cari Warga</h5>
                            <div class="small text-muted">Ketik nama atau NIK. Klik ikon plus untuk memilih.</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="search" class="form-control" id="wargaSearch" placeholder="Cari: nama atau NIK...">
                            <div class="small text-muted mt-1" id="wargaHint">Mulai ketik untuk mencari.</div>
                        </div>

                        <div class="d-flex align-items-center gap-2 mb-2" id="wargaLoading" style="display:none;">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            <div class="small text-muted">Mencari...</div>
                        </div>

                        <div class="list-group" id="wargaList"></div>

                        <div class="text-center text-muted small mt-3" id="wargaEmpty" style="display:none;">Tidak ada hasil.</div>
                    </div>

                    <div class="modal-footer">
                        <div class="small text-muted me-auto" id="wargaCount"></div>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        (function() {
            const selectTemplate = document.getElementById('surat_template_id');
            if (selectTemplate) {
                selectTemplate.addEventListener('change', function() {
                    const url = new URL(window.location.href);
                    if (this.value) {
                        url.searchParams.set('surat_template_id', this.value);
                    } else {
                        url.searchParams.delete('surat_template_id');
                    }
                    window.location.href = url.toString();
                });
            }
        })();
    </script>
@endpush

@if (Auth::user()->isAdmin())
    @push('scripts')
        <script>
            (function() {
                const searchInput = document.getElementById('wargaSearch');
                const listEl = document.getElementById('wargaList');
                const loadingEl = document.getElementById('wargaLoading');
                const emptyEl = document.getElementById('wargaEmpty');
                const countEl = document.getElementById('wargaCount');
                const hintEl = document.getElementById('wargaHint');

                const hiddenId = document.getElementById('warga_id');
                const display = document.getElementById('warga_display');

                const previewWrap = document.getElementById('warga_preview');
                const prevNama = document.getElementById('prev_nama');
                const prevNik = document.getElementById('prev_nik');
                const prevTgl = document.getElementById('prev_tgl');
                const btnReset = document.getElementById('btnResetWarga');

                const API_URL = @json(route('warga.search'));

                let debounceTimer = null;
                let controller = null;

                function setLoading(isLoading) {
                    loadingEl.style.display = isLoading ? '' : 'none';
                }

                function setEmpty(isEmpty) {
                    emptyEl.style.display = isEmpty ? '' : 'none';
                }

                function clearList() {
                    listEl.innerHTML = '';
                    countEl.textContent = '';
                }

                function escapeHtml(str) {
                    return String(str).replace(/[&<>"'`=\/]/g, function(s) {
                        return ({
                            '&': '&amp;',
                            '<': '&lt;',
                            '>': '&gt;',
                            '"': '&quot;',
                            "'": '&#39;',
                            '`': '&#x60;',
                            '=': '&#x3D;',
                            '/': '&#x2F;'
                        })[s];
                    });
                }

                function formatTanggal(tgl) {
                    if (!tgl) return '-';
                    const parts = tgl.split('-');
                    if (parts.length !== 3) return tgl;
                    return `${parts[2]}/${parts[1]}/${parts[0]}`;
                }

                function renderItems(items) {
                    clearList();
                    setEmpty(items.length === 0);

                    countEl.textContent = items.length ? `Menampilkan ${items.length} data` : '';

                    for (const w of items) {
                        const nama = escapeHtml(w.nama);
                        const nik = escapeHtml(w.nik);
                        const tgl = escapeHtml(formatTanggal(w.tanggal_lahir));

                        const item = document.createElement('div');
                        item.className = 'list-group-item d-flex align-items-center justify-content-between';

                        item.innerHTML = `
                            <div>
                                <div class="fw-semibold">${nama}</div>
                                <div class="small text-muted">NIK: ${nik} | Tgl Lahir: ${tgl}</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" title="Pilih warga"
                                data-id="${escapeHtml(w.id)}"
                                data-nama="${nama}"
                                data-nik="${nik}"
                                data-tgl="${escapeHtml(w.tanggal_lahir)}">
                                <i class="ti ti-plus"></i>
                            </button>
                        `;

                        const btn = item.querySelector('button');
                        btn.addEventListener('click', () => {
                            pilihWarga({
                                id: btn.dataset.id,
                                nama: btn.dataset.nama,
                                nik: btn.dataset.nik,
                                tanggal_lahir: btn.dataset.tgl,
                            });
                        });

                        listEl.appendChild(item);
                    }
                }

                function pilihWarga(w) {
                    hiddenId.value = w.id;

                    display.classList.remove('text-muted');
                    display.textContent = `${w.nama} - ${w.nik}`;

                    prevNama.textContent = w.nama;
                    prevNik.textContent = w.nik;
                    prevTgl.textContent = formatTanggal(w.tanggal_lahir);

                    previewWrap.style.display = '';

                    const modalEl = document.getElementById('modalWarga');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }

                if (btnReset) {
                    btnReset.addEventListener('click', () => {
                        hiddenId.value = '';
                        display.classList.add('text-muted');
                        display.textContent = 'Klik untuk cari warga (nama / NIK)';
                        previewWrap.style.display = 'none';
                    });
                }

                async function searchWarga(query) {
                    const q = query.trim();
                    if (q.length < 2) {
                        setLoading(false);
                        clearList();
                        setEmpty(false);
                        hintEl.textContent = 'Ketik minimal 2 karakter.';
                        return;
                    }

                    hintEl.textContent = '';

                    if (controller) controller.abort();
                    controller = new AbortController();

                    setLoading(true);
                    setEmpty(false);

                    try {
                        const url = new URL(API_URL);
                        url.searchParams.set('q', q);

                        const res = await fetch(url.toString(), {
                            headers: {
                                'Accept': 'application/json'
                            },
                            signal: controller.signal,
                        });

                        if (!res.ok) throw new Error('Gagal mengambil data');

                        const data = await res.json();
                        renderItems(Array.isArray(data) ? data : []);
                    } catch (err) {
                        if (err.name !== 'AbortError') {
                            clearList();
                            setEmpty(true);
                            hintEl.textContent = 'Terjadi kesalahan saat mencari warga.';
                        }
                    } finally {
                        setLoading(false);
                    }
                }

                if (searchInput) {
                    searchInput.addEventListener('input', () => {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(() => {
                            searchWarga(searchInput.value);
                        }, 350);
                    });
                }
            })();
        </script>
    @endpush
@endif
