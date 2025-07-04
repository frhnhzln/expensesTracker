<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Profile extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'profile';

    protected $fillable = [
        'username',
        'email',
        'password',
        'qr_code',
        'face_id',
    ];

    // ✅ Hide password and remember_token when serializing
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ✅ Cast password and other fields
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
