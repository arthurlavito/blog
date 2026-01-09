<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthorRequestController extends Controller
{
    /**
     * Reader: Submit request to become an author
     */
    public function submit(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'reader') {
            return back()->with('error', 'Only readers can apply for author status.');
        }

        $user->update([
            'author_requested_at' => now(),
        ]);

        return back()->with('success', 'Your application is now being reviewed by our team!');
    }

    /**
     * Admin: Approve a pending request
     */
    public function approve(User $user)
    {
        // Safety check: Ensure the user actually requested it
        if (!$user->author_requested_at) {
            return back()->with('error', 'This user has not submitted an application.');
        }

        $user->update([
            'role' => 'author',
            'author_requested_at' => null, // Reset the request
        ]);

        return back()->with('success', "{$user->name} is now a verified Author!");
    }

    /**
     * Admin: Deny/Reject a request
     */
    public function deny(User $user)
    {
        $user->update([
            'author_requested_at' => null, // Just clear the request
        ]);

        return back()->with('success', "The application for {$user->name} has been declined.");
    }

    public function demote(User $user)
        {
            if ($user->role === 'admin') {
                return back()->with('error', 'You cannot demote an admin!');
            }

            $user->update(['role' => 'reader']);

            return back()->with('success', "{$user->name} has been demoted to Reader.");
        }
}