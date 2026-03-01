<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHistory;
use App\Models\PengajuanSurat;
use App\Models\SuratTemplate;
use App\Models\Warga;
use App\Services\SuratGeneratorService;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PengajuanController extends Controller
{
    public function __construct(private readonly SuratGeneratorService $suratGeneratorService)
    {
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));

        $pengajuan = PengajuanSurat::with(['warga', 'suratTemplate'])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('nomor_surat', 'like', "%{$q}%")
                        ->orWhere('judul_surat', 'like', "%{$q}%")
                        ->orWhere('jenis_surat', 'like', "%{$q}%")
                        ->orWhereHas('suratTemplate', function ($t) use ($q) {
                            $t->where('nama', 'like', "%{$q}%")
                                ->orWhere('nomor_jenis', 'like', "%{$q}%");
                        })
                        ->orWhereHas('warga', function ($w) use ($q) {
                            $w->where('nik', 'like', "%{$q}%")
                                ->orWhere('nama', 'like', "%{$q}%");
                        });
                });
            })
            ->when(in_array($status, ['Diterima','Diproses','Ditolak','Selesai']), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($user?->isWarga(), function ($query) use ($user) {
                $query->where('warga_id', $user->warga_id);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pengajuan.index', compact('pengajuan', 'q', 'status'));
    }

    public function create()
    {
        $selectedTemplateId = request()->query('surat_template_id', old('surat_template_id'));
        $selectedTemplate = null;

        $templates = SuratTemplate::query()
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        if ($templates->isEmpty()) {
            return redirect()->route('pengajuan.index')
                ->with('error', 'Belum ada template surat aktif. Silakan hubungi admin.');
        }

        if ($selectedTemplateId) {
            $selectedTemplate = $templates->firstWhere('id', $selectedTemplateId);
        }

        if (Auth::user()?->isWarga()) {
            $warga = Warga::query()
                ->whereKey(Auth::user()->warga_id)
                ->get();

            if ($warga->isEmpty()) {
                return redirect()->route('pengajuan.index')
                    ->with('error', 'Akun warga Anda belum terhubung ke data warga.');
            }

            $dynamicFields = $selectedTemplate
                ? $this->suratGeneratorService->resolveDynamicFields($selectedTemplate)
                : [];

            return view('pengajuan.create', compact(
                'warga',
                'templates',
                'selectedTemplate',
                'dynamicFields'
            ));
        }

        $warga = Warga::orderBy('nama')->limit(200)->get();
        $dynamicFields = $selectedTemplate
            ? $this->suratGeneratorService->resolveDynamicFields($selectedTemplate)
            : [];

        return view('pengajuan.create', compact(
            'warga',
            'templates',
            'selectedTemplate',
            'dynamicFields'
        ));
    }

    public function store(Request $request)
    {
        $isAdmin = Auth::user()?->isAdmin();

        $data = $request->validate([
            'warga_id' => [$isAdmin ? 'required' : 'nullable', 'exists:warga,id'],
            'surat_template_id' => [
                'required',
                Rule::exists('surat_templates', 'id')->where('is_active', true),
            ],
        ]);

        $wargaId = $isAdmin ? $data['warga_id'] : Auth::user()?->warga_id;

        if (!$wargaId) {
            return back()->withInput()->with('error', 'Akun warga Anda belum terhubung ke data warga.');
        }

        $warga = Warga::findOrFail($wargaId);
        $template = SuratTemplate::whereKey($data['surat_template_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $dynamicFields = $this->suratGeneratorService->resolveDynamicFields($template);
        $dynamicRules = $this->suratGeneratorService->dynamicValidationRules($dynamicFields);
        $attributeNames = collect($dynamicFields)
            ->mapWithKeys(fn ($field) => ['fields.' . $field['key'] => $field['label']])
            ->all();

        validator($request->all(), $dynamicRules, [], $attributeNames)->validate();
        $fieldValues = $this->suratGeneratorService->normalizeDynamicValues(
            $dynamicFields,
            (array) $request->input('fields', [])
        );

        $pengajuan = DB::transaction(function () use ($warga, $template, $fieldValues): PengajuanSurat {
            $nextUrut = ((int) PengajuanSurat::query()
                ->where('surat_template_id', $template->id)
                ->lockForUpdate()
                ->max('nomor_urut_jenis')) + 1;

            $nomorSurat = $this->suratGeneratorService->generateNomorSurat(
                $template,
                $warga,
                $nextUrut,
                now()
            );

            return PengajuanSurat::create([
                'warga_id' => $warga->id,
                'surat_template_id' => $template->id,
                'nomor_urut_jenis' => $nextUrut,
                'nomor_surat' => $nomorSurat,
                'jenis_surat' => $template->nama,
                'judul_surat' => 'Pengajuan ' . $template->nama . ' a.n. ' . $warga->nama,
                'field_values' => $fieldValues,
            ]);
        });

        return redirect()->route('pengajuan.show', $pengajuan->id)
            ->with('success', 'Pengajuan surat berhasil dibuat.');
    }

    public function show(PengajuanSurat $pengajuan)
    {
        if (Auth::user()?->isWarga() && $pengajuan->warga_id !== Auth::user()?->warga_id) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $pengajuan->load('warga', 'histories.user', 'suratTemplate');
        $fieldLabels = $pengajuan->suratTemplate
            ? collect($this->suratGeneratorService->resolveDynamicFields($pengajuan->suratTemplate))
                ->mapWithKeys(fn ($field) => [$field['key'] => $field['label']])
                ->all()
            : [];

        return view('pengajuan.show', compact('pengajuan', 'fieldLabels'));
    }

    public function edit(PengajuanSurat $pengajuan)
    {
        $selectedTemplateId = request()->query('surat_template_id', old('surat_template_id', $pengajuan->surat_template_id));
        $templates = SuratTemplate::query()
            ->where(function ($query) use ($pengajuan) {
                $query->where('is_active', true)
                    ->orWhere('id', $pengajuan->surat_template_id);
            })
            ->orderBy('nama')
            ->get();

        $selectedTemplate = $templates->firstWhere('id', $selectedTemplateId)
            ?? $templates->firstWhere('id', $pengajuan->surat_template_id);

        $dynamicFields = $selectedTemplate
            ? $this->suratGeneratorService->resolveDynamicFields($selectedTemplate)
            : [];

        $warga = Warga::orderBy('nama')->limit(200)->get();

        return view('pengajuan.edit', compact(
            'pengajuan',
            'warga',
            'templates',
            'selectedTemplate',
            'dynamicFields'
        ));
    }

    public function update(Request $request, PengajuanSurat $pengajuan)
    {
        $data = $request->validate([
            'warga_id' => ['required','exists:warga,id'],
            'surat_template_id' => ['required', 'exists:surat_templates,id'],
        ]);

        $warga = Warga::findOrFail($data['warga_id']);
        $template = SuratTemplate::findOrFail($data['surat_template_id']);

        $dynamicFields = $this->suratGeneratorService->resolveDynamicFields($template);
        $dynamicRules = $this->suratGeneratorService->dynamicValidationRules($dynamicFields);
        $attributeNames = collect($dynamicFields)
            ->mapWithKeys(fn ($field) => ['fields.' . $field['key'] => $field['label']])
            ->all();

        validator($request->all(), $dynamicRules, [], $attributeNames)->validate();
        $fieldValues = $this->suratGeneratorService->normalizeDynamicValues(
            $dynamicFields,
            (array) $request->input('fields', [])
        );

        DB::transaction(function () use ($pengajuan, $template, $warga, $fieldValues): void {
            $nomorUrut = (int) $pengajuan->nomor_urut_jenis;
            $nomorSurat = (string) $pengajuan->nomor_surat;
            $templateChanged = $pengajuan->surat_template_id !== $template->id;

            if ($templateChanged || $nomorUrut === 0) {
                $nomorUrut = ((int) PengajuanSurat::query()
                    ->where('surat_template_id', $template->id)
                    ->where('id', '!=', $pengajuan->id)
                    ->lockForUpdate()
                    ->max('nomor_urut_jenis')) + 1;

                $nomorSurat = $this->suratGeneratorService->generateNomorSurat(
                    $template,
                    $warga,
                    $nomorUrut,
                    $pengajuan->created_at ?? now()
                );
            }

            if ($templateChanged && !empty($pengajuan->generated_docx_path)) {
                Storage::disk('local')->delete($pengajuan->generated_docx_path);
            }

            $pengajuan->update([
                'warga_id' => $warga->id,
                'surat_template_id' => $template->id,
                'nomor_urut_jenis' => $nomorUrut,
                'nomor_surat' => $nomorSurat,
                'jenis_surat' => $template->nama,
                'judul_surat' => 'Pengajuan ' . $template->nama . ' a.n. ' . $warga->nama,
                'field_values' => $fieldValues,
                'generated_docx_path' => $templateChanged ? null : $pengajuan->generated_docx_path,
            ]);
        });

        return redirect()->route('pengajuan.show', $pengajuan->id)
            ->with('success', 'Pengajuan surat berhasil diupdate.');
    }

    public function destroy(PengajuanSurat $pengajuan)
    {
        if (!empty($pengajuan->generated_docx_path)) {
            Storage::disk('local')->delete($pengajuan->generated_docx_path);
        }

        if (!empty($pengajuan->signed_pdf_path)) {
            Storage::disk('local')->delete($pengajuan->signed_pdf_path);
        }

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

    public function downloadDocx(PengajuanSurat $pengajuan)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN, 'Anda tidak memiliki akses untuk mengunduh dokumen ini.');
        }

        try {
            $docxPath = $this->suratGeneratorService->generateDocx($pengajuan);
            $pengajuan->update(['generated_docx_path' => $docxPath]);
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal membuat dokumen surat. Pastikan template DOCX valid.');
        }

        $filename = Str::slug((string) $pengajuan->jenis_surat, '_') . '-' . ($pengajuan->nomor_surat ?: $pengajuan->id) . '.docx';
        $filename = str_replace('/', '-', $filename);

        return Storage::disk('local')->download($docxPath, $filename);
    }

    public function uploadSignedPdf(Request $request, PengajuanSurat $pengajuan)
    {
        if (!Auth::user()?->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN, 'Anda tidak memiliki akses untuk mengunggah dokumen ini.');
        }

        $data = $request->validate([
            'signed_pdf' => ['required', 'file', 'mimes:pdf', 'max:20480'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $filename = $pengajuan->id . '-' . now()->format('YmdHis') . '.pdf';
        $path = $request->file('signed_pdf')->storeAs('surat/signed', $filename, 'local');

        if (!empty($pengajuan->signed_pdf_path)) {
            Storage::disk('local')->delete($pengajuan->signed_pdf_path);
        }

        $pengajuan->update([
            'signed_pdf_path' => $path,
            'status' => 'Selesai',
        ]);

        PengajuanHistory::create([
            'pengajuan_surat_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'deskripsi' => $data['deskripsi'] ?: 'Dokumen final bertanda tangan telah diunggah.',
        ]);

        return back()->with('success', 'PDF final berhasil diunggah.');
    }

    public function downloadSignedPdf(PengajuanSurat $pengajuan)
    {
        $isAdmin = Auth::user()?->isAdmin();
        $isOwner = Auth::user()?->isWarga() && $pengajuan->warga_id === Auth::user()?->warga_id;

        if (!$isAdmin && !$isOwner) {
            abort(Response::HTTP_FORBIDDEN, 'Anda tidak memiliki akses untuk mengunduh dokumen ini.');
        }

        if (empty($pengajuan->signed_pdf_path) || !Storage::disk('local')->exists($pengajuan->signed_pdf_path)) {
            return back()->with('error', 'Dokumen final belum tersedia.');
        }

        $filename = Str::slug((string) $pengajuan->jenis_surat, '_') . '-' . ($pengajuan->nomor_surat ?: $pengajuan->id) . '.pdf';
        $filename = str_replace('/', '-', $filename);

        return Storage::disk('local')->download($pengajuan->signed_pdf_path, $filename);
    }
}
