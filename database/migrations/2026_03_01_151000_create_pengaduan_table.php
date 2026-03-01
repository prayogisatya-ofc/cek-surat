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
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('warga_id')->references('id')->on('warga')->onDelete('cascade');
            $table->string('judul', 191);
            $table->string('kategori', 100);
            $table->text('isi_laporan');
            $table->string('lokasi', 191)->nullable();
            $table->string('kontak', 50)->nullable();
            $table->enum('status', ['Baru', 'Diproses', 'Selesai', 'Ditolak'])->default('Baru');
            $table->text('tanggapan')->nullable();
            $table->foreignUlid('ditangani_oleh')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['warga_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};

