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
        Schema::create('pengajuan_histories', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('pengajuan_surat_id')->references('id')->on('pengajuan_surat')->onDelete('cascade');
            $table->foreignUlid('user_id')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->text('deskripsi');
            $table->timestamps();

            $table->index(['pengajuan_surat_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_histories');
    }
};
