<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surahs', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('nomor')->unique(); // 1-114
            $table->string('nama_latin'); // Al-Fatihah
            $table->string('nama_arab');  // الفاتحة
            $table->unsignedSmallInteger('jumlah_ayat');
            $table->unsignedTinyInteger('juz_mulai');
            $table->unsignedTinyInteger('juz_selesai');
            $table->enum('tempat_turun', ['Mekkah', 'Madinah']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surahs');
    }
};