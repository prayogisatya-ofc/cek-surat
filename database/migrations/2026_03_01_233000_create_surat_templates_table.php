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
        Schema::create('surat_templates', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('nama', 150);
            $table->string('nomor_jenis', 50)->unique();
            $table->text('deskripsi')->nullable();
            $table->string('template_path', 191);
            $table->json('placeholders')->nullable();
            $table->json('custom_fields')->nullable();
            $table->string('nomor_surat_format', 191)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'nama']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_templates');
    }
};
