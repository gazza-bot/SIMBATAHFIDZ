<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JuzStart extends Model
{
    protected $fillable = [
        'juz',
        'surah_id',
        'ayat',
    ];

    public function surah(): BelongsTo
    {
        return $this->belongsTo(Surah::class);
    }
}