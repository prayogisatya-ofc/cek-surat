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
        Schema::create('warga', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('nik', 20)->unique();
            $table->string('nama', 150);
            $table->date('tanggal_lahir');
            $table->timestamps();

            $table->index(['nik', 'tanggal_lahir']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warga');
    }
};
