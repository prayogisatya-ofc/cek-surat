<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class PengajuanHistory extends Model
{
    use HasUlids;
    
    protected $fillable = [
        'pengajuan_surat_id',
        'user_id',
        'deskripsi',
    ];

    public function pengajuanSurat()
    {
        return $this->belongsTo(PengajuanSurat::class, 'pengajuan_surat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
