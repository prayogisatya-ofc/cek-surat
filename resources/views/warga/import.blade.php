@extends('app')

@section('title', 'Import Data Warga')

@push('style')
    <style>
        .upload-box {
            border: 2px dashed #d9e2f2;
            border-radius: 12px;
            background: #f8fbff;
            transition: 0.2s ease;
        }

        .upload-box.dragover {
            border-color: #5d87ff;
            background: #edf3ff;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="card bg-primary-subtle shadow-none position-relative overflow-hidden mb-4">
            <div class="card-body px-4 py-3">
                <div class="row align-items-center">
                    <div class="col-9">
                        <h4 class="fw-semibold mb-8">Import Data Warga</h4>
                        <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: '/'">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a class="text-muted text-decoration-none" href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a class="text-muted text-decoration-none" href="{{ route('warga.index') }}">Data Warga</a>
                                </li>
                                <li class="breadcrumb-item active text-primary" aria-current="page">Import</li>
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

        <div id="alert-container"></div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-1">Upload File Excel</h5>
                        <p class="text-muted mb-4">Format yang didukung: <strong>.xls</strong> dan <strong>.xlsx</strong>, maksimal 10 MB.</p>

                        <form id="importForm" action="{{ route('warga.import.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="upload-box p-4 text-center mb-3" id="uploadBox">
                                <i class="ti ti-file-spreadsheet fs-8 text-primary d-block mb-2"></i>
                                <div class="fw-semibold mb-1">Tarik dan lepas file ke sini</div>
                                <div class="text-muted small mb-3">atau pilih file secara manual</div>
                                <input class="form-control" type="file" name="file" id="fileInput" accept=".xls,.xlsx">
                                <div class="small text-muted mt-2" id="fileName">Belum ada file dipilih.</div>
                            </div>

                            <div id="progressWrap" class="mb-3" style="display:none;">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Progress upload</span>
                                    <span id="progressText">0%</span>
                                </div>
                                <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressBar"
                                        style="width:0%"></div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" id="btnImport">
                                    <span class="btn-label">Mulai Import</span>
                                    <span class="btn-loading" style="display:none;">
                                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                        Mengupload...
                                    </span>
                                </button>
                                <a href="{{ route('warga.index') }}" class="btn bg-primary-subtle text-primary">Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3">Catatan Kolom Penting</h5>
                        <ul class="mb-0 text-muted">
                            <li>NIK</li>
                            <li>No_KK</li>
                            <li>Nama</li>
                            <li>Tanggal_Lahir</li>
                            <li>Nama_RT</li>
                            <li>Nama_RW</li>
                            <li>Nama_Dusun</li>
                            <li>Kode_Desa</li>
                            <li>Jenis_Kelamin</li>
                            <li>Tempat_Lahir</li>
                            <li>Agama</li>
                            <li>Pekerjaan</li>
                            <li>Pendidikan_KK</li>
                            <li>Status_Kawin</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            const form = document.getElementById('importForm');
            const uploadBox = document.getElementById('uploadBox');
            const fileInput = document.getElementById('fileInput');
            const fileName = document.getElementById('fileName');
            const progressWrap = document.getElementById('progressWrap');
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');
            const btnImport = document.getElementById('btnImport');
            const btnLabel = btnImport.querySelector('.btn-label');
            const btnLoading = btnImport.querySelector('.btn-loading');
            const alertContainer = document.getElementById('alert-container');

            function setLoading(isLoading) {
                btnImport.disabled = isLoading;
                btnLabel.style.display = isLoading ? 'none' : '';
                btnLoading.style.display = isLoading ? '' : 'none';
            }

            function setProgress(value) {
                const percent = Math.max(0, Math.min(100, value));
                progressBar.style.width = percent + '%';
                progressText.textContent = percent + '%';
            }

            function showAlert(type, message) {
                const icon = type === 'success' ? 'ti-circle-check-filled text-success' : 'ti-circle-x-filled text-danger';
                alertContainer.innerHTML = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        <div class="d-flex gap-2 justify-content-start">
                            <i class="ti ${icon} fs-6"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            }

            function parseJson(text) {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    return null;
                }
            }

            function updateFileName() {
                const file = fileInput.files[0];
                fileName.textContent = file ? `File dipilih: ${file.name}` : 'Belum ada file dipilih.';
            }

            fileInput.addEventListener('change', updateFileName);

            ['dragenter', 'dragover'].forEach((eventName) => {
                uploadBox.addEventListener(eventName, (event) => {
                    event.preventDefault();
                    uploadBox.classList.add('dragover');
                });
            });

            ['dragleave', 'drop'].forEach((eventName) => {
                uploadBox.addEventListener(eventName, (event) => {
                    event.preventDefault();
                    uploadBox.classList.remove('dragover');
                });
            });

            uploadBox.addEventListener('drop', (event) => {
                const files = event.dataTransfer?.files;
                if (!files || !files.length) return;
                fileInput.files = files;
                updateFileName();
            });

            form.addEventListener('submit', (event) => {
                event.preventDefault();

                const file = fileInput.files[0];
                if (!file) {
                    showAlert('danger', 'Pilih file Excel terlebih dahulu.');
                    return;
                }

                const formData = new FormData(form);
                const xhr = new XMLHttpRequest();

                setLoading(true);
                progressWrap.style.display = '';
                setProgress(0);
                alertContainer.innerHTML = '';

                xhr.open('POST', form.action, true);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                xhr.upload.onprogress = function(event) {
                    if (!event.lengthComputable) return;
                    const percent = Math.round((event.loaded / event.total) * 100);
                    setProgress(percent);
                };

                xhr.onload = function() {
                    setLoading(false);
                    const response = parseJson(xhr.responseText) || {};

                    if (xhr.status >= 200 && xhr.status < 300) {
                        setProgress(100);
                        showAlert('success', response.message || 'Import warga berhasil diproses.');
                        form.reset();
                        updateFileName();
                        return;
                    }

                    const firstValidationError = response.errors?.file?.[0];
                    const baseMessage = firstValidationError || response.message || `Import gagal (HTTP ${xhr.status}).`;
                    const detailMessage = response.error ? `<br><small>${response.error}</small>` : '';
                    showAlert('danger', `${baseMessage}${detailMessage}`);
                };

                xhr.onerror = function() {
                    setLoading(false);
                    showAlert('danger', 'Terjadi gangguan jaringan saat upload.');
                };

                xhr.send(formData);
            });
        })();
    </script>
@endpush
