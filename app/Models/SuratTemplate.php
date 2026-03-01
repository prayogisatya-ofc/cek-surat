<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class SuratTemplate extends Model
{
    use HasUlids;

    protected $fillable = [
        'nama',
        'nomor_jenis',
        'deskripsi',
        'template_path',
        'placeholders',
        'custom_fields',
        'nomor_surat_format',
        'is_active',
    ];

    protected $casts = [
        'placeholders' => 'array',
        'custom_fields' => 'array',
        'is_active' => 'boolean',
    ];

    public function pengajuan()
    {
        return $this->hasMany(PengajuanSurat::class, 'surat_template_id');
    }
}
