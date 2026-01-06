<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory; 

    protected $fillable = [
        'user_id', 'title', 'content', 'category', 'image', 'slug', 'views',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Senior move: Local Scope for filtering.
     * This handles both Search and Category filtering in one place.
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        // Filter by Search Keywords
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                      ->orWhere('content', 'like', '%' . $search . '%');
            });
        });

        // Filter by Category
        $query->when($filters['category'] ?? false, function ($query, $category) {
            $query->where('category', $category);
        });
    }
}