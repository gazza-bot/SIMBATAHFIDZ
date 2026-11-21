<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wali_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wali_id')->constrained()->cascadeOnDelete();
            $table->foreignId('santri_id')->constrained()->cascadeOnDelete();
            $table->text('pesan');
            $table->boolean('dibacakan')->default(false);
            $table->timestamp('dibacakan_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wali_messages');
    }
};
