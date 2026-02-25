<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Favicon icon-->
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.ico') }}" />
    <meta property="og:image" content="{{ asset('assets/images/logos/railsnap-logo.png') }}" />

    <!-- Core Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons/tabler-icons.css') }}">

    <title>{{ config('app.name') }} - Tracking Surat</title>

    {{-- CSRF --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="link-sidebar">
    <div id="main-wrapper">
        <div class="container">
            <div class="card bg-primary-subtle shadow-none position-relative overflow-hidden mb-4 mt-5">
                <div class="card-body px-4 py-3">
                    <div class="row align-items-center">
                        <div class="col-9">
                            <h4 class="fw-semibold mb-8">Cek Status Surat</h4>
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

            <div class="card mb-4">
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                            <h5 class="fw-semibold mb-3 text-center">Masukkan NIK</h5>

                            <div class="input-group">
                                <input type="text" id="nikInput" class="form-control"
                                    placeholder="Contoh: 1805xxxxxxxxxxxx">
                                <button class="btn btn-primary" id="btnCari" type="button">
                                    Cari
                                </button>
                            </div>

                            <div class="small text-muted mt-2 text-center">
                                Setelah ditemukan, daftar surat akan tampil di bawah.
                            </div>

                            <div class="alert alert-danger mt-3 d-none" id="errBox"></div>
                            <div class="alert alert-success mt-3 d-none" id="okBox"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 d-none" id="hasilWrap">
                <div class="col-lg-5">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="fw-semibold mb-2">Daftar Surat</h5>
                                    <div class="text-muted small" id="wargaInfo">-</div>
                                </div>
                                <button class="btn btn-sm btn-outline-secondary" id="btnRefresh">Refresh</button>
                            </div>

                            <hr>

                            <div class="list-group" id="listSurat" style="max-height:520px; overflow:auto;"></div>
                            <div class="text-muted small mt-3 d-none" id="emptyList">Belum ada pengajuan surat.</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="fw-semibold mb-3">Detail Surat</h5>

                            <div class="text-muted" id="hintDetail">
                                Klik salah satu surat di kolom kiri untuk melihat detail.
                            </div>

                            <div class="d-none" id="detailWrap">
                                <div class="mb-3">
                                    <div class="fw-semibold fs-5 mb-2" id="dJudul">-</div>
                                    <div class="text-muted">
                                        Jenis: <span class="fw-semibold" id="dJenis">-</span>
                                    </div>
                                    <div class="mt-1">
                                        Status: <span class="badge bg-secondary" id="dStatus">-</span>
                                        <span class="text-muted small ms-2" id="dDibuat">-</span>
                                    </div>
                                </div>

                                <hr>

                                <div>
                                    <div class="fw-semibold mb-2">Riwayat Proses</div>
                                    <div class="d-flex flex-column gap-2" id="historyList"></div>
                                    <div class="text-muted small d-none" id="historyEmpty">Belum ada riwayat.</div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2 mt-3 d-none" id="loadingDetail">
                                <div class="spinner-border spinner-border-sm" role="status"></div>
                                <div class="small text-muted">Memuat detail...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="dark-transparent sidebartoggler"></div>

    <!-- Import Js Files -->
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme/app.init.js') }}"></script>
    <script src="{{ asset('assets/js/theme/theme.js') }}"></script>
    <script src="{{ asset('assets/js/theme/app.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme/sidebarmenu.js') }}"></script>

    <script>
        (function() {
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const nikInput = document.getElementById('nikInput');
            const btnCari = document.getElementById('btnCari');

            const errBox = document.getElementById('errBox');
            const okBox = document.getElementById('okBox');

            const hasilWrap = document.getElementById('hasilWrap');
            const wargaInfo = document.getElementById('wargaInfo');
            const listSurat = document.getElementById('listSurat');
            const emptyList = document.getElementById('emptyList');
            const btnRefresh = document.getElementById('btnRefresh');

            const hintDetail = document.getElementById('hintDetail');
            const detailWrap = document.getElementById('detailWrap');
            const loadingDetail = document.getElementById('loadingDetail');

            const dJudul = document.getElementById('dJudul');
            const dJenis = document.getElementById('dJenis');
            const dStatus = document.getElementById('dStatus');
            const dDibuat = document.getElementById('dDibuat');

            const historyList = document.getElementById('historyList');
            const historyEmpty = document.getElementById('historyEmpty');

            const URL_CARI = @json(route('publik.surat.cari'));
            const URL_SESSION = @json(route('publik.surat.session'));
            const URL_DETAIL_BASE = @json(url('/cek-surat/detail')); // + /{id}

            let currentSelectedId = null;

            function showErr(msg) {
                errBox.textContent = msg;
                errBox.classList.remove('d-none');
                okBox.classList.add('d-none');
            }

            function showOk(msg) {
                okBox.textContent = msg;
                okBox.classList.remove('d-none');
                errBox.classList.add('d-none');
            }

            function clearAlerts() {
                errBox.classList.add('d-none');
                okBox.classList.add('d-none');
            }

            function statusBadgeClass(status) {
                switch (status) {
                    case 'Diterima':
                        return 'info';
                    case 'Diproses':
                        return 'warning';
                    case 'Ditolak':
                        return 'danger';
                    case 'Selesai':
                        return 'success';
                    default:
                        return 'secondary';
                }
            }

            function renderList(warga, items) {
                hasilWrap.classList.remove('d-none');
                wargaInfo.textContent = `${warga.nama} - NIK: ${warga.nik}`;

                listSurat.innerHTML = '';
                emptyList.classList.toggle('d-none', items.length !== 0);

                items.forEach((p) => {
                    const a = document.createElement('button');
                    a.type = 'button';
                    a.className =
                        'list-group-item list-group-item-action d-flex justify-content-between align-items-start';
                    a.dataset.id = p.id;

                    a.innerHTML = `
            <div class="me-3">
                <div class="fw-semibold mb-1">${escapeHtml(p.judul_surat)}</div>
                <div class="small">${escapeHtml(p.jenis_surat)} - ${escapeHtml(p.tanggal || '')}</div>
            </div>
            <span class="badge fs-2 bg-${statusBadgeClass(p.status)}-subtle text-${statusBadgeClass(p.status)}">${escapeHtml(p.status)}</span>
        `;

                    a.addEventListener('click', () => {
                        currentSelectedId = p.id;
                        [...listSurat.querySelectorAll('.list-group-item')].forEach(el => el.classList
                            .remove('active'));
                        a.classList.add('active');

                        loadDetail(p.id);
                    });

                    listSurat.appendChild(a);
                });

                hintDetail.classList.remove('d-none');
                detailWrap.classList.add('d-none');
                loadingDetail.classList.add('d-none');
                currentSelectedId = null;
            }

            async function cariNik(nik) {
                clearAlerts();
                const cleanNik = String(nik || '').trim();
                if (!cleanNik) {
                    showErr('NIK wajib diisi.');
                    return;
                }

                btnCari.disabled = true;
                btnCari.textContent = 'Mencari...';

                try {
                    const res = await fetch(URL_CARI, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({
                            nik: cleanNik
                        })
                    });

                    const data = await res.json();
                    if (!res.ok || !data.ok) {
                        showErr(data.message || 'Data tidak ditemukan.');
                        return;
                    }

                    showOk('Data ditemukan. Silakan pilih surat di daftar.');
                    renderList(data.warga, data.pengajuan || []);
                } catch (e) {
                    showErr('Terjadi kesalahan saat mencari. Coba lagi.');
                } finally {
                    btnCari.disabled = false;
                    btnCari.textContent = 'Cari';
                }
            }

            async function loadSessionIfAny() {
                try {
                    const res = await fetch(URL_SESSION, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();

                    if (data.ok) {
                        renderList(data.warga, data.pengajuan || []);
                    }
                } catch (e) {
                    // silent
                }
            }

            async function loadDetail(id) {
                hintDetail.classList.add('d-none');
                detailWrap.classList.add('d-none');
                loadingDetail.classList.remove('d-none');

                try {
                    const res = await fetch(`${URL_DETAIL_BASE}/${id}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();

                    if (!res.ok || !data.ok) {
                        loadingDetail.classList.add('d-none');
                        showErr(data.message || 'Gagal memuat detail.');
                        return;
                    }

                    const d = data.detail;

                    dJudul.textContent = d.judul_surat;
                    dJenis.textContent = d.jenis_surat;

                    dStatus.className =
                        `badge fs-2 bg-${statusBadgeClass(d.status)}-subtle text-${statusBadgeClass(d.status)}`;
                    dStatus.textContent = d.status;

                    dDibuat.textContent = `Dibuat: ${d.dibuat}`;

                    // histories
                    historyList.innerHTML = '';
                    const hs = d.histories || [];
                    historyEmpty.classList.toggle('d-none', hs.length !== 0);

                    hs.forEach(h => {
                        const div = document.createElement('div');
                        div.className = 'border rounded p-3';

                        div.innerHTML = `
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div class="fw-semibold small">Catatan</div>
                    <div class="text-muted small">${escapeHtml(h.waktu || '')}</div>
                </div>
                <div class="mt-2">${nl2br(escapeHtml(h.deskripsi || ''))}</div>
                <div class="text-muted small mt-2">Oleh: ${escapeHtml(h.oleh || '-')}</div>
            `;
                        historyList.appendChild(div);
                    });

                    loadingDetail.classList.add('d-none');
                    detailWrap.classList.remove('d-none');
                } catch (e) {
                    loadingDetail.classList.add('d-none');
                    showErr('Terjadi kesalahan saat memuat detail.');
                }
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

            function nl2br(str) {
                return str.replace(/\n/g, '<br>');
            }

            btnCari.addEventListener('click', () => cariNik(nikInput.value));
            nikInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    cariNik(nikInput.value);
                }
            });

            btnRefresh.addEventListener('click', () => loadSessionIfAny());

            loadSessionIfAny();
        })();
    </script>
</body>

</html>
