<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditLogService
{
    /**
     * Log a model event (create/update/delete).
     */
    public function logModel(
        string $event,
        Model $model,
        array $oldValues = [],
        array $newValues = [],
        ?string $description = null
    ): AuditLog {
        return AuditLog::create([
            'user_id'        => auth()->id(),
            'event'          => $event,
            'auditable_type' => get_class($model),
            'auditable_id'   => $model->getKey(),
            'old_values'     => $oldValues ?: null,
            'new_values'     => $newValues ?: null,
            'description'    => $description,
            'url'            => request()->fullUrl(),
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
        ]);
    }

    /**
     * Log a custom action (login, logout, status change, etc.).
     */
    public function logAction(
        string $description,
        ?Model $model = null,
        array $extra = [],
        string $event = 'custom'
    ): AuditLog {
        return AuditLog::create([
            'user_id'        => auth()->id(),
            'event'          => $event,
            'auditable_type' => $model ? get_class($model) : null,
            'auditable_id'   => $model?->getKey(),
            'old_values'     => $extra['old'] ?? null,
            'new_values'     => $extra['new'] ?? null,
            'description'    => $description,
            'url'            => request()->fullUrl(),
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
        ]);
    }

    /**
     * Log login event (user_id from $user, not auth).
     */
    public function logLogin($user): AuditLog
    {
        return AuditLog::create([
            'user_id'        => $user->id,
            'event'          => 'login',
            'auditable_type' => get_class($user),
            'auditable_id'   => $user->id,
            'description'    => 'User logged in',
            'url'            => request()->fullUrl(),
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
        ]);
    }

    /**
     * Log logout event.
     */
    public function logLogout($user): AuditLog
    {
        return AuditLog::create([
            'user_id'        => $user->id,
            'event'          => 'logout',
            'auditable_type' => get_class($user),
            'auditable_id'   => $user->id,
            'description'    => 'User logged out',
            'url'            => request()->fullUrl(),
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
        ]);
    }
}