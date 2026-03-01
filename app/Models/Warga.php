<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Warga extends Model
{
    use HasUlids;

    protected $table = 'warga';

    protected $fillable = [
        'nik',
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
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    protected static function booted(): void
    {
        static::saved(function (Warga $warga): void {
            $warga->syncUserLogin();
        });

        static::deleting(function (Warga $warga): void {
            $warga->user()->delete();
        });
    }

    public function user()
    {
        return $this->hasOne(User::class, 'warga_id');
    }

    public function pengaduan()
    {
        return $this->hasMany(Pengaduan::class, 'warga_id');
    }

    public function syncUserLogin(): void
    {
        $user = $this->user()->first();

        if (!$user) {
            $user = User::query()
                ->where('role', 'warga')
                ->where(function ($query) {
                    $query->where('warga_id', $this->id)
                        ->orWhere('username', $this->nik);
                })
                ->first();
        }

        if (!$user) {
            User::create([
                'name' => $this->nama,
                'username' => $this->nik,
                'password' => Hash::make($this->defaultPassword()),
                'role' => 'warga',
                'warga_id' => $this->id,
            ]);

            return;
        }

        $payload = [
            'name' => $this->nama,
            'username' => $this->nik,
            'role' => 'warga',
            'warga_id' => $this->id,
        ];

        if (empty($user->password)) {
            $payload['password'] = Hash::make($this->defaultPassword());
        }

        $user->update($payload);
    }

    private function defaultPassword(): string
    {
        $tanggalLahir = $this->tanggal_lahir?->format('dmY');

        if (!empty($tanggalLahir)) {
            return $tanggalLahir;
        }

        return substr($this->nik, -6);
    }
}
