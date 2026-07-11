<?php

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class PostObserver
{
    public function saved(Post $post): void
    {
        if ($post->wasChanged('is_featured')) {
            Cache::forget('featured_post');
        }
    }

    public function deleted(Post $post): void
    {
        if ($post->is_featured) {
            Cache::forget('featured_post');
        }
    }
}
