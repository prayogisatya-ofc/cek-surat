<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('warga')->after('password');
            $table->foreignUlid('warga_id')->nullable()->unique()->after('role')
                ->constrained('warga')->nullOnDelete();
        });

        Schema::table('warga', function (Blueprint $table) {
            $table->string('nomor_kk', 20)->nullable()->after('nik');
            $table->string('rt', 10)->nullable()->after('nomor_kk');
            $table->string('rw', 10)->nullable()->after('rt');
            $table->string('nama_dusun', 150)->nullable()->after('rw');
            $table->string('jenis_kelamin', 20)->nullable()->after('tanggal_lahir');
            $table->string('tempat_lahir', 150)->nullable()->after('jenis_kelamin');
            $table->string('kode_desa', 50)->nullable()->after('tempat_lahir');
            $table->string('agama', 50)->nullable()->after('kode_desa');
            $table->string('pekerjaan', 150)->nullable()->after('agama');
            $table->string('pendidikan', 150)->nullable()->after('pekerjaan');
            $table->string('status_kawin', 80)->nullable()->after('pendidikan');
        });

        DB::table('users')->update(['role' => 'admin']);

        $now = now();
        $wargaRows = DB::table('warga')
            ->select('id', 'nik', 'nama', 'tanggal_lahir')
            ->get();

        foreach ($wargaRows as $warga) {
            $defaultPassword = !empty($warga->tanggal_lahir)
                ? \Carbon\Carbon::parse($warga->tanggal_lahir)->format('dmY')
                : substr((string) $warga->nik, -6);

            $existingWargaUser = DB::table('users')
                ->where('warga_id', $warga->id)
                ->orWhere(function ($query) use ($warga) {
                    $query->where('username', $warga->nik)->where('role', 'warga');
                })
                ->first();

            if ($existingWargaUser) {
                DB::table('users')
                    ->where('id', $existingWargaUser->id)
                    ->update([
                        'name' => $warga->nama,
                        'username' => $warga->nik,
                        'role' => 'warga',
                        'warga_id' => $warga->id,
                        'updated_at' => $now,
                    ]);

                continue;
            }

            if (DB::table('users')->where('username', $warga->nik)->exists()) {
                continue;
            }

            DB::table('users')->insert([
                'id' => (string) Str::ulid(),
                'name' => $warga->nama,
                'username' => $warga->nik,
                'password' => Hash::make($defaultPassword),
                'role' => 'warga',
                'warga_id' => $warga->id,
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warga', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_kk',
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
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('warga_id');
            $table->dropColumn('role');
        });
    }
};
