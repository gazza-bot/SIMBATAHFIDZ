<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('setorans', function (Blueprint $table) {
            $table->enum('jenis', ['ziyadah', 'murajaah', 'tasmi'])->default('ziyadah')->change();
            $table->unsignedTinyInteger('skor')->nullable()->after('nilai');
            $table->boolean('tuntas_lanjut')->default(false)->after('skor');
            $table->boolean('is_draft')->default(false)->after('tuntas_lanjut');
        });
    }

    public function down(): void
    {
        Schema::table('setorans', function (Blueprint $table) {
            $table->dropColumn(['skor', 'tuntas_lanjut', 'is_draft']);
            $table->enum('jenis', ['ziyadah', 'murajaah'])->default('ziyadah')->change();
        });
    }
};