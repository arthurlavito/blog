<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Collection;

class RelatedPostsService
{
    /**
     * Return up to $limit published posts related to $post.
     *
     * Scoring: posts that share the most tags with $post rank first.
     * Tie-break: most recent. Falls back to same-category recents when there
     * are no tag overlaps (or the post has no tags), and finally to global
     * recents if the post has no category either.
     */
    public function get(Post $post, int $limit = 4): Collection
    {
        $tagIds = $post->relationLoaded('tags')
            ? $post->tags->pluck('id')
            : $post->tags()->pluck('tags.id');

        if ($tagIds->isNotEmpty()) {
            // whereHas (EXISTS) filters the rows — SQLite-safe.
            // withCount gives the overlap score for ordering.
            $byTags = Post::published()
                ->where('id', '!=', $post->id)
                ->with(['user', 'category'])
                ->whereHas('tags', fn ($q) => $q->whereIn('tags.id', $tagIds))
                ->withCount([
                    'tags as tag_overlap' => fn ($q) => $q->whereIn('tags.id', $tagIds),
                ])
                ->orderByDesc('tag_overlap')
                ->orderByDesc('created_at')
                ->take($limit)
                ->get();

            if ($byTags->isNotEmpty()) {
                return $byTags;
            }
        }

        return $this->fallback($post, $limit);
    }

    private function fallback(Post $post, int $limit): Collection
    {
        return Post::published()
            ->where('id', '!=', $post->id)
            ->with(['user', 'category'])
            ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
            ->latest()
            ->take($limit)
            ->get();
    }
}
