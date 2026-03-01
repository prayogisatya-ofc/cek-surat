<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WargaController extends Controller
{
    private const KODE_DESA_DEFAULT = '18.06.02.2022';

    private const AGAMA_OPTIONS = [
        'ISLAM',
        'KRISTEN',
        'KATOLIK',
        'HINDU',
        'BUDDHA',
        'KHONGHUCU',
    ];

    private const STATUS_KAWIN_OPTIONS = [
        'BELUM KAWIN',
        'KAWIN TERCATAT',
        'KAWIN BELUM TERCATAT',
        'CERAI HIDUP',
        'CERAI MATI',
    ];

    private const PENDIDIKAN_OPTIONS = [
        'TIDAK / BELUM SEKOLAH',
        'BELUM TAMAT SD/SEDERAJAT',
        'TAMAT SD / SEDERAJAT',
        'SLTP/SEDERAJAT',
        'SLTA/SEDERAJAT',
        'D1',
        'D2',
        'D3',
        'S1',
        'S2',
        'S3',
        'SEDANG SLTA/SEDERAJAT',
        'SEDANG KULIAH',
        'TIDAK SEDANG SEKOLAH',
    ];

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $wargas = Warga::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('nik', 'like', "%{$q}%")
                        ->orWhere('nomor_kk', 'like', "%{$q}%")
                        ->orWhere('nama', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('warga.index', compact('wargas', 'q'));
    }

    public function create()
    {
        return view('warga.create', $this->formOptions());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nik' => ['required','string','max:20','regex:/^[0-9]+$/', 'unique:warga,nik'],
            'nomor_kk' => ['nullable','string','max:20','regex:/^[0-9]+$/'],
            'nama' => ['required','string','max:150'],
            'tanggal_lahir' => ['required','date'],
            'rt' => ['nullable','string','max:10'],
            'rw' => ['nullable','string','max:10'],
            'nama_dusun' => ['nullable','string','max:150'],
            'jenis_kelamin' => ['nullable','string','max:20'],
            'tempat_lahir' => ['nullable','string','max:150'],
            'kode_desa' => ['required','string', Rule::in([self::KODE_DESA_DEFAULT])],
            'agama' => ['nullable','string','max:50', Rule::in(self::AGAMA_OPTIONS)],
            'pekerjaan' => ['nullable','string','max:150'],
            'pendidikan' => ['nullable','string','max:150', Rule::in(self::PENDIDIKAN_OPTIONS)],
            'status_kawin' => ['nullable','string','max:80', Rule::in(self::STATUS_KAWIN_OPTIONS)],
        ]);

        $data['kode_desa'] = self::KODE_DESA_DEFAULT;

        Warga::create($data);

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil ditambahkan.');
    }

    public function edit(Warga $warga)
    {
        return view('warga.edit', array_merge(compact('warga'), $this->formOptions()));
    }

    public function update(Request $request, Warga $warga)
    {
        $data = $request->validate([
            'nik' => [
                'required','string','max:20','regex:/^[0-9]+$/',
                Rule::unique('warga', 'nik')->ignore($warga->id),
            ],
            'nomor_kk' => ['nullable','string','max:20','regex:/^[0-9]+$/'],
            'nama' => ['required','string','max:150'],
            'tanggal_lahir' => ['required','date'],
            'rt' => ['nullable','string','max:10'],
            'rw' => ['nullable','string','max:10'],
            'nama_dusun' => ['nullable','string','max:150'],
            'jenis_kelamin' => ['nullable','string','max:20'],
            'tempat_lahir' => ['nullable','string','max:150'],
            'kode_desa' => ['required','string', Rule::in([self::KODE_DESA_DEFAULT])],
            'agama' => ['nullable','string','max:50', Rule::in(self::AGAMA_OPTIONS)],
            'pekerjaan' => ['nullable','string','max:150'],
            'pendidikan' => ['nullable','string','max:150', Rule::in(self::PENDIDIKAN_OPTIONS)],
            'status_kawin' => ['nullable','string','max:80', Rule::in(self::STATUS_KAWIN_OPTIONS)],
        ]);

        $data['kode_desa'] = self::KODE_DESA_DEFAULT;

        $warga->update($data);

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil diupdate.');
    }

    public function destroy(Warga $warga)
    {
        $warga->delete();

        return back()->with('success', 'Data warga berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = collect($request->input('ids', []))
            ->filter(fn ($id) => filled($id))
            ->unique()
            ->values()
            ->all();

        if (count($ids) < 1) {
            return back()->with('error', 'Pilih minimal 1 data warga untuk dihapus.');
        }

        $deleted = Warga::query()->whereIn('id', $ids)->delete();

        if ($deleted < 1) {
            return back()->with('error', 'Tidak ada data warga yang berhasil dihapus.');
        }

        return back()->with('success', "{$deleted} data warga berhasil dihapus.");
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
                'tanggal_lahir' => optional($w->tanggal_lahir)->format('Y-m-d'),
            ]);

        return response()->json(['data' => $items]);
    }

    private function formOptions(): array
    {
        return [
            'kodeDesaDefault' => self::KODE_DESA_DEFAULT,
            'agamaOptions' => self::AGAMA_OPTIONS,
            'statusKawinOptions' => self::STATUS_KAWIN_OPTIONS,
            'pendidikanOptions' => self::PENDIDIKAN_OPTIONS,
        ];
    }
}
