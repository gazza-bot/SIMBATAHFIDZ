<?php

namespace App\Models;

use Database\Factories\UstadzFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ustadz extends Model
{
    /** @use HasFactory<UstadzFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nip',
        'nama',
        'telepon',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelompok(): HasMany
    {
        return $this->hasMany(Kelompok::class);
    }

    public function setorans(): HasMany
    {
        return $this->hasMany(Setoran::class);
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }
}