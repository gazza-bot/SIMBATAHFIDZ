<?php

namespace App\Models;

use Database\Factories\AngkatanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Angkatan extends Model
{
    /** @use HasFactory<AngkatanFactory> */
    use HasFactory;

    protected $fillable = [
        'nama',
        'tahun_mulai',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    public function kelompoks(): HasMany
    {
        return $this->hasMany(Kelompok::class);
    }

    public function santris(): HasMany
    {
        return $this->hasMany(Santri::class);
    }
}