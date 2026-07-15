<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use App\Observers\PostObserver;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

#[ObservedBy(PostObserver::class)]
class Post extends Model implements Feedable
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'category_id',
        'image',
        'user_id',
        'views',
        'is_featured',
        'meta_title',
        'meta_description',
        'focus_keyword',
        'canonical_url',
        'noindex',
    ];

    /**
     * Boot logic for automated slugs
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            $post->slug = static::generateUniqueSlug($post->title);
        });

        static::updating(function ($post) {
            if ($post->isDirty('title')) {
                $post->slug = static::generateUniqueSlug($post->title);
            }
        });
    }

    /**
     * Recursive function to ensure slug uniqueness
     */
    private static function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Scope: only posts with status = 0 (published/live).
     * status defaults to 0 on creation — 0 = live, anything else = unlisted/draft.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 0);
    }

    /**
     * Scope for searching and filtering
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        // Search Filter
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                      ->orWhere('content', 'like', '%' . $search . '%');
            });
        });

        // Category Filter using the new Relationship ID
        $query->when($filters['category'] ?? false, function ($query, $category_id) {
            $query->where('category_id', $category_id);
        });
    }

    /**
     * Relationships
     */
    
    // The Professional Category Relationship
    public function category() 
    { 
        return $this->belongsTo(Category::class); 
    }

    public function user() 
    { 
        return $this->belongsTo(User::class); 
    }

    public function comments() 
    { 
        return $this->hasMany(Comment::class)->latest(); 
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Helpers
     */
    
    public function isLikedBy($user)
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // ── RSS Feed ───────────────────────────────────────────────────────────

    public static function getFeedItems()
    {
        return static::with('user')
            ->published()
            ->latest()
            ->take(25)
            ->get();
    }

    public function toFeedItem(): FeedItem
    {
        return FeedItem::create()
            ->id(route('posts.show', $this))
            ->title($this->meta_title ?: $this->title)
            ->summary($this->meta_description ?: Str::words(strip_tags($this->content), 28, '…'))
            ->updated($this->updated_at)
            ->link(route('posts.show', $this))
            ->authorName($this->user->name);
    }

}