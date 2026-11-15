<?php

namespace App\Models;

use Database\Factories\KelompokFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelompok extends Model
{
    /** @use HasFactory<KelompokFactory> */
    use HasFactory;

    protected $fillable = [
        'nama',
        'ustadz_id',
        'angkatan_id',
        'deskripsi',
    ];

    public function ustadz(): BelongsTo
    {
        return $this->belongsTo(Ustadz::class);
    }

    public function angkatan(): BelongsTo
    {
        return $this->belongsTo(Angkatan::class);
    }

    public function santris(): HasMany
    {
        return $this->hasMany(Santri::class);
    }
}