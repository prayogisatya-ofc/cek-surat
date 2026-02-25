<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Warga extends Model
{
    use HasUlids;

    protected $table = 'warga';

    protected $fillable = [
        'nik',
        'nama',
        'tanggal_lahir',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];
}
