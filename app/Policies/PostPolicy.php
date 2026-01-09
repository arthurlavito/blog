<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * Run before any other authorization checks.
     * If this returns true, the user is authorized immediately.
     */
    public function before(User $user, string $ability): bool|null
    {
        // Admins can do everything.
        if ($user->isAdmin()) {
            return true;
        }

        return null; // Fall through to specific methods for non-admins
    }

    /**
     * Anyone (including guests) can view the list of posts.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Anyone (including guests) can view a specific post.
     */
    public function view(?User $user, Post $post): bool
    {
        return true;
    }

    /**
     * Non-admins can only create if they are an author.
     * (Admins are already cleared by before())
     */
    public function create(User $user): bool
    {
        return $user->isAuthor();
    }

    /**
     * Only the post owner can update.
     */
    public function update(User $user, Post $post): Response
    {
        return $user->id === $post->user_id
            ? Response::allow()
            : Response::deny('You do not own this post, so you cannot edit it.');
    }

    /**
     * Only the post owner can delete.
     */
    public function delete(User $user, Post $post): Response
    {
        return $user->id === $post->user_id
            ? Response::allow()
            : Response::deny('Only the author can delete this masterpiece.');
    }
}