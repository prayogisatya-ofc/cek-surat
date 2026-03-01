<?php

namespace Database\Seeders;

use App\Models\Warga;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nik' => '1805130707030006', 'nomor_kk' => '1805130101010001', 'nama' => 'Budi Santoso',   'tanggal_lahir' => '2003-07-07', 'rt' => '01', 'rw' => '01', 'nama_dusun' => 'Kebun Kelapa', 'jenis_kelamin' => 'LAKI-LAKI', 'tempat_lahir' => 'Bandar Lampung', 'kode_desa' => '18.06.02.2022', 'agama' => 'ISLAM', 'pekerjaan' => 'Pelajar/Mahasiswa', 'pendidikan' => 'SEDANG SLTA/SEDERAJAT', 'status_kawin' => 'BELUM KAWIN'],
            ['nik' => '1805131201990002', 'nomor_kk' => '1805130101010002', 'nama' => 'Siti Aisyah',    'tanggal_lahir' => '1999-01-12', 'rt' => '01', 'rw' => '01', 'nama_dusun' => 'Pardasuka', 'jenis_kelamin' => 'PEREMPUAN', 'tempat_lahir' => 'Tanggamus', 'kode_desa' => '18.06.02.2022', 'agama' => 'ISLAM', 'pekerjaan' => 'Ibu Rumah Tangga', 'pendidikan' => 'SLTA/SEDERAJAT', 'status_kawin' => 'KAWIN TERCATAT'],
            ['nik' => '1805132308000003', 'nomor_kk' => '1805130101010003', 'nama' => 'Ahmad Fauzi',    'tanggal_lahir' => '2000-08-23', 'rt' => '02', 'rw' => '01', 'nama_dusun' => 'Banding Agung', 'jenis_kelamin' => 'LAKI-LAKI', 'tempat_lahir' => 'Banding Agung', 'kode_desa' => '18.06.02.2022', 'agama' => 'ISLAM', 'pekerjaan' => 'Wiraswasta', 'pendidikan' => 'SLTA/SEDERAJAT', 'status_kawin' => 'BELUM KAWIN'],
            ['nik' => '1805130410020004', 'nomor_kk' => '1805130101010004', 'nama' => 'Rina Oktaviani', 'tanggal_lahir' => '2002-10-04', 'rt' => '02', 'rw' => '01', 'nama_dusun' => 'Banding Agung', 'jenis_kelamin' => 'PEREMPUAN', 'tempat_lahir' => 'Bandar Lampung', 'kode_desa' => '18.06.02.2022', 'agama' => 'ISLAM', 'pekerjaan' => 'Pelajar/Mahasiswa', 'pendidikan' => 'SEDANG KULIAH', 'status_kawin' => 'BELUM KAWIN'],
            ['nik' => '1805131505950005', 'nomor_kk' => '1805130101010005', 'nama' => 'Dedi Pratama',   'tanggal_lahir' => '1995-05-15', 'rt' => '03', 'rw' => '01', 'nama_dusun' => 'Pardasuka', 'jenis_kelamin' => 'LAKI-LAKI', 'tempat_lahir' => 'Talang Padang', 'kode_desa' => '18.06.02.2022', 'agama' => 'ISLAM', 'pekerjaan' => 'Petani', 'pendidikan' => 'SLTP/SEDERAJAT', 'status_kawin' => 'KAWIN TERCATAT'],
            ['nik' => '1805130207880006', 'nomor_kk' => '1805130101010006', 'nama' => 'Nurul Hidayah',  'tanggal_lahir' => '1988-07-02', 'rt' => '03', 'rw' => '01', 'nama_dusun' => 'Kebun Kelapa', 'jenis_kelamin' => 'PEREMPUAN', 'tempat_lahir' => 'Banding Agung', 'kode_desa' => '18.06.02.2022', 'agama' => 'ISLAM', 'pekerjaan' => 'Pedagang', 'pendidikan' => 'SLTA/SEDERAJAT', 'status_kawin' => 'KAWIN TERCATAT'],
            ['nik' => '1805133009010007', 'nomor_kk' => '1805130101010007', 'nama' => 'Andi Saputra',   'tanggal_lahir' => '2001-09-30', 'rt' => '04', 'rw' => '02', 'nama_dusun' => 'Banding Agung', 'jenis_kelamin' => 'LAKI-LAKI', 'tempat_lahir' => 'Banding Agung', 'kode_desa' => '18.06.02.2022', 'agama' => 'ISLAM', 'pekerjaan' => 'Karyawan Swasta', 'pendidikan' => 'D3', 'status_kawin' => 'BELUM KAWIN'],
            ['nik' => '1805131106960008', 'nomor_kk' => '1805130101010008', 'nama' => 'Dewi Lestari',   'tanggal_lahir' => '1996-06-11', 'rt' => '04', 'rw' => '02', 'nama_dusun' => 'Pardasuka', 'jenis_kelamin' => 'PEREMPUAN', 'tempat_lahir' => 'Banding Agung', 'kode_desa' => '18.06.02.2022', 'agama' => 'ISLAM', 'pekerjaan' => 'Guru', 'pendidikan' => 'S1', 'status_kawin' => 'KAWIN TERCATAT'],
            ['nik' => '1805132503940009', 'nomor_kk' => '1805130101010009', 'nama' => 'Rizki Ramadhan', 'tanggal_lahir' => '1994-03-25', 'rt' => '05', 'rw' => '02', 'nama_dusun' => 'Kebun Kelapa', 'jenis_kelamin' => 'LAKI-LAKI', 'tempat_lahir' => 'Talang Padang', 'kode_desa' => '18.06.02.2022', 'agama' => 'ISLAM', 'pekerjaan' => 'Wiraswasta', 'pendidikan' => 'SLTA/SEDERAJAT', 'status_kawin' => 'KAWIN TERCATAT'],
            ['nik' => '1805130107010010', 'nomor_kk' => '1805130101010010', 'nama' => 'Putri Maharani', 'tanggal_lahir' => '2001-07-01', 'rt' => '05', 'rw' => '02', 'nama_dusun' => 'Banding Agung', 'jenis_kelamin' => 'PEREMPUAN', 'tempat_lahir' => 'Banding Agung', 'kode_desa' => '18.06.02.2022', 'agama' => 'ISLAM', 'pekerjaan' => 'Mahasiswa', 'pendidikan' => 'SEDANG KULIAH', 'status_kawin' => 'BELUM KAWIN'],
        ];

        foreach ($data as $row) {
            Warga::updateOrCreate(
                ['nik' => $row['nik']],
                $row
            );
        }
    }
}
