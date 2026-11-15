<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setorans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ustadz_id')->constrained('ustadzs')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('jenis', ['ziyadah', 'murajaah']);
            $table->unsignedTinyInteger('juz'); // 1-30
            $table->foreignId('surah_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('ayat_awal');
            $table->unsignedSmallInteger('ayat_akhir');
            $table->enum('nilai', ['maqbul', 'jayyid', 'jayyid_jiddan', 'mumtaz']);
            $table->text('catatan')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();

            $table->index(['santri_id', 'tanggal']);
            $table->index(['ustadz_id', 'tanggal']);
            $table->index(['jenis', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setorans');
    }
};