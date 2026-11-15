<?php

namespace App\Models;

use Database\Factories\KepalaTahfidzFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KepalaTahfidz extends Model
{
    /** @use HasFactory<KepalaTahfidzFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}