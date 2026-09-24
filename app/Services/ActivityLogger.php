<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Log a CRM activity.
     */
    public static function log(
        string $action,
        string $description,
        ?string $subjectType = null,
        ?int $subjectId = null,
        ?array $properties = null
    ): ?ActivityLog {
        try {
            $user = Auth::user();

            return ActivityLog::create([
                'user_id' => $user?->id,
                'user_name' => $user?->name ?? 'Sistem Otomatis',
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'action' => $action,
                'description' => $description,
                'properties' => $properties,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Silently catch to prevent breaking user flow
            report($e);
            return null;
        }
    }
}
