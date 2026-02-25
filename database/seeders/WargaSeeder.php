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
            ['nik' => '1805130707030006', 'nama' => 'Budi Santoso',        'tanggal_lahir' => '2003-07-07'],
            ['nik' => '1805131201990002', 'nama' => 'Siti Aisyah',         'tanggal_lahir' => '1999-01-12'],
            ['nik' => '1805132308000003', 'nama' => 'Ahmad Fauzi',         'tanggal_lahir' => '2000-08-23'],
            ['nik' => '1805130410020004', 'nama' => 'Rina Oktaviani',      'tanggal_lahir' => '2002-10-04'],
            ['nik' => '1805131505950005', 'nama' => 'Dedi Pratama',        'tanggal_lahir' => '1995-05-15'],
            ['nik' => '1805130207880006', 'nama' => 'Nurul Hidayah',       'tanggal_lahir' => '1988-07-02'],
            ['nik' => '1805133009010007', 'nama' => 'Andi Saputra',        'tanggal_lahir' => '2001-09-30'],
            ['nik' => '1805131106960008', 'nama' => 'Dewi Lestari',        'tanggal_lahir' => '1996-06-11'],
            ['nik' => '1805132503940009', 'nama' => 'Rizki Ramadhan',      'tanggal_lahir' => '1994-03-25'],
            ['nik' => '1805130107010010', 'nama' => 'Putri Maharani',      'tanggal_lahir' => '2001-07-01'],
        ];

        foreach ($data as $row) {
            Warga::updateOrCreate(
                ['nik' => $row['nik']],
                ['nama' => $row['nama'], 'tanggal_lahir' => $row['tanggal_lahir']]
            );
        }
    }
}
