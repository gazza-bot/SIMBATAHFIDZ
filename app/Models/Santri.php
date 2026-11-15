<?php

namespace App\Models;

use Database\Factories\SantriFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Santri extends Model
{
    /** @use HasFactory<SantriFactory> */
    use HasFactory;

    protected $fillable = [
        'nis',
        'nama',
        'angkatan_id',
        'kelompok_id',
        'wali_id',
        'foto',
        'status_aktif',
        'tanggal_masuk',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
    ];

    public function angkatan(): BelongsTo
    {
        return $this->belongsTo(Angkatan::class);
    }

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class);
    }

    public function wali(): BelongsTo
    {
        return $this->belongsTo(Wali::class);
    }

    public function setorans(): HasMany
    {
        return $this->hasMany(Setoran::class);
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function getProgressJuzAttribute(): ?int
    {
        return $this->setorans()
            ->where('jenis', 'ziyadah')
            ->where('status', 'approved')
            ->max('juz');
    }

    public function getProgressPercentAttribute(): int
    {
        $juz = $this->progress_juz ?? 0;
        return min(100, round(($juz / 30) * 100));
    }

    public function getMuthuTerakhirAttribute(): ?string
    {
        return $this->setorans()
            ->where('status', 'approved')
            ->latest('tanggal')
            ->value('nilai');
    }
}