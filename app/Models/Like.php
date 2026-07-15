<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    public const VALID_TYPES = ['like', 'love', 'fire', 'thinking'];

    protected $fillable = ['user_id', 'likeable_id', 'likeable_type', 'type'];

    public function likeable() { return $this->morphTo(); }
    public function user() { return $this->belongsTo(User::class); }
}