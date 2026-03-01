<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaduanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));

        $pengaduan = Pengaduan::with(['warga', 'adminPenangan'])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('judul', 'like', "%{$q}%")
                        ->orWhere('kategori', 'like', "%{$q}%")
                        ->orWhere('isi_laporan', 'like', "%{$q}%")
                        ->orWhereHas('warga', function ($w) use ($q) {
                            $w->where('nik', 'like', "%{$q}%")
                                ->orWhere('nama', 'like', "%{$q}%");
                        });
                });
            })
            ->when(in_array($status, ['Baru', 'Diproses', 'Selesai', 'Ditolak'], true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($user?->isWarga(), function ($query) use ($user) {
                $query->where('warga_id', $user->warga_id);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pengaduan.index', compact('pengaduan', 'q', 'status'));
    }

    public function create()
    {
        $isAdmin = Auth::user()?->isAdmin();

        if (!$isAdmin) {
            $warga = Warga::query()->whereKey(Auth::user()?->warga_id)->first();

            if (!$warga) {
                return redirect()->route('pengaduan.index')
                    ->with('error', 'Akun warga Anda belum terhubung ke data warga.');
            }

            return view('pengaduan.create', compact('warga'));
        }

        return view('pengaduan.create', ['warga' => null]);
    }

    public function store(Request $request)
    {
        $isAdmin = Auth::user()?->isAdmin();

        $data = $request->validate([
            'warga_id' => [$isAdmin ? 'required' : 'nullable', 'exists:warga,id'],
            'judul' => ['required', 'string', 'max:191'],
            'kategori' => ['required', 'string', 'max:100'],
            'isi_laporan' => ['required', 'string'],
            'lokasi' => ['nullable', 'string', 'max:191'],
            'kontak' => ['nullable', 'string', 'max:50'],
        ]);

        $wargaId = $isAdmin ? $data['warga_id'] : Auth::user()?->warga_id;

        if (!$wargaId) {
            return back()->withInput()->with('error', 'Akun warga Anda belum terhubung ke data warga.');
        }

        $pengaduan = Pengaduan::create([
            'warga_id' => $wargaId,
            'judul' => $data['judul'],
            'kategori' => $data['kategori'],
            'isi_laporan' => $data['isi_laporan'],
            'lokasi' => $data['lokasi'] ?? null,
            'kontak' => $data['kontak'] ?? null,
            'status' => 'Baru',
        ]);

        return redirect()->route('pengaduan.show', $pengaduan->id)
            ->with('success', 'Pengaduan berhasil dikirim.');
    }

    public function show(Pengaduan $pengaduan)
    {
        if (Auth::user()?->isWarga() && $pengaduan->warga_id !== Auth::user()?->warga_id) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $pengaduan->load(['warga', 'adminPenangan']);

        return view('pengaduan.show', compact('pengaduan'));
    }

    public function updateStatus(Request $request, Pengaduan $pengaduan)
    {
        $data = $request->validate([
            'status' => ['required', 'in:Baru,Diproses,Selesai,Ditolak'],
            'tanggapan' => ['nullable', 'string'],
        ]);

        $pengaduan->update([
            'status' => $data['status'],
            'tanggapan' => $data['tanggapan'] ?? null,
            'ditangani_oleh' => Auth::id(),
        ]);

        return back()->with('success', 'Status pengaduan berhasil diperbarui.');
    }

    public function destroy(Pengaduan $pengaduan)
    {
        $pengaduan->delete();

        return redirect()->route('pengaduan.index')
            ->with('success', 'Pengaduan berhasil dihapus.');
    }
}

