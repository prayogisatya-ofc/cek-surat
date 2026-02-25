<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuan_surat', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('warga_id')->references('id')->on('warga')->onDelete('cascade');
            $table->string('jenis_surat', 100);
            $table->string('judul_surat', 191);
            $table->enum('status', ['Diterima', 'Diproses', 'Ditolak', 'Selesai'])->default('Diterima');
            $table->timestamps();

            $table->index(['warga_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_surat');
    }
};
