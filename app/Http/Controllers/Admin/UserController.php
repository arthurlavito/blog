<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of all users for Admin management.
     */
    public function index()
    {
        /** @var \App\Models\User $admin */
        $admin = Auth::user();

        // 1. Double-check Authorization
        if (!$admin || !$admin->isAdmin()) {
            abort(403, 'Unauthorized access to user management.');
        }

        // 2. Fetch users with pagination (latest first)
        $users = User::latest()->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Handle the request from a Reader to become an Author.
     */
    public function requestAuthorStatus()
    {
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Only allow readers who haven't already requested to submit
        if ($user->role === 'reader' && !$user->author_requested_at) {
            $user->update(['author_requested_at' => now()]);
            return back()->with('success', 'Your request to become an author has been sent!');
        }
        
        return back()->with('error', 'Request already pending or you are already an author.');
    }

    /**
     * Update the role of a specific user (Admin only).
     */
    public function updateRole(Request $request, User $user)
    {
        /** @var \App\Models\User $admin */
        $admin = Auth::user();

        // 1. Authorization Check
        if (!$admin || !$admin->isAdmin()) {
            abort(403);
        }

        // 2. Safety: Prevent the current Admin from demoting themselves
        if ($admin->id === $user->id && $request->role !== 'admin') {
            return back()->with('error', 'You cannot demote yourself from Admin status.');
        }

        // 3. Validation
        $validated = $request->validate([
            'role' => 'required|in:admin,author,reader',
        ]);

        // 4. Prepare update data
        $updateData = ['role' => $validated['role']];

        // SENIOR MOVE: If we are promoting them to Author or Admin, clear the request timestamp
        if (in_array($validated['role'], ['author', 'admin'])) {
            $updateData['author_requested_at'] = null;
        }

        // 5. Update and Redirect
        $user->update($updateData);

        return back()->with('success', "User {$user->name} role updated to " . ucfirst($validated['role']));
    }

    public function destroy(User $user)
        {
            // 1. Loop through all user posts to delete their physical images
            foreach($user->posts as $post) {
                if ($post->image) {
                    Storage::disk('public')->delete($post->image);
                }
            }

            // 2. Delete the user (Laravel will cascade delete posts if your migration is set up, 
            // or you can delete them manually first)
            $user->delete();

            return back()->with('success', 'User and all associated data purged.');
        }
}