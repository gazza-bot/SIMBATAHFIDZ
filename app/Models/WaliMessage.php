<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaliMessage extends Model
{
    protected $fillable = [
        'wali_id',
        'santri_id',
        'pesan',
        'dibacakan',
        'dibacakan_at',
    ];

    protected $casts = [
        'dibacakan' => 'boolean',
        'dibacakan_at' => 'datetime',
    ];

    public function wali(): BelongsTo
    {
        return $this->belongsTo(Wali::class);
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }
}
