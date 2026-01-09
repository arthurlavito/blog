<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class LikeController extends Controller
{
   public function toggle(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Logic to find the model
        $type = $request->type === 'post' ? \App\Models\Post::class : \App\Models\Comment::class;
        $model = $type::findOrFail($request->id);

        $like = $model->likes()->where('user_id', auth()->id())->first();

        if ($like) {
            $like->delete();
            $status = 'unliked';
        } else {
            $model->likes()->create(['user_id' => auth()->id()]);
            $status = 'liked';
        }

        // This return is crucial for Alpine.js
        return response()->json([
            'status' => $status,
            'count' => $model->likes()->count()
        ]);
    }
}
