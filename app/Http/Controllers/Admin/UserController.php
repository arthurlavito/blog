<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of all users for Admin management.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Double-check Authorization
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Unauthorized access to user management.');
        }

        // 2. Fetch users with pagination
        $users = User::latest()->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    
    

    /**
     * Update the role of a specific user.
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

        // 4. Update and Redirect
        $user->update([
            'role' => $validated['role']
        ]);

        return back()->with('success', "User {$user->name} role updated to " . ucfirst($validated['role']));
    }

    
}