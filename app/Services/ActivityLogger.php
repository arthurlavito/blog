<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public function log(
        string $action,
        ?Model $subject = null,
        ?string $description = null,
        ?int $userId = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id'      => $userId ?? Auth::id(),
            'action'       => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->getKey(),
            'description'  => $description,
        ]);
    }
}
