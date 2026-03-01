@php
    $isEdit = isset($suratTemplate);
    $model = $suratTemplate ?? null;
    $customFields = old('custom_fields', $model->custom_fields ?? []);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nama Template <span class="text-danger">*</span></label>
        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
            placeholder="Contoh: Surat Keterangan KTP Dalam Proses" value="{{ old('nama', $model->nama ?? '') }}">
        @error('nama')
            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Nomor Jenis <span class="text-danger">*</span></label>
        <input type="text" name="nomor_jenis" class="form-control @error('nomor_jenis') is-invalid @enderror"
            placeholder="Contoh: 470" value="{{ old('nomor_jenis', $model->nomor_jenis ?? '') }}">
        @error('nomor_jenis')
            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Status Template</label>
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active"
                value="1" {{ old('is_active', $model->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Aktif dipakai pengajuan</label>
        </div>
    </div>

    <div class="col-12">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" rows="2" class="form-control @error('deskripsi') is-invalid @enderror"
            placeholder="Deskripsi singkat template surat ini...">{{ old('deskripsi', $model->deskripsi ?? '') }}</textarea>
        @error('deskripsi')
            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Format Nomor Surat</label>
        <input type="text" name="nomor_surat_format"
            class="form-control @error('nomor_surat_format') is-invalid @enderror"
            value="{{ old('nomor_surat_format', $model->nomor_surat_format ?? $defaultNomorFormat) }}">
        <div class="small text-muted mt-1">
            Placeholder yang bisa dipakai: <code>${nomor_urut}</code>, <code>${nomor_urut_padded}</code>,
            <code>${nomor_jenis}</code>, <code>${kode_desa}</code>, <code>${bulan_romawi}</code>,
            <code>${bulan}</code>, <code>${tahun}</code>
        </div>
        @error('nomor_surat_format')
            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">File Template DOCX {{ $isEdit ? '' : '*' }}</label>
        <input type="file" name="template_file" accept=".docx"
            class="form-control @error('template_file') is-invalid @enderror">
        <div class="small text-muted mt-1">
            Gunakan placeholder di file Word dengan format <code>${nama}</code>, <code>${nik}</code>, dst.
        </div>
        @if ($isEdit)
            <div class="small text-muted mt-1">Kosongkan jika tidak ingin mengganti file template.</div>
        @endif
        @error('template_file')
            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    @if ($isEdit)
        <div class="col-12">
            <div class="border rounded p-3 bg-light">
                <div class="fw-semibold mb-2">Placeholder Terdeteksi di Template</div>
                @if (count($model->placeholders ?? []) > 0)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($model->placeholders as $placeholder)
                            <span class="badge bg-primary-subtle text-primary">${{ '{' . $placeholder . '}' }}</span>
                        @endforeach
                    </div>
                @else
                    <div class="text-muted small">Belum ada placeholder terdeteksi.</div>
                @endif
            </div>
        </div>
    @endif

    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label mb-0">Field Dinamis Tambahan</label>
            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddField">
                <i class="ti ti-plus me-1"></i>Tambah Field
            </button>
        </div>

        <div class="table-responsive border rounded">
            <table class="table table-sm mb-0" id="customFieldsTable">
                <thead>
                    <tr>
                        <th style="width: 16%">Key Placeholder</th>
                        <th style="width: 16%">Label Input</th>
                        <th style="width: 14%">Tipe</th>
                        <th style="width: 14%">Placeholder Input</th>
                        <th style="width: 30%">Opsi (khusus select)</th>
                        <th style="width: 6%">Wajib</th>
                        <th style="width: 4%"></th>
                    </tr>
                </thead>
                <tbody id="customFieldsBody">
                    @forelse ($customFields as $idx => $field)
                        <tr>
                            <td>
                                <input type="text" name="custom_fields[{{ $idx }}][key]" class="form-control form-control-sm"
                                    value="{{ data_get($field, 'key') }}" placeholder="contoh: keperluan">
                            </td>
                            <td>
                                <input type="text" name="custom_fields[{{ $idx }}][label]" class="form-control form-control-sm"
                                    value="{{ data_get($field, 'label') }}" placeholder="Contoh: Keperluan">
                            </td>
                            <td>
                                <select name="custom_fields[{{ $idx }}][type]" class="form-select form-select-sm field-type">
                                    @foreach (['text', 'textarea', 'number', 'date', 'select'] as $type)
                                        <option value="{{ $type }}"
                                            {{ data_get($field, 'type', 'text') === $type ? 'selected' : '' }}>
                                            {{ strtoupper($type) }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" name="custom_fields[{{ $idx }}][placeholder]"
                                    class="form-control form-control-sm" value="{{ data_get($field, 'placeholder') }}"
                                    placeholder="Placeholder input">
                            </td>
                            <td>
                                <textarea name="custom_fields[{{ $idx }}][options]" rows="1"
                                    class="form-control form-control-sm field-options"
                                    placeholder="Pisahkan dengan enter atau koma">{{ implode("\n", data_get($field, 'options', [])) }}</textarea>
                            </td>
                            <td class="text-center">
                                <input class="form-check-input mt-2" type="checkbox"
                                    name="custom_fields[{{ $idx }}][required]" value="1"
                                    {{ data_get($field, 'required') ? 'checked' : '' }}>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td>
                                <input type="text" name="custom_fields[0][key]" class="form-control form-control-sm"
                                    placeholder="contoh: keperluan">
                            </td>
                            <td>
                                <input type="text" name="custom_fields[0][label]" class="form-control form-control-sm"
                                    placeholder="Contoh: Keperluan">
                            </td>
                            <td>
                                <select name="custom_fields[0][type]" class="form-select form-select-sm field-type">
                                    @foreach (['text', 'textarea', 'number', 'date', 'select'] as $type)
                                        <option value="{{ $type }}" {{ $type === 'text' ? 'selected' : '' }}>
                                            {{ strtoupper($type) }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" name="custom_fields[0][placeholder]"
                                    class="form-control form-control-sm" placeholder="Placeholder input">
                            </td>
                            <td>
                                <textarea name="custom_fields[0][options]" rows="1"
                                    class="form-control form-control-sm field-options"
                                    placeholder="Pisahkan dengan enter atau koma"></textarea>
                            </td>
                            <td class="text-center">
                                <input class="form-check-input mt-2" type="checkbox" name="custom_fields[0][required]"
                                    value="1">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="small text-muted mt-1">
            Field tambahan dipakai jika placeholder tidak tersedia di data warga/sistem.
        </div>
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update Template' : 'Simpan Template' }}</button>
    <a href="{{ route('surat-template.index') }}" class="btn bg-primary-subtle text-primary">Kembali</a>
</div>

@push('scripts')
    <script>
        (function() {
            const body = document.getElementById('customFieldsBody');
            const addButton = document.getElementById('btnAddField');

            if (!body || !addButton) return;

            function newRowHtml(index) {
                return `
                    <tr>
                        <td><input type="text" name="custom_fields[${index}][key]" class="form-control form-control-sm" placeholder="contoh: keperluan"></td>
                        <td><input type="text" name="custom_fields[${index}][label]" class="form-control form-control-sm" placeholder="Contoh: Keperluan"></td>
                        <td>
                            <select name="custom_fields[${index}][type]" class="form-select form-select-sm field-type">
                                <option value="text" selected>TEXT</option>
                                <option value="textarea">TEXTAREA</option>
                                <option value="number">NUMBER</option>
                                <option value="date">DATE</option>
                                <option value="select">SELECT</option>
                            </select>
                        </td>
                        <td><input type="text" name="custom_fields[${index}][placeholder]" class="form-control form-control-sm" placeholder="Placeholder input"></td>
                        <td><textarea name="custom_fields[${index}][options]" rows="1" class="form-control form-control-sm field-options" placeholder="Pisahkan dengan enter atau koma"></textarea></td>
                        <td class="text-center"><input class="form-check-input mt-2" type="checkbox" name="custom_fields[${index}][required]" value="1"></td>
                        <td><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="ti ti-trash"></i></button></td>
                    </tr>
                `;
            }

            function nextIndex() {
                const rows = body.querySelectorAll('tr');
                return rows.length;
            }

            addButton.addEventListener('click', function() {
                body.insertAdjacentHTML('beforeend', newRowHtml(nextIndex()));
            });

            body.addEventListener('click', function(event) {
                const btn = event.target.closest('.btn-remove-row');
                if (!btn) return;

                const rows = body.querySelectorAll('tr');
                if (rows.length <= 1) {
                    rows[0].querySelectorAll('input, textarea').forEach(el => el.value = '');
                    rows[0].querySelectorAll('input[type="checkbox"]').forEach(el => el.checked = false);
                    rows[0].querySelectorAll('select').forEach(el => el.value = 'text');
                    return;
                }

                btn.closest('tr').remove();
            });
        })();
    </script>
@endpush
