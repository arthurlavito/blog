<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'role', 
        'author_requested_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'author_requested_at' => 'datetime', 
    ];

    public const ROLE_ADMIN = 'admin';
    public const ROLE_AUTHOR = 'author';
    public const ROLE_READER = 'reader';

    public function isAdmin(): bool { return $this->role === self::ROLE_ADMIN; }
    public function isAuthor(): bool { return $this->role === self::ROLE_AUTHOR; }
    public function isReader(): bool { return $this->role === self::ROLE_READER; }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }
}