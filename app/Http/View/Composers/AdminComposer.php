<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\User;

class AdminComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        // Logic: Count readers who have a non-null request timestamp
        $pendingCount = User::where('role', 'reader')
                            ->whereNotNull('author_requested_at')
                            ->count();

        $view->with('pendingAuthorCount', $pendingCount);
    }
}