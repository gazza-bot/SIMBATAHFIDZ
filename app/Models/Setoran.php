<?php

namespace App\Models;

use Database\Factories\SetoranFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setoran extends Model
{
    /** @use HasFactory<SetoranFactory> */
    use HasFactory;

    protected $fillable = [
        'santri_id',
        'ustadz_id',
        'tanggal',
        'jenis',
        'juz',
        'surah_id',
        'ayat_awal',
        'ayat_akhir',
        'nilai',
        'skor',
        'tuntas_lanjut',
        'is_draft',
        'catatan',
        'status',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'validated_at' => 'datetime',
        'tuntas_lanjut' => 'boolean',
        'is_draft' => 'boolean',
    ];

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    public function ustadz(): BelongsTo
    {
        return $this->belongsTo(Ustadz::class);
    }

    public function surah(): BelongsTo
    {
        return $this->belongsTo(Surah::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function scopeZiyadah($query)
    {
        return $query->where('jenis', 'ziyadah');
    }

    public function scopeMurajaah($query)
    {
        return $query->where('jenis', 'murajaah');
    }

    public function scopeTasmi($query)
    {
        return $query->where('jenis', 'tasmi');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeNotDraft($query)
    {
        return $query->where('is_draft', false);
    }

    public function getAyatRangeAttribute(): string
    {
        return $this->ayat_awal === $this->ayat_akhir
            ? "Ayat {$this->ayat_awal}"
            : "Ayat {$this->ayat_awal}-{$this->ayat_akhir}";
    }

    public static function resolveJuz(int $surahNomor, int $ayat): int
    {
        $start = self::query()
            ->select('juz')
            ->join('juz_starts', 'juz_starts.surah_id', '=', 'surahs.id')
            ->join('surahs', 'surahs.id', '=', 'juz_starts.surah_id')
            ->where('surahs.nomor', '<=', $surahNomor)
            ->where(function ($q) use ($surahNomor, $ayat) {
                $q->where('surahs.nomor', '<', $surahNomor)
                  ->orWhere(function ($q2) use ($ayat) {
                      $q2->where('surahs.nomor', $surahNomor)
                         ->where('juz_starts.ayat', '<=', $ayat);
                  });
            })
            ->orderBy('juz_starts.juz', 'desc')
            ->value('juz');

        return $start ?? 1;
    }
}