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
        'jenis_surat',
        'judul_surat',
        'status',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class, 'warga_id');
    }

    public function histories()
    {
        return $this->hasMany(PengajuanHistory::class, 'pengajuan_surat_id')->latest();
    }
}
