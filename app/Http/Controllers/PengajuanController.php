<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHistory;
use App\Models\PengajuanSurat;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));

        $pengajuan = PengajuanSurat::with('warga')
            ->when($q, function ($query) use ($q) {
                $query->where('judul_surat', 'like', "%{$q}%")
                    ->orWhere('jenis_surat', 'like', "%{$q}%")
                    ->orWhereHas('warga', function ($w) use ($q) {
                        $w->where('nik', 'like', "%{$q}%")
                          ->orWhere('nama', 'like', "%{$q}%");
                    });
            })
            ->when(in_array($status, ['Diterima','Diproses','Ditolak','Selesai']), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pengajuan.index', compact('pengajuan', 'q', 'status'));
    }

    public function create()
    {
        $warga = Warga::orderBy('nama')->limit(200)->get();
        return view('pengajuan.create', compact('warga'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'warga_id' => ['required','exists:warga,id'],
            'jenis_surat' => ['required','string','max:100'],
            'judul_surat' => ['required','string','max:191'],
        ]);

        $pengajuan = PengajuanSurat::create([
            'warga_id' => $data['warga_id'],
            'jenis_surat' => $data['jenis_surat'],
            'judul_surat' => $data['judul_surat'],
        ]);

        return redirect()->route('pengajuan.show', $pengajuan->id)
            ->with('success', 'Pengajuan surat berhasil dibuat.');
    }

    public function show(PengajuanSurat $pengajuan)
    {
        $pengajuan->load('warga', 'histories.user');

        return view('pengajuan.show', compact('pengajuan'));
    }

    public function edit(PengajuanSurat $pengajuan)
    {
        $warga = Warga::orderBy('nama')->limit(200)->get();

        return view('pengajuan.edit', compact('pengajuan', 'warga'));
    }

    public function update(Request $request, PengajuanSurat $pengajuan)
    {
        $data = $request->validate([
            'warga_id' => ['required','exists:warga,id'],
            'jenis_surat' => ['required','string','max:100'],
            'judul_surat' => ['required','string','max:191'],
        ]);

        $pengajuan->update($data);

        return redirect()->route('pengajuan.show', $pengajuan->id)
            ->with('success', 'Pengajuan surat berhasil diupdate.');
    }

    public function destroy(PengajuanSurat $pengajuan)
    {
        $pengajuan->delete();

        return redirect()->route('pengajuan.index')
            ->with('success', 'Pengajuan surat berhasil dihapus.');
    }

    public function updateStatus(Request $request, PengajuanSurat $pengajuan)
    {
        $data = $request->validate([
            'status' => ['required','in:Diterima,Diproses,Ditolak,Selesai'],
            'deskripsi' => ['required','string'],
        ]);

        $pengajuan->update(['status' => $data['status']]);

        PengajuanHistory::create([
            'pengajuan_surat_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'deskripsi' => $data['deskripsi'],
        ]);

        return back()->with('success', 'Status dan riwayat berhasil ditambahkan.');
    }
}
