<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simaan_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ustadz_id')->constrained('ustadzs')->cascadeOnDelete();
            $table->date('tanggal')->index();
            $table->enum('status', ['dijadwalkan', 'selesai'])->default('dijadwalkan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simaan_schedules');
    }
};