<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use App\Models\PengajuanSurat;
use App\Models\User;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isWarga = $user?->isWarga();

        $pengajuanQuery = PengajuanSurat::query();
        $pengaduanQuery = Pengaduan::query();

        if ($isWarga) {
            $pengajuanQuery->where('warga_id', $user->warga_id);
            $pengaduanQuery->where('warga_id', $user->warga_id);
        }

        $suratTotal = (clone $pengajuanQuery)->count();
        $suratDiterima = (clone $pengajuanQuery)->where('status', 'Diterima')->count();
        $suratDiproses = (clone $pengajuanQuery)->where('status', 'Diproses')->count();
        $suratDitolak  = (clone $pengajuanQuery)->where('status', 'Ditolak')->count();
        $suratSelesai  = (clone $pengajuanQuery)->where('status', 'Selesai')->count();

        $aduanTotal = (clone $pengaduanQuery)->count();
        $aduanBaru = (clone $pengaduanQuery)->where('status', 'Baru')->count();
        $aduanDiproses = (clone $pengaduanQuery)->where('status', 'Diproses')->count();
        $aduanSelesai = (clone $pengaduanQuery)->where('status', 'Selesai')->count();
        $aduanDitolak = (clone $pengaduanQuery)->where('status', 'Ditolak')->count();

        $layananTotal = $suratTotal + $aduanTotal;
        $layananSelesai = $suratSelesai + $aduanSelesai;
        $layananDiproses = $suratDiproses + $aduanDiproses;
        $resolusiPersen = $layananTotal > 0 ? (int) round(($layananSelesai / $layananTotal) * 100) : 0;

        $recentPengajuan = (clone $pengajuanQuery)
            ->with('warga')
            ->latest()
            ->limit(5)
            ->get();

        $recentPengaduan = (clone $pengaduanQuery)
            ->with(['warga', 'adminPenangan'])
            ->latest()
            ->limit(5)
            ->get();

        $trendDays = 14;
        $trendStart = now()->copy()->startOfDay()->subDays($trendDays - 1);
        $trendEnd = now()->copy()->endOfDay();

        $suratTrendByDate = (clone $pengajuanQuery)
            ->whereBetween('created_at', [$trendStart, $trendEnd])
            ->selectRaw('DATE(created_at) as tanggal, COUNT(*) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $aduanTrendByDate = (clone $pengaduanQuery)
            ->whereBetween('created_at', [$trendStart, $trendEnd])
            ->selectRaw('DATE(created_at) as tanggal, COUNT(*) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $trendLabels = [];
        $trendSuratData = [];
        $trendAduanData = [];
        $trendLayananData = [];

        for ($i = $trendDays - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateKey = $date->toDateString();
            $label = $date->format('d/m');

            $suratCount = (int) ($suratTrendByDate[$dateKey] ?? 0);
            $aduanCount = (int) ($aduanTrendByDate[$dateKey] ?? 0);

            $trendLabels[] = $label;
            $trendSuratData[] = $suratCount;
            $trendAduanData[] = $aduanCount;
            $trendLayananData[] = $suratCount + $aduanCount;
        }

        return view('dashboard', [
            'suratTotal' => $suratTotal,
            'suratDiterima' => $suratDiterima,
            'suratDiproses' => $suratDiproses,
            'suratDitolak' => $suratDitolak,
            'suratSelesai' => $suratSelesai,
            'aduanTotal' => $aduanTotal,
            'aduanBaru' => $aduanBaru,
            'aduanDiproses' => $aduanDiproses,
            'aduanSelesai' => $aduanSelesai,
            'aduanDitolak' => $aduanDitolak,
            'layananTotal' => $layananTotal,
            'layananSelesai' => $layananSelesai,
            'layananDiproses' => $layananDiproses,
            'resolusiPersen' => $resolusiPersen,
            'recentPengajuan' => $recentPengajuan,
            'recentPengaduan' => $recentPengaduan,
            'totalWarga' => $isWarga ? null : Warga::count(),
            'totalAdmin' => $isWarga ? null : User::where('role', 'admin')->count(),
            'trendLabels' => $trendLabels,
            'trendSuratData' => $trendSuratData,
            'trendAduanData' => $trendAduanData,
            'trendLayananData' => $trendLayananData,
        ]);
    }
}
