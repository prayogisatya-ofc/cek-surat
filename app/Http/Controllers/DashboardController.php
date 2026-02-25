<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSurat;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $total = PengajuanSurat::count();

        $diterima = PengajuanSurat::where('status', 'Diterima')->count();
        $diproses = PengajuanSurat::where('status', 'Diproses')->count();
        $ditolak  = PengajuanSurat::where('status', 'Ditolak')->count();
        $selesai  = PengajuanSurat::where('status', 'Selesai')->count();

        return view('dashboard', [
            'total' => $total,
            'diterima' => $diterima,
            'diproses' => $diproses,
            'ditolak' => $ditolak,
            'selesai' => $selesai,
        ]);
    }
}
