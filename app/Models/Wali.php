<?php

namespace App\Models;

use Database\Factories\WaliFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wali extends Model
{
    /** @use HasFactory<WaliFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'telepon',
        'alamat',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function santris(): HasMany
    {
        return $this->hasMany(Santri::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WaliMessage::class);
    }
}