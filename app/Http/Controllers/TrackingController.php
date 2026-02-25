<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSurat;
use App\Models\Warga;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index()
    {
        return view('tracking');
    }

    public function cari(Request $request)
    {
        $data = $request->validate([
            'nik' => ['required','string','max:20'],
        ]);

        $warga = Warga::where('nik', $data['nik'])->first();

        if (!$warga) {
            return response()->json([
                'ok' => false,
                'message' => 'NIK tidak ditemukan.',
            ], 404);
        }

        $request->session()->put('publik_warga_id', $warga->id);

        $pengajuan = PengajuanSurat::query()
            ->select('id','judul_surat','jenis_surat','status','created_at')
            ->where('warga_id', $warga->id)
            ->latest()
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'judul_surat' => $p->judul_surat,
                'jenis_surat' => $p->jenis_surat,
                'status' => $p->status,
                'tanggal' => optional($p->created_at)->format('d/m/Y H:i'),
            ]);

        return response()->json([
            'ok' => true,
            'warga' => [
                'id' => $warga->id,
                'nama' => $warga->nama,
                'nik' => $warga->nik,
                'tanggal_lahir' => optional($warga->tanggal_lahir)->format('d/m/Y'),
            ],
            'pengajuan' => $pengajuan,
        ]);
    }

    public function session(Request $request)
    {
        $wargaId = $request->session()->get('publik_warga_id');
        if (!$wargaId) {
            return response()->json(['ok' => false], 200);
        }

        $warga = Warga::find($wargaId);
        if (!$warga) {
            $request->session()->forget('publik_warga_id');
            return response()->json(['ok' => false], 200);
        }

        $pengajuan = PengajuanSurat::query()
            ->select('id','judul_surat','jenis_surat','status','created_at')
            ->where('warga_id', $warga->id)
            ->latest()
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'judul_surat' => $p->judul_surat,
                'jenis_surat' => $p->jenis_surat,
                'status' => $p->status,
                'tanggal' => optional($p->created_at)->format('d/m/Y H:i'),
            ]);

        return response()->json([
            'ok' => true,
            'warga' => [
                'id' => $warga->id,
                'nama' => $warga->nama,
                'nik' => $warga->nik,
                'tanggal_lahir' => optional($warga->tanggal_lahir)->format('d/m/Y'),
            ],
            'pengajuan' => $pengajuan,
        ]);
    }

    public function list(Request $request)
    {
        $wargaId = $request->session()->get('publik_warga_id');
        if (!$wargaId) {
            return response()->json(['ok' => false, 'message' => 'Session tidak ada. Silakan cari NIK lagi.'], 401);
        }

        $pengajuan = PengajuanSurat::query()
            ->select('id','judul_surat','jenis_surat','status','created_at')
            ->where('warga_id', $wargaId)
            ->latest()
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'judul_surat' => $p->judul_surat,
                'jenis_surat' => $p->jenis_surat,
                'status' => $p->status,
                'tanggal' => optional($p->created_at)->format('d/m/Y H:i'),
            ]);

        return response()->json(['ok' => true, 'pengajuan' => $pengajuan]);
    }

    public function detail(Request $request, string $id)
    {
        $wargaId = $request->session()->get('publik_warga_id');
        if (!$wargaId) {
            return response()->json(['ok' => false, 'message' => 'Session tidak ada. Silakan cari NIK lagi.'], 401);
        }

        $pengajuan = PengajuanSurat::with(['warga','histories.user'])
            ->where('warga_id', $wargaId)
            ->findOrFail($id);

        $histories = ($pengajuan->histories ?? collect())->map(fn ($h) => [
            'deskripsi' => $h->deskripsi,
            'waktu' => optional($h->created_at)->format('d/m/Y H:i'),
            'oleh' => $h->user ? $h->user->name : '-',
        ])->values();

        return response()->json([
            'ok' => true,
            'detail' => [
                'id' => $pengajuan->id,
                'judul_surat' => $pengajuan->judul_surat,
                'jenis_surat' => $pengajuan->jenis_surat,
                'status' => $pengajuan->status,
                'dibuat' => optional($pengajuan->created_at)->format('d/m/Y H:i'),
                'warga' => [
                    'nama' => $pengajuan->warga->nama,
                    'nik' => $pengajuan->warga->nik,
                ],
                'histories' => $histories,
            ],
        ]);
    }
}
