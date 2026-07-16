<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class FlushPostViews extends Command
{
    protected $signature   = 'posts:flush-views';
    protected $description = 'Flush buffered guest view counts from cache to the database.';

    public function handle(): int
    {
        // pull() is atomic: gets the value then deletes the key in one operation.
        // Any views that arrive between pull() and the DB writes go into a fresh buffer.
        $buffer = Cache::pull('post_views_buffer', []);

        if (empty($buffer)) {
            return self::SUCCESS;
        }

        foreach ($buffer as $postId => $count) {
            Post::where('id', $postId)->increment('views', (int) $count);
        }

        $this->line('Flushed view counts for ' . count($buffer) . ' post(s).');

        return self::SUCCESS;
    }
}
