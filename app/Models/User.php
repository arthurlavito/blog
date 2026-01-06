<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];

    // Senior Tip: Always define these constants to avoid "Magic Strings"
    public const ROLE_ADMIN = 'admin';
    public const ROLE_AUTHOR = 'author';
    public const ROLE_READER = 'reader';

    /**
     * The missing method that is causing your error!
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isAuthor(): bool
    {
        return $this->role === self::ROLE_AUTHOR;
    }

    public function isReader(): bool
    {
        return $this->role === self::ROLE_READER;
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}