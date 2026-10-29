<?php

namespace App\Services\Traits;

use App\Services\AuditLogService;

trait Auditable
{
    /**
     * Boot the trait — hook into model events.
     */
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            if (! $model->shouldAudit('created')) {
                return;
            }

            app(AuditLogService::class)->logModel(
                'created',
                $model,
                [],
                $model->getAttributes(),
                "Created {$model->getModelLabel()}: " . ($model->name ?? $model->id)
            );
        });

        static::updated(function ($model) {
            if (! $model->shouldAudit('updated')) {
                return;
            }

            $changes = $model->getChanges();
            unset($changes['updated_at']); // ignore timestamp

            if (empty($changes)) {
                return;
            }

            $old = [];
            foreach (array_keys($changes) as $key) {
                $old[$key] = $model->getOriginal($key);
            }

            app(AuditLogService::class)->logModel(
                'updated',
                $model,
                $old,
                $changes,
                "Updated {$model->getModelLabel()}: " . ($model->name ?? $model->id)
            );
        });

        static::deleted(function ($model) {
            if (! $model->shouldAudit('deleted')) {
                return;
            }

            app(AuditLogService::class)->logModel(
                'deleted',
                $model,
                $model->getAttributes(),
                [],
                "Deleted {$model->getModelLabel()}: " . ($model->name ?? $model->id)
            );
        });
    }

    /**
     * Override in model to disable specific events.
     */
    protected function shouldAudit(string $event): bool
    {
        return true;
    }

    /**
     * Human-readable model label.
     */
    protected function getModelLabel(): string
    {
        $parts = explode('\\', static::class);
        return end($parts);
    }

    /**
     * Fields to exclude from audit (privacy).
     */
    protected function getAuditExclude(): array
    {
        return ['password', 'remember_token'];
    }
}