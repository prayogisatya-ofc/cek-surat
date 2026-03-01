<?php

namespace App\Services;

use App\Models\PengajuanSurat;
use App\Models\SuratTemplate;
use App\Models\Warga;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

class SuratGeneratorService
{
    /**
     * Placeholder yang otomatis tersedia dari data warga / sistem.
     *
     * @return array<string, string>
     */
    public function knownPlaceholderLabels(): array
    {
        return [
            'nama' => 'Nama Warga',
            'nik' => 'NIK',
            'nomor_kk' => 'Nomor KK',
            'rt' => 'RT',
            'rw' => 'RW',
            'nama_dusun' => 'Nama Dusun',
            'jenis_kelamin' => 'Jenis Kelamin',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'kode_desa' => 'Kode Desa',
            'agama' => 'Agama',
            'pekerjaan' => 'Pekerjaan',
            'pendidikan' => 'Pendidikan',
            'status_kawin' => 'Status Kawin',
            'tanggal_pengajuan' => 'Tanggal Pengajuan',
            'tanggal_surat' => 'Tanggal Surat',
            'nomor_surat' => 'Nomor Surat',
            'nomor_jenis' => 'Nomor Jenis Surat',
            'nomor_urut' => 'Nomor Urut',
            'nomor_urut_jenis' => 'Nomor Urut Jenis',
            'nomor_urut_padded' => 'Nomor Urut (3 Digit)',
            'urutan_surat' => 'Urutan Surat',
            'bulan' => 'Bulan',
            'bulan_romawi' => 'Bulan Romawi',
            'bulan_angka' => 'Bulan Angka',
            'tahun' => 'Tahun',
        ];
    }

    /**
     * @return array<int, string>
     */
    public function extractTemplatePlaceholders(string $templatePath): array
    {
        $absolutePath = Storage::disk('local')->path($templatePath);
        if (!is_file($absolutePath)) {
            return [];
        }

        $processor = new TemplateProcessor($absolutePath);
        $variables = collect($processor->getVariables())
            ->map(fn ($var) => trim((string) $var))
            ->filter()
            ->unique()
            ->values()
            ->all();

        sort($variables);

        return $variables;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function resolveDynamicFields(SuratTemplate $template): array
    {
        $knownKeys = array_keys($this->knownPlaceholderLabels());
        $knownLookup = array_flip($knownKeys);

        $fields = [];
        $customFields = collect($template->custom_fields ?? []);

        foreach ($customFields as $custom) {
            $key = $this->normalizeFieldKey((string) data_get($custom, 'key', ''));
            if ($key === '' || isset($knownLookup[$key])) {
                continue;
            }

            $fields[$key] = [
                'key' => $key,
                'label' => trim((string) data_get($custom, 'label', '')) ?: Str::headline($key),
                'type' => $this->normalizeFieldType((string) data_get($custom, 'type', 'text')),
                'required' => (bool) data_get($custom, 'required', false),
                'placeholder' => trim((string) data_get($custom, 'placeholder', '')),
                'options' => $this->normalizeOptions(data_get($custom, 'options', [])),
            ];
        }

        foreach (($template->placeholders ?? []) as $placeholder) {
            $key = $this->normalizeFieldKey((string) $placeholder);
            if ($key === '' || isset($knownLookup[$key]) || isset($fields[$key])) {
                continue;
            }

            $fields[$key] = [
                'key' => $key,
                'label' => Str::headline($key),
                'type' => 'text',
                'required' => true,
                'placeholder' => 'Isi ' . Str::headline($key),
                'options' => [],
            ];
        }

        return array_values($fields);
    }

    /**
     * @param array<int, array<string, mixed>> $fields
     * @return array<string, array<int, string>>
     */
    public function dynamicValidationRules(array $fields): array
    {
        $rules = [];

        foreach ($fields as $field) {
            $key = (string) data_get($field, 'key', '');
            if ($key === '') {
                continue;
            }

            $fieldRules = [];
            $fieldRules[] = data_get($field, 'required') ? 'required' : 'nullable';

            $type = (string) data_get($field, 'type', 'text');
            if ($type === 'number') {
                $fieldRules[] = 'numeric';
            } elseif ($type === 'date') {
                $fieldRules[] = 'date';
            } else {
                $fieldRules[] = 'string';
                $fieldRules[] = 'max:500';
            }

            $options = $this->normalizeOptions(data_get($field, 'options', []));
            if ($type === 'select' && !empty($options)) {
                $escaped = array_map(fn (string $opt) => str_replace(',', '\,', $opt), $options);
                $fieldRules[] = 'in:' . implode(',', $escaped);
            }

            $rules['fields.' . $key] = $fieldRules;
        }

        return $rules;
    }

    /**
     * @param array<int, array<string, mixed>> $fields
     * @param array<string, mixed> $input
     * @return array<string, string>
     */
    public function normalizeDynamicValues(array $fields, array $input): array
    {
        $values = [];

        foreach ($fields as $field) {
            $key = (string) data_get($field, 'key', '');
            if ($key === '') {
                continue;
            }

            $value = $input[$key] ?? '';
            $values[$key] = is_scalar($value) ? trim((string) $value) : '';
        }

        return $values;
    }

    public function generateNomorSurat(
        SuratTemplate $template,
        Warga $warga,
        int $sequence,
        CarbonInterface $tanggal
    ): string {
        $format = trim((string) $template->nomor_surat_format);
        if ($format === '') {
            $format = '${nomor_urut_padded}/${nomor_jenis}/${kode_desa}/${bulan_romawi}/${tahun}';
        }

        $replacements = [
            'nomor_urut' => (string) $sequence,
            'nomor_urut_jenis' => (string) $sequence,
            'nomor_urut_padded' => str_pad((string) $sequence, 3, '0', STR_PAD_LEFT),
            'urutan_surat' => (string) $sequence,
            'nomor_jenis' => (string) $template->nomor_jenis,
            'kode_desa' => (string) ($warga->kode_desa ?? ''),
            'bulan' => $tanggal->format('m'),
            'bulan_angka' => $tanggal->format('m'),
            'bulan_romawi' => $this->toRomanMonth((int) $tanggal->format('n')),
            'tahun' => $tanggal->format('Y'),
        ];

        $result = $format;
        foreach ($replacements as $key => $value) {
            $result = str_replace(['${' . $key . '}', '{' . $key . '}'], $value, $result);
        }

        $result = preg_replace('/\$\{[^}]+\}/', '', $result) ?? $result;
        $result = preg_replace('/\s{2,}/', ' ', $result) ?? $result;
        $result = str_replace('//', '/', $result);

        return trim($result, " \t\n\r\0\x0B/");
    }

    /**
     * @param array<string, mixed> $fieldValues
     * @return array<string, string>
     */
    public function buildPlaceholderValues(
        Warga $warga,
        SuratTemplate $template,
        int $sequence,
        string $nomorSurat,
        array $fieldValues,
        CarbonInterface $tanggal
    ): array {
        $base = [
            'nama' => (string) $warga->nama,
            'nik' => (string) $warga->nik,
            'nomor_kk' => (string) ($warga->nomor_kk ?? ''),
            'rt' => (string) ($warga->rt ?? ''),
            'rw' => (string) ($warga->rw ?? ''),
            'nama_dusun' => (string) ($warga->nama_dusun ?? ''),
            'jenis_kelamin' => (string) ($warga->jenis_kelamin ?? ''),
            'tempat_lahir' => (string) ($warga->tempat_lahir ?? ''),
            'tanggal_lahir' => $warga->tanggal_lahir?->format('d-m-Y') ?? '',
            'kode_desa' => (string) ($warga->kode_desa ?? ''),
            'agama' => (string) ($warga->agama ?? ''),
            'pekerjaan' => (string) ($warga->pekerjaan ?? ''),
            'pendidikan' => (string) ($warga->pendidikan ?? ''),
            'status_kawin' => (string) ($warga->status_kawin ?? ''),
            'tanggal_pengajuan' => $tanggal->format('d-m-Y'),
            'tanggal_surat' => $tanggal->format('d-m-Y'),
            'nomor_surat' => $nomorSurat,
            'nomor_jenis' => (string) $template->nomor_jenis,
            'nomor_urut' => (string) $sequence,
            'nomor_urut_jenis' => (string) $sequence,
            'nomor_urut_padded' => str_pad((string) $sequence, 3, '0', STR_PAD_LEFT),
            'urutan_surat' => (string) $sequence,
            'bulan' => $tanggal->format('m'),
            'bulan_angka' => $tanggal->format('m'),
            'bulan_romawi' => $this->toRomanMonth((int) $tanggal->format('n')),
            'tahun' => $tanggal->format('Y'),
        ];

        $dynamic = collect($fieldValues)
            ->mapWithKeys(fn ($value, $key) => [(string) $key => is_scalar($value) ? (string) $value : ''])
            ->all();

        return array_merge($base, $dynamic);
    }

    public function generateDocx(PengajuanSurat $pengajuan): string
    {
        $pengajuan->loadMissing('warga', 'suratTemplate');

        $template = $pengajuan->suratTemplate;
        $warga = $pengajuan->warga;

        if (!$template || !$warga) {
            throw new \RuntimeException('Template atau data warga tidak ditemukan.');
        }

        $templatePath = Storage::disk('local')->path($template->template_path);
        if (!is_file($templatePath)) {
            throw new \RuntimeException('File template DOCX tidak ditemukan di storage.');
        }

        $processor = new TemplateProcessor($templatePath);

        $tanggal = $pengajuan->created_at ?? now();
        $values = $this->buildPlaceholderValues(
            $warga,
            $template,
            (int) $pengajuan->nomor_urut_jenis,
            (string) $pengajuan->nomor_surat,
            $pengajuan->field_values ?? [],
            $tanggal
        );

        foreach (($template->placeholders ?? []) as $placeholder) {
            $key = trim((string) $placeholder);
            if ($key === '') {
                continue;
            }

            $normalizedKey = $this->normalizeFieldKey($key);
            $value = $values[$key]
                ?? $values[$normalizedKey]
                ?? $values[Str::lower($key)]
                ?? '';

            $processor->setValue($key, $value);
        }

        $outputPath = 'surat/generated/' . $pengajuan->id . '.docx';
        Storage::disk('local')->makeDirectory(dirname($outputPath));
        $absoluteOutputPath = Storage::disk('local')->path($outputPath);
        $processor->saveAs($absoluteOutputPath);

        return $outputPath;
    }

    private function normalizeFieldType(string $type): string
    {
        return in_array($type, ['text', 'textarea', 'number', 'date', 'select'], true)
            ? $type
            : 'text';
    }

    /**
     * @param mixed $value
     * @return array<int, string>
     */
    private function normalizeOptions(mixed $value): array
    {
        if (is_array($value)) {
            return collect($value)
                ->map(fn ($item) => trim((string) $item))
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        if (!is_string($value)) {
            return [];
        }

        return collect(preg_split('/(\r\n|\r|\n|,)/', $value) ?: [])
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function normalizeFieldKey(string $key): string
    {
        $clean = preg_replace('/[^a-zA-Z0-9_]/', '_', trim($key)) ?? '';
        $clean = trim($clean, '_');

        return Str::lower($clean);
    }

    private function toRomanMonth(int $month): string
    {
        $map = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        return $map[$month] ?? '';
    }
}
