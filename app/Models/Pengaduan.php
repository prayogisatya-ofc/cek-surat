<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    use HasUlids;

    protected $table = 'pengaduan';

    protected $fillable = [
        'warga_id',
        'judul',
        'kategori',
        'isi_laporan',
        'lokasi',
        'kontak',
        'status',
        'tanggapan',
        'ditangani_oleh',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class, 'warga_id');
    }

    public function adminPenangan()
    {
        return $this->belongsTo(User::class, 'ditangani_oleh');
    }
}

