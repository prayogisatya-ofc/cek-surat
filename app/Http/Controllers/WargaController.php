<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $wargas = Warga::query()
            ->when($q, function ($query) use ($q) {
                $query->where('nik', 'like', "%{$q}%")
                      ->orWhere('nama', 'like', "%{$q}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('warga.index', compact('wargas', 'q'));
    }

    public function create()
    {
        return view('warga.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nik' => ['required','string','max:20','regex:/^[0-9]+$/', 'unique:warga,nik'],
            'nama' => ['required','string','max:150'],
            'tanggal_lahir' => ['required','date'],
        ]);

        Warga::create($data);

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil ditambahkan.');
    }

    public function edit(Warga $warga)
    {
        return view('warga.edit', compact('warga'));
    }

    public function update(Request $request, Warga $warga)
    {
        $data = $request->validate([
            'nik' => [
                'required','string','max:20','regex:/^[0-9]+$/',
                Rule::unique('warga', 'nik')->ignore($warga->id),
            ],
            'nama' => ['required','string','max:150'],
            'tanggal_lahir' => ['required','date'],
        ]);

        $warga->update($data);

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil diupdate.');
    }

    public function destroy(Warga $warga)
    {
        $warga->delete();

        return back()->with('success', 'Data warga berhasil dihapus.');
    }

    public function cariJson(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['data' => []]);
        }

        $items = Warga::query()
            ->select('id', 'nik', 'nama', 'tanggal_lahir')
            ->where(function ($query) use ($q) {
                $query->where('nik', 'like', "%{$q}%")
                    ->orWhere('nama', 'like', "%{$q}%");
            })
            ->orderBy('nama')
            ->limit(50)
            ->get()
            ->map(fn ($w) => [
                'id' => $w->id,
                'nik' => $w->nik,
                'nama' => $w->nama,
                'tanggal_lahir' => optional($w->tanggal_lahir)->format('Y-m-d'), // FIX: date only
            ]);

        return response()->json(['data' => $items]);
    }
}
