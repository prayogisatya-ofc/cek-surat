<?php

namespace App\Http\Controllers;

use App\Models\SuratTemplate;
use App\Services\SuratGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SuratTemplateController extends Controller
{
    public function __construct(private readonly SuratGeneratorService $suratGeneratorService)
    {
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $templates = SuratTemplate::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('nama', 'like', "%{$q}%")
                        ->orWhere('nomor_jenis', 'like', "%{$q}%");
                });
            })
            ->withCount('pengajuan')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('surat-template.index', compact('templates', 'q'));
    }

    public function create()
    {
        $defaultNomorFormat = '${nomor_urut_padded}/${nomor_jenis}/${kode_desa}/${bulan_romawi}/${tahun}';

        return view('surat-template.create', compact('defaultNomorFormat'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'nomor_jenis' => ['required', 'string', 'max:50', 'unique:surat_templates,nomor_jenis'],
            'deskripsi' => ['nullable', 'string'],
            'nomor_surat_format' => ['nullable', 'string', 'max:191'],
            'is_active' => ['nullable', 'boolean'],
            'template_file' => ['required', 'file', 'mimes:docx', 'max:20480'],
            'custom_fields' => ['nullable', 'array'],
            'custom_fields.*.key' => ['nullable', 'string', 'max:60', 'regex:/^[a-zA-Z0-9_]+$/'],
            'custom_fields.*.label' => ['nullable', 'string', 'max:120'],
            'custom_fields.*.type' => ['nullable', Rule::in(['text', 'textarea', 'number', 'date', 'select'])],
            'custom_fields.*.placeholder' => ['nullable', 'string', 'max:191'],
            'custom_fields.*.options' => ['nullable', 'string'],
            'custom_fields.*.required' => ['nullable', 'boolean'],
        ]);

        $filename = (string) Str::ulid() . '.docx';
        $templatePath = $request->file('template_file')->storeAs('surat/templates', $filename, 'local');
        $placeholders = $this->suratGeneratorService->extractTemplatePlaceholders($templatePath);
        $customFields = $this->normalizeCustomFields($request->input('custom_fields', []));

        SuratTemplate::create([
            'nama' => $data['nama'],
            'nomor_jenis' => $data['nomor_jenis'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'template_path' => $templatePath,
            'placeholders' => $placeholders,
            'custom_fields' => $customFields,
            'nomor_surat_format' => $data['nomor_surat_format'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return redirect()->route('surat-template.index')
            ->with('success', 'Template surat berhasil dibuat.');
    }

    public function edit(SuratTemplate $suratTemplate)
    {
        $defaultNomorFormat = '${nomor_urut_padded}/${nomor_jenis}/${kode_desa}/${bulan_romawi}/${tahun}';

        return view('surat-template.edit', compact('suratTemplate', 'defaultNomorFormat'));
    }

    public function update(Request $request, SuratTemplate $suratTemplate)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'nomor_jenis' => [
                'required',
                'string',
                'max:50',
                Rule::unique('surat_templates', 'nomor_jenis')->ignore($suratTemplate->id),
            ],
            'deskripsi' => ['nullable', 'string'],
            'nomor_surat_format' => ['nullable', 'string', 'max:191'],
            'is_active' => ['nullable', 'boolean'],
            'template_file' => ['nullable', 'file', 'mimes:docx', 'max:20480'],
            'custom_fields' => ['nullable', 'array'],
            'custom_fields.*.key' => ['nullable', 'string', 'max:60', 'regex:/^[a-zA-Z0-9_]+$/'],
            'custom_fields.*.label' => ['nullable', 'string', 'max:120'],
            'custom_fields.*.type' => ['nullable', Rule::in(['text', 'textarea', 'number', 'date', 'select'])],
            'custom_fields.*.placeholder' => ['nullable', 'string', 'max:191'],
            'custom_fields.*.options' => ['nullable', 'string'],
            'custom_fields.*.required' => ['nullable', 'boolean'],
        ]);

        $templatePath = $suratTemplate->template_path;
        $placeholders = $suratTemplate->placeholders ?? [];

        if ($request->hasFile('template_file')) {
            $filename = (string) Str::ulid() . '.docx';
            $templatePath = $request->file('template_file')->storeAs('surat/templates', $filename, 'local');
            $placeholders = $this->suratGeneratorService->extractTemplatePlaceholders($templatePath);

            if (!empty($suratTemplate->template_path)) {
                Storage::disk('local')->delete($suratTemplate->template_path);
            }
        }

        $suratTemplate->update([
            'nama' => $data['nama'],
            'nomor_jenis' => $data['nomor_jenis'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'template_path' => $templatePath,
            'placeholders' => $placeholders,
            'custom_fields' => $this->normalizeCustomFields($request->input('custom_fields', [])),
            'nomor_surat_format' => $data['nomor_surat_format'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return redirect()->route('surat-template.index')
            ->with('success', 'Template surat berhasil diperbarui.');
    }

    public function destroy(SuratTemplate $suratTemplate)
    {
        if (!empty($suratTemplate->template_path)) {
            Storage::disk('local')->delete($suratTemplate->template_path);
        }

        $suratTemplate->delete();

        return redirect()->route('surat-template.index')
            ->with('success', 'Template surat berhasil dihapus.');
    }

    /**
     * @param array<int, array<string, mixed>> $customFields
     * @return array<int, array<string, mixed>>
     */
    private function normalizeCustomFields(array $customFields): array
    {
        $normalized = [];

        foreach ($customFields as $field) {
            $key = Str::lower(trim((string) data_get($field, 'key', '')));
            $key = preg_replace('/[^a-z0-9_]/', '_', $key) ?? '';
            $key = trim($key, '_');

            if ($key === '') {
                continue;
            }

            $type = (string) data_get($field, 'type', 'text');
            if (!in_array($type, ['text', 'textarea', 'number', 'date', 'select'], true)) {
                $type = 'text';
            }

            $options = collect(preg_split('/(\r\n|\r|\n|,)/', (string) data_get($field, 'options', '')) ?: [])
                ->map(fn ($value) => trim((string) $value))
                ->filter()
                ->unique()
                ->values()
                ->all();

            $normalized[] = [
                'key' => $key,
                'label' => trim((string) data_get($field, 'label', '')) ?: Str::headline($key),
                'type' => $type,
                'placeholder' => trim((string) data_get($field, 'placeholder', '')),
                'required' => (bool) data_get($field, 'required', false),
                'options' => $options,
            ];
        }

        return $normalized;
    }
}
