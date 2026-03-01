<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class PengajuanSurat extends Model
{
    use HasUlids;

    protected $table = 'pengajuan_surat';

    protected $fillable = [
        'warga_id',
        'surat_template_id',
        'nomor_urut_jenis',
        'nomor_surat',
        'jenis_surat',
        'judul_surat',
        'field_values',
        'generated_docx_path',
        'signed_pdf_path',
        'status',
    ];

    protected $casts = [
        'field_values' => 'array',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class, 'warga_id');
    }

    public function suratTemplate()
    {
        return $this->belongsTo(SuratTemplate::class, 'surat_template_id');
    }

    public function histories()
    {
        return $this->hasMany(PengajuanHistory::class, 'pengajuan_surat_id')->latest();
    }
}
