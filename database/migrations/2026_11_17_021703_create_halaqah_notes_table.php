<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('halaqah_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ustadz_id')->constrained('ustadzs')->cascadeOnDelete();
            $table->date('tanggal')->index();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['kelompok_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('halaqah_notes');
    }
};