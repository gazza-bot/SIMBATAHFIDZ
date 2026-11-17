<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ustadz_id')->constrained('ustadzs')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('sesi', ['subuh', 'ashar', 'maghrib'])->default('ashar');
            $table->time('waktu_tiba')->nullable();
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa']);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['santri_id', 'tanggal', 'sesi']);
            $table->index(['ustadz_id', 'tanggal']);
            $table->index(['tanggal', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};