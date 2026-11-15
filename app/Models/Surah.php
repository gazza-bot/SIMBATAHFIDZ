<?php

namespace App\Models;

use Database\Factories\SurahFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Surah extends Model
{
    /** @use HasFactory<SurahFactory> */
    use HasFactory;

    protected $fillable = [
        'nomor',
        'nama_latin',
        'nama_arab',
        'jumlah_ayat',
        'juz_mulai',
        'juz_selesai',
        'tempat_turun',
    ];

    protected $casts = [
        'nomor' => 'integer',
        'jumlah_ayat' => 'integer',
        'juz_mulai' => 'integer',
        'juz_selesai' => 'integer',
    ];

    public function setorans(): HasMany
    {
        return $this->hasMany(Setoran::class);
    }

    public function getJuzRangeAttribute(): string
    {
        return $this->juz_mulai === $this->juz_selesai
            ? "Juz {$this->juz_mulai}"
            : "Juz {$this->juz_mulai}-{$this->juz_selesai}";
    }
}