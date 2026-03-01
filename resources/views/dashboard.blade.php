@extends('app')

@section('title', 'Dashboard Pelayanan Publik')

@push('style')
    <style>
        .hero-service {
            background: linear-gradient(135deg, #5d87ff 0%, #3f65d8 100%);
        }

        .stat-card {
            transition: transform .2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .service-badge {
            border: 1px solid rgba(255, 255, 255, .35);
            background: rgba(255, 255, 255, .15);
        }
    </style>
@endpush

@section('content')
    @php
        $isAdmin = Auth::user()->isAdmin();
        $totalTrendSurat = array_sum($trendSuratData);
        $totalTrendAduan = array_sum($trendAduanData);
        $totalTrendLayanan = array_sum($trendLayananData);
        $trendRerataHarian = count($trendLayananData) > 0 ? round($totalTrendLayanan / count($trendLayananData), 1) : 0;

        $badgeSurat = function ($status) {
            return match ($status) {
                'Diterima' => 'bg-info-subtle text-info',
                'Diproses' => 'bg-warning-subtle text-warning',
                'Selesai' => 'bg-success-subtle text-success',
                'Ditolak' => 'bg-danger-subtle text-danger',
                default => 'bg-secondary-subtle text-secondary',
            };
        };

        $badgeAduan = function ($status) {
            return match ($status) {
                'Baru' => 'bg-info-subtle text-info',
                'Diproses' => 'bg-warning-subtle text-warning',
                'Selesai' => 'bg-success-subtle text-success',
                'Ditolak' => 'bg-danger-subtle text-danger',
                default => 'bg-secondary-subtle text-secondary',
            };
        };
    @endphp

    <div class="container-fluid">
        <div class="card hero-service border-0 shadow-sm mb-4 overflow-hidden">
            <div class="card-body p-4 p-lg-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge service-badge text-white fs-2 px-3 py-2">Pusat Pelayanan Publik Desa</span>
                        </div>
                        <h3 class="text-white fw-bold mb-2">Dashboard Layanan Warga</h3>
                        <p class="text-white mb-4 opacity-75">
                            Pantau pengajuan surat, pengaduan masyarakat, dan progres tindak lanjut dalam satu halaman.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('pengajuan.create') }}" class="btn btn-light text-primary fw-semibold">
                                <i class="ti ti-file-plus me-1"></i> Buat Pengajuan Surat
                            </a>
                            <a href="{{ route('pengaduan.create') }}" class="btn btn-outline-light fw-semibold">
                                <i class="ti ti-message-2 me-1"></i> Buat Pengaduan
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end text-center">
                        <img src="{{ asset('assets/images/backgrounds/banner.png') }}" alt="Pelayanan Publik"
                            class="img-fluid" style="max-height: 190px;">
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-sm-6 col-xl-3 d-flex align-items-stretch">
                <div class="card w-100 stat-card border-0 shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Total Layanan</p>
                        <h4 class="fw-semibold mb-2">{{ number_format($layananTotal) }}</h4>
                        <div class="small text-muted">Pengajuan + Pengaduan</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 d-flex align-items-stretch">
                <div class="card w-100 stat-card border-0 shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Sedang Diproses</p>
                        <h4 class="fw-semibold mb-2 text-warning">{{ number_format($layananDiproses) }}</h4>
                        <div class="small text-muted">Membutuhkan tindak lanjut</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 d-flex align-items-stretch">
                <div class="card w-100 stat-card border-0 shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Layanan Selesai</p>
                        <h4 class="fw-semibold mb-2 text-success">{{ number_format($layananSelesai) }}</h4>
                        <div class="small text-muted">Sudah dituntaskan</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 d-flex align-items-stretch">
                <div class="card w-100 stat-card border-0 shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Tingkat Penyelesaian</p>
                        <h4 class="fw-semibold mb-2 text-primary">{{ $resolusiPersen }}%</h4>
                        <div class="progress bg-primary-subtle" style="height: 8px;">
                            <div class="progress-bar" style="width: {{ $resolusiPersen }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($isAdmin)
            <div class="row mb-1">
                <div class="col-sm-6 col-xl-3 d-flex align-items-stretch">
                    <div class="card w-100 border-0 bg-primary-subtle shadow-none">
                        <div class="card-body">
                            <p class="text-primary fw-semibold mb-1">Data Warga Terdaftar</p>
                            <h4 class="text-primary fw-bold mb-0">{{ number_format($totalWarga ?? 0) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3 d-flex align-items-stretch">
                    <div class="card w-100 border-0 bg-secondary-subtle shadow-none">
                        <div class="card-body">
                            <p class="text-secondary fw-semibold mb-1">Petugas Admin</p>
                            <h4 class="text-secondary fw-bold mb-0">{{ number_format($totalAdmin ?? 0) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row mb-4">
            <div class="{{ $isAdmin ? 'col-lg-8' : 'col-lg-12' }} d-flex align-items-stretch">
                <div class="card w-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h4 class="card-title fw-semibold mb-1">Tren Layanan 14 Hari Terakhir</h4>
                                <p class="card-subtitle mb-0">Pergerakan harian pengajuan surat dan pengaduan warga</p>
                            </div>
                            <span class="badge bg-light-primary text-primary">14 Hari</span>
                        </div>

                        <div id="layananTrendChart" style="min-height: 320px;"></div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <div class="p-3 rounded bg-primary-subtle h-100">
                                    <div class="small text-primary mb-1">Pengajuan Surat (14 Hari)</div>
                                    <div class="fw-semibold fs-6 text-primary">{{ number_format($totalTrendSurat) }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded bg-info-subtle h-100">
                                    <div class="small text-info mb-1">Pengaduan (14 Hari)</div>
                                    <div class="fw-semibold fs-6 text-info">{{ number_format($totalTrendAduan) }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded bg-success-subtle h-100">
                                    <div class="small text-success mb-1">Rata-rata Layanan / Hari</div>
                                    <div class="fw-semibold fs-6 text-success">{{ number_format($trendRerataHarian, 1) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($isAdmin)
                <div class="col-lg-4 d-flex align-items-stretch">
                    <div class="card w-100 shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title fw-semibold mb-1">Akses Cepat Layanan</h4>
                            <p class="card-subtitle mb-4">Menu utama pelayanan publik desa</p>

                            <div class="d-grid gap-2 mb-3">
                                <a href="{{ route('pengajuan.index') }}" class="btn btn-outline-primary text-start">
                                    <i class="ti ti-files me-2"></i> Kelola Pengajuan Surat
                                </a>
                                <a href="{{ route('pengaduan.index') }}" class="btn btn-outline-primary text-start">
                                    <i class="ti ti-alert-square-rounded me-2"></i> Kelola Pengaduan
                                </a>
                                <a href="{{ route('warga.index') }}" class="btn btn-outline-primary text-start">
                                    <i class="ti ti-users me-2"></i> Data Warga
                                </a>
                                <a href="{{ route('warga.import.page') }}" class="btn btn-outline-primary text-start">
                                    <i class="ti ti-file-import me-2"></i> Import Data Warga
                                </a>
                            </div>

                            <div class="border rounded p-3 bg-light">
                                <div class="small text-muted mb-1">Fokus Hari Ini</div>
                                <div class="fw-semibold">{{ number_format($layananDiproses) }} layanan sedang diproses</div>
                                <div class="small text-muted mt-2">Prioritaskan layanan dengan status diproses agar waktu tanggap tetap cepat.</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="row">
            <div class="col-lg-6 d-flex align-items-stretch">
                <div class="card w-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h4 class="card-title fw-semibold mb-1">Pengajuan Surat Terbaru</h4>
                                <p class="card-subtitle mb-0">Aktivitas terbaru layanan administrasi</p>
                            </div>
                            <a href="{{ route('pengajuan.index') }}" class="btn btn-sm btn-light-primary">Lihat Semua</a>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle text-nowrap mb-0">
                                <thead>
                                    <tr class="text-muted fw-semibold">
                                        <th>Judul</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentPengajuan as $item)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($item->judul_surat, 42) }}</div>
                                                @if ($isAdmin)
                                                    <div class="small text-muted">{{ $item->warga?->nama ?? '-' }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge fs-2 {{ $badgeSurat($item->status) }}">{{ $item->status }}</span>
                                            </td>
                                            <td class="small text-muted">{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Belum ada pengajuan surat.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 d-flex align-items-stretch">
                <div class="card w-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h4 class="card-title fw-semibold mb-1">Pengaduan Terbaru</h4>
                                <p class="card-subtitle mb-0">Laporan warga yang perlu perhatian</p>
                            </div>
                            <a href="{{ route('pengaduan.index') }}" class="btn btn-sm btn-light-primary">Lihat Semua</a>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle text-nowrap mb-0">
                                <thead>
                                    <tr class="text-muted fw-semibold">
                                        <th>Judul</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentPengaduan as $item)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($item->judul, 42) }}</div>
                                                @if ($isAdmin)
                                                    <div class="small text-muted">{{ $item->warga?->nama ?? '-' }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge fs-2 {{ $badgeAduan($item->status) }}">{{ $item->status }}</span>
                                            </td>
                                            <td class="small text-muted">{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Belum ada pengaduan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script>
        (function() {
            const chartElement = document.querySelector('#layananTrendChart');
            if (!chartElement || typeof ApexCharts === 'undefined') {
                return;
            }

            const labels = @json($trendLabels);
            const dataSurat = @json($trendSuratData);
            const dataAduan = @json($trendAduanData);
            const dataLayanan = @json($trendLayananData);

            const options = {
                chart: {
                    type: 'area',
                    height: 320,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'inherit',
                },
                series: [{
                        name: 'Pengajuan Surat',
                        data: dataSurat
                    },
                    {
                        name: 'Pengaduan',
                        data: dataAduan
                    },
                    {
                        name: 'Total Layanan',
                        data: dataLayanan
                    }
                ],
                colors: ['#5D87FF', '#49BEFF', '#13DEB9'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 0.35,
                        opacityFrom: 0.25,
                        opacityTo: 0.02,
                    }
                },
                grid: {
                    borderColor: '#e6ecf4',
                    strokeDashArray: 4
                },
                xaxis: {
                    categories: labels,
                    labels: {
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    min: 0,
                    forceNiceScale: true,
                    labels: {
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'left'
                },
                tooltip: {
                    shared: true
                }
            };

            const chart = new ApexCharts(chartElement, options);
            chart.render();
        })();
    </script>
@endpush
