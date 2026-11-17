<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HalaqahNote extends Model
{
    protected $fillable = [
        'kelompok_id',
        'ustadz_id',
        'tanggal',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class);
    }

    public function ustadz(): BelongsTo
    {
        return $this->belongsTo(Ustadz::class);
    }
}