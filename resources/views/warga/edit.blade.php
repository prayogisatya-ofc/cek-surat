@extends('app')

@section('title', 'Edit Warga')

@section('content')
    <div class="container-fluid">
        <div class="card bg-primary-subtle shadow-none position-relative overflow-hidden mb-4">
            <div class="card-body px-4 py-3">
                <div class="row align-items-center">
                    <div class="col-9">
                        <h4 class="fw-semibold mb-8">Edit Warga</h4>
                        <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: '/'">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a class="text-muted text-decoration-none" href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a class="text-muted text-decoration-none" href="{{ route('warga.index') }}">Data
                                        Warga</a>
                                </li>
                                <li class="breadcrumb-item active text-primary" aria-current="page">Edit Warga</li>
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
                <form action="{{ route('warga.update', $warga->id) }}" method="post">
                    @csrf
                    @method('put')
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">NIK<span class="text-danger">*</span></label>
                            <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror"
                                placeholder="Contoh: 180602..." value="{{ old('nik', $warga->nik) }}">
                            @error('nik')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nomor KK</label>
                            <input type="text" name="nomor_kk" class="form-control @error('nomor_kk') is-invalid @enderror"
                                placeholder="Contoh: 1806020404230001" value="{{ old('nomor_kk', $warga->nomor_kk) }}">
                            @error('nomor_kk')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tanggal Lahir<span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_lahir"
                                class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                value="{{ old('tanggal_lahir', optional($warga->tanggal_lahir)->format('Y-m-d')) }}">
                            @error('tanggal_lahir')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nama Lengkap<span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                placeholder="Contoh: Budiono Siregar" value="{{ old('nama', $warga->nama) }}">
                            @error('nama')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                <option value="">- Pilih -</option>
                                <option value="LAKI-LAKI"
                                    {{ old('jenis_kelamin', $warga->jenis_kelamin) === 'LAKI-LAKI' ? 'selected' : '' }}>
                                    LAKI-LAKI
                                </option>
                                <option value="PEREMPUAN"
                                    {{ old('jenis_kelamin', $warga->jenis_kelamin) === 'PEREMPUAN' ? 'selected' : '' }}>
                                    PEREMPUAN
                                </option>
                            </select>
                            @error('jenis_kelamin')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir"
                                class="form-control @error('tempat_lahir') is-invalid @enderror"
                                placeholder="Contoh: Panggantian"
                                value="{{ old('tempat_lahir', $warga->tempat_lahir) }}">
                            @error('tempat_lahir')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kode Desa</label>
                            <input type="text" name="kode_desa"
                                class="form-control @error('kode_desa') is-invalid @enderror"
                                value="{{ old('kode_desa', $kodeDesaDefault) }}"
                                placeholder="Otomatis dari data desa"
                                readonly>
                            @error('kode_desa')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">RT</label>
                            <input type="text" name="rt" class="form-control @error('rt') is-invalid @enderror"
                                placeholder="Contoh: 1"
                                value="{{ old('rt', $warga->rt) }}">
                            @error('rt')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">RW</label>
                            <input type="text" name="rw" class="form-control @error('rw') is-invalid @enderror"
                                placeholder="Contoh: 6"
                                value="{{ old('rw', $warga->rw) }}">
                            @error('rw')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Dusun</label>
                            <input type="text" name="nama_dusun"
                                class="form-control @error('nama_dusun') is-invalid @enderror"
                                placeholder="Contoh: Kebun Kelapa"
                                value="{{ old('nama_dusun', $warga->nama_dusun) }}">
                            @error('nama_dusun')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Agama</label>
                            <select name="agama" class="form-select @error('agama') is-invalid @enderror">
                                <option value="">- Pilih agama -</option>
                                @foreach ($agamaOptions as $agama)
                                    <option value="{{ $agama }}"
                                        {{ old('agama', $warga->agama) === $agama ? 'selected' : '' }}>
                                        {{ $agama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('agama')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status Kawin</label>
                            <select name="status_kawin" class="form-select @error('status_kawin') is-invalid @enderror">
                                <option value="">- Pilih status kawin -</option>
                                @foreach ($statusKawinOptions as $statusKawin)
                                    <option value="{{ $statusKawin }}"
                                        {{ old('status_kawin', $warga->status_kawin) === $statusKawin ? 'selected' : '' }}>
                                        {{ $statusKawin }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status_kawin')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pendidikan</label>
                            <select name="pendidikan" class="form-select @error('pendidikan') is-invalid @enderror">
                                <option value="">- Pilih pendidikan -</option>
                                @foreach ($pendidikanOptions as $pendidikan)
                                    <option value="{{ $pendidikan }}"
                                        {{ old('pendidikan', $warga->pendidikan) === $pendidikan ? 'selected' : '' }}>
                                        {{ $pendidikan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pendidikan')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Pekerjaan</label>
                            <input type="text" name="pekerjaan"
                                class="form-control @error('pekerjaan') is-invalid @enderror"
                                placeholder="Contoh: Buruh Harian Lepas"
                                value="{{ old('pekerjaan', $warga->pekerjaan) }}">
                            @error('pekerjaan')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('warga.index') }}" class="btn bg-primary-subtle text-primary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
