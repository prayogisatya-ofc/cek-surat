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
        Schema::table('pengajuan_surat', function (Blueprint $table) {
            $table->foreignUlid('surat_template_id')
                ->nullable()
                ->after('warga_id')
                ->constrained('surat_templates')
                ->nullOnDelete();

            $table->unsignedInteger('nomor_urut_jenis')->nullable()->after('surat_template_id');
            $table->string('nomor_surat', 191)->nullable()->after('nomor_urut_jenis');
            $table->json('field_values')->nullable()->after('judul_surat');
            $table->string('generated_docx_path', 191)->nullable()->after('field_values');
            $table->string('signed_pdf_path', 191)->nullable()->after('generated_docx_path');

            $table->unique(['surat_template_id', 'nomor_urut_jenis'], 'pengajuan_surat_template_nomor_unique');
            $table->index(['surat_template_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_surat', function (Blueprint $table) {
            $table->dropUnique('pengajuan_surat_template_nomor_unique');
            $table->dropIndex(['surat_template_id', 'status']);
            $table->dropConstrainedForeignId('surat_template_id');
            $table->dropColumn([
                'nomor_urut_jenis',
                'nomor_surat',
                'field_values',
                'generated_docx_path',
                'signed_pdf_path',
            ]);
        });
    }
};
