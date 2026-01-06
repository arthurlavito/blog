<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * Run before any other authorization checks.
     * High-level override for Admins.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null; // Fall through to specific methods
    }

    /**
     * Determine whether guests or users can view the index.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether guests or users can view a specific post.
     */
    public function view(?User $user, Post $post): bool
    {
        return true;
    }

    
    /**
 * Only a logged-in user with the 'author' role can create.
 * (Guests are automatically blocked here because there is no '?' prefix)
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