<?php

namespace App\Models;

use Database\Factories\AbsensiFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    /** @use HasFactory<AbsensiFactory> */
    use HasFactory;

    protected $fillable = [
        'santri_id',
        'ustadz_id',
        'tanggal',
        'sesi',
        'waktu_tiba',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class);
    }

    public function ustadz(): BelongsTo
    {
        return $this->belongsTo(Ustadz::class);
    }

    public static function getStatusColors(): array
    {
        return [
            'hadir' => 'bg-success-50 text-success-700 border-success-200',
            'izin' => 'bg-info-50 text-info-700 border-info-200',
            'sakit' => 'bg-warning-50 text-warning-700 border-warning-200',
            'alpa' => 'bg-error-50 text-error-700 border-error-200',
        ];
    }

    public static function getStatusLabels(): array
    {
        return [
            'hadir' => 'Hadir',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpa' => 'Alpa',
        ];
    }

    public static function getSesiLabels(): array
    {
        return [
            'subuh' => 'Subuh',
            'ashar' => 'Ashar',
            'maghrib' => 'Maghrib',
        ];
    }
}