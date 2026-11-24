<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
        'foto',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function wali()
    {
        return $this->hasOne(Wali::class);
    }

    public function ustadz()
    {
        return $this->hasOne(Ustadz::class);
    }

    public function kepalaTahfidz()
    {
        return $this->hasOne(KepalaTahfidz::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUstadz(): bool
    {
        return $this->role === 'ustadz';
    }

    public function isKepalaTahfidz(): bool
    {
        return $this->role === 'kepala_tahfidz';
    }

    public function isWali(): bool
    {
        return $this->role === 'wali';
    }

    public function passwordReset()
    {
        return $this->hasOne(PasswordReset::class, 'username', 'username');
    }
}