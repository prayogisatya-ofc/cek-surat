<?php

namespace App\Imports;

use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class WargaImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $nik  = trim((string) ($row['nik'] ?? ''));
            $nama = trim((string) ($row['nama'] ?? ''));
            $rawTgl = $row['tanggal_lahir'] ?? null;

            if ($nik === '' || $nama === '' || $rawTgl === null || $rawTgl === '') {
                continue;
            }

            $tanggalLahir = $this->parseTanggalLahir($rawTgl);
            if (!$tanggalLahir) {
                continue;
            }

            Warga::updateOrCreate(
                ['nik' => $nik],
                [
                    'nama' => $nama,
                    'tanggal_lahir' => $tanggalLahir->format('Y-m-d'),
                ]
            );
        }
    }

    private function parseTanggalLahir($value): ?Carbon
    {
        if (is_numeric($value)) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject($value))->startOfDay();
            } catch (\Throwable $e) {
                return null;
            }
        }

        $str = trim((string) $value);

        $formats = [
            'n/j/Y g:i:s A',   // 11/7/1999 12:00:00 AM
            'm/d/Y g:i:s A',   // 11/17/1999 12:00:00 AM
            'm/d/Y H:i:s',     // 11/17/1999 00:00:00
            'm/d/Y',           // 11/17/1999
            'Y-m-d',           // 1999-11-17
            'd/m/Y',           // 17/11/1999 (kalau ada yang format indo)
        ];

        foreach ($formats as $fmt) {
            try {
                $dt = Carbon::createFromFormat($fmt, $str);
                if ($dt !== false) return $dt->startOfDay();
            } catch (\Throwable $e) {
            }
        }

        try {
            return Carbon::parse($str)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
