<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        // Honeypot — bots fill this hidden field; humans don't see it
        if ($request->filled('website')) {
            return back()->with('success', 'Comment posted!');
        }

        $key = 'comment:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, 4)) {
            return back()->withErrors(['body' => 'Too many comments. Please wait a minute.']);
        }
        RateLimiter::hit($key, 60);

        $validated = $request->validate([
            'body'      => 'required|string|min:2|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $user = auth()->user();

        // Auto-approve users who already have at least one approved comment
        $hasApprovedHistory = Comment::where('user_id', $user->id)
            ->where('status', Comment::STATUS_APPROVED)
            ->exists();

        $status = $hasApprovedHistory ? Comment::STATUS_APPROVED : Comment::STATUS_PENDING;

        $post->allComments()->create([
            'user_id'   => $user->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'body'      => $validated['body'],
            'status'    => $status,
        ]);

        $message = $status === Comment::STATUS_APPROVED
            ? 'Comment posted!'
            : 'Comment submitted and awaiting moderation.';

        return back()->with('success', $message);
    }

    public function approve(Comment $comment)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $comment->update(['status' => Comment::STATUS_APPROVED]);
        return back()->with('success', 'Comment approved.');
    }

    public function spam(Comment $comment)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $comment->update(['status' => Comment::STATUS_SPAM]);
        return back()->with('success', 'Comment marked as spam.');
    }

    public function trash(Comment $comment)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $comment->update(['status' => Comment::STATUS_TRASHED]);
        return back()->with('success', 'Comment trashed.');
    }
}
