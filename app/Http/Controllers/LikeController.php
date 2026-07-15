<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class LikeController extends Controller
{
    public function toggle(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $key = 'like:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, 30)) {
            return response()->json(['message' => 'Too many requests.'], 429);
        }
        RateLimiter::hit($key, 60);

        $reactionType = in_array($request->reaction, Like::VALID_TYPES)
            ? $request->reaction
            : 'like';

        $modelClass = $request->type === 'post' ? Post::class : Comment::class;
        $model = $modelClass::findOrFail($request->id);

        $existing = $model->likes()->where('user_id', auth()->id())->first();

        if ($existing) {
            if ($existing->type === $reactionType) {
                // Tapping same reaction removes it
                $existing->delete();
                $status = 'removed';
            } else {
                // Switching to a different reaction
                $existing->update(['type' => $reactionType]);
                $status = 'switched';
            }
        } else {
            $model->likes()->create([
                'user_id' => auth()->id(),
                'type'    => $reactionType,
            ]);
            $status = 'reacted';
        }

        $counts = $model->likes()
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        return response()->json([
            'status'      => $status,
            'reaction'    => $reactionType,
            'counts'      => $counts,
            'total'       => $counts->sum(),
            'user_reaction' => $status === 'removed' ? null : $reactionType,
        ]);
    }
}
