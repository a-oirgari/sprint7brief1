<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    /**
     * Mass-assignable attributes.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Hidden attributes (never returned in JSON).
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'password' => 'hashed',
    ];
}