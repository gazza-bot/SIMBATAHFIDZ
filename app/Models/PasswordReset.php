<?php

namespace App\Models;

use Database\Factories\PasswordResetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;

    protected $table = 'password_resets';

    protected $primaryKey = 'username';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'username',
        'selector',
        'token',
        'requested_at',
        'token_created_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'token_created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }
}