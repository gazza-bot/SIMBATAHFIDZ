<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('santris', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->unique();
            $table->string('nama');
            $table->foreignId('angkatan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kelompok_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('wali_id')->nullable()->constrained('walis')->nullOnDelete();
            $table->string('foto')->nullable();
            $table->enum('status_aktif', ['aktif', 'nonaktif', 'lulus', 'pindah'])->default('aktif');
            $table->date('tanggal_masuk')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('santris');
    }
};