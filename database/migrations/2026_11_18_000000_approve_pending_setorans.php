<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('setorans')
            ->where('status', 'pending')
            ->where('is_draft', false)
            ->orderBy('id')
            ->chunkById(100, function ($setorans) {
                foreach ($setorans as $setoran) {
                    $validatedBy = DB::table('ustadzs')
                        ->where('id', $setoran->ustadz_id)
                        ->value('user_id');

                    DB::table('setorans')
                        ->where('id', $setoran->id)
                        ->update([
                            'status' => 'approved',
                            'validated_by' => $validatedBy,
                            'validated_at' => $setoran->created_at ?? now(),
                        ]);
                }
            });
    }

    public function down(): void
    {
    }
};
