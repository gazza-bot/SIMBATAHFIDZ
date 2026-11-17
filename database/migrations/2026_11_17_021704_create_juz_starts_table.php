<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('juz_starts', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('juz');
            $table->foreignId('surah_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('ayat');
            $table->timestamps();

            $table->unique('juz');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('juz_starts');
    }
};