<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelompoks', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // contoh: Halaqah A, Halaqah B
            $table->foreignId('ustadz_id')->constrained('ustadzs')->cascadeOnDelete();
            $table->foreignId('angkatan_id')->constrained()->cascadeOnDelete();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelompoks');
    }
};