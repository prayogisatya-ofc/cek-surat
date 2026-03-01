<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithColumnLimit;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class WargaImport implements ToCollection, WithHeadingRow, WithColumnLimit
{
    private const DEFAULT_KODE_DESA = '18.06.02.2022';

    public function collection(Collection $rows)
    {
        $preparedWargaRows = [];
        $niks = [];
        $now = now();

        foreach ($rows as $row) {
            $nik  = trim((string) $this->firstValue($row, ['nik', 'NIK']));
            $nama = trim((string) $this->firstValue($row, ['nama', 'Nama']));
            $rawTgl = $this->firstValue($row, ['tanggal_lahir', 'Tanggal_Lahir']);

            if ($nik === '' || $nama === '' || $rawTgl === null || $rawTgl === '') {
                continue;
            }

            $tanggalLahir = $this->parseTanggalLahir($rawTgl);
            if (!$tanggalLahir) {
                continue;
            }

            $preparedWargaRows[] = [
                'nik' => $nik,
                'nomor_kk' => $this->normalizeText($this->firstValue($row, ['no_kk', 'No_KK'])),
                'nama' => $nama,
                'tanggal_lahir' => $tanggalLahir->format('Y-m-d'),
                'rt' => $this->normalizeText($this->firstValue($row, ['nama_rt', 'Nama_RT'])),
                'rw' => $this->normalizeText($this->firstValue($row, ['nama_rw', 'Nama_RW'])),
                'nama_dusun' => $this->normalizeText($this->firstValue($row, ['nama_dusun', 'Nama_Dusun'])),
                'jenis_kelamin' => $this->normalizeText($this->firstValue($row, ['jenis_kelamin', 'Jenis_Kelamin'])),
                'tempat_lahir' => $this->normalizeText($this->firstValue($row, ['tempat_lahir', 'Tempat_Lahir'])),
                'kode_desa' => $this->normalizeText($this->firstValue($row, ['kode_desa', 'Kode_Desa'])) ?? self::DEFAULT_KODE_DESA,
                'agama' => $this->normalizeText($this->firstValue($row, ['agama', 'Agama'])),
                'pekerjaan' => $this->normalizeText($this->firstValue($row, ['pekerjaan', 'Pekerjaan'])),
                'pendidikan' => $this->normalizeText($this->firstValue($row, ['pendidikan_kk', 'Pendidikan_KK'])),
                'status_kawin' => $this->normalizeText($this->firstValue($row, ['status_kawin', 'Status_Kawin'])),
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $niks[] = $nik;
        }

        if (empty($preparedWargaRows)) {
            return;
        }

        $uniqueNiks = array_values(array_unique($niks));
        DB::transaction(function () use ($preparedWargaRows, $uniqueNiks, $now) {
            $wargaChunks = array_chunk($preparedWargaRows, 400);

            Warga::withoutEvents(function () use ($wargaChunks) {
                foreach ($wargaChunks as $chunkRows) {
                    Warga::query()->upsert(
                        $chunkRows,
                        ['nik'],
                        [
                            'nomor_kk',
                            'nama',
                            'tanggal_lahir',
                            'rt',
                            'rw',
                            'nama_dusun',
                            'jenis_kelamin',
                            'tempat_lahir',
                            'kode_desa',
                            'agama',
                            'pekerjaan',
                            'pendidikan',
                            'status_kawin',
                            'updated_at',
                        ]
                    );
                }
            });

            $wargaByNik = Warga::query()
                ->whereIn('nik', $uniqueNiks)
                ->get(['id', 'nik', 'nama', 'tanggal_lahir'])
                ->keyBy('nik');

            $existingUsers = User::query()
                ->whereIn('username', $uniqueNiks)
                ->get(['id', 'username', 'role', 'password'])
                ->keyBy('username');

            $usersToUpsert = [];
            $usersToInsert = [];

            foreach ($wargaByNik as $nik => $warga) {
                $existingUser = $existingUsers->get($nik);
                if ($existingUser) {
                    if ($existingUser->role !== 'warga') {
                        continue;
                    }

                    $usersToUpsert[] = [
                        'username' => $nik,
                        'name' => $warga->nama,
                        'password' => $existingUser->password,
                        'role' => 'warga',
                        'warga_id' => $warga->id,
                        'updated_at' => $now,
                    ];

                    continue;
                }

                $usersToInsert[] = [
                    'id' => (string) Str::ulid(),
                    'name' => $warga->nama,
                    'username' => $nik,
                    'password' => Hash::make($this->defaultPasswordForWarga($warga), ['rounds' => 4]),
                    'role' => 'warga',
                    'warga_id' => $warga->id,
                    'remember_token' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (!empty($usersToUpsert)) {
                foreach (array_chunk($usersToUpsert, 500) as $chunkRows) {
                    User::query()->upsert(
                        $chunkRows,
                        ['username'],
                        ['name', 'role', 'warga_id', 'updated_at']
                    );
                }
            }

            if (!empty($usersToInsert)) {
                foreach (array_chunk($usersToInsert, 500) as $chunkRows) {
                    User::query()->insert($chunkRows);
                }
            }
        });
    }

    private function firstValue($row, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && $row[$key] !== '') {
                return $row[$key];
            }
        }

        return null;
    }

    private function normalizeText($value): ?string
    {
        $normalized = trim((string) $value);

        if ($normalized === '' || $normalized === '-') {
            return null;
        }

        return $normalized;
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

    private function defaultPasswordForWarga(Warga $warga): string
    {
        if (!empty($warga->tanggal_lahir)) {
            return Carbon::parse($warga->tanggal_lahir)->format('dmY');
        }

        return substr((string) $warga->nik, -6);
    }

    public function endColumn(): string
    {
        return 'V';
    }
}
