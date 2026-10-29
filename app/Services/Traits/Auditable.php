<?php

namespace App\Services\Traits;

use App\Services\AuditLogService;

trait Auditable
{
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
                $model->filteredAttributes(),
                "Created {$model->getModelLabel()}: " . ($model->name ?? $model->id)
            );
        });

        static::updated(function ($model) {
            if (! $model->shouldAudit('updated')) {
                return;
            }

            $changes = $model->filteredChanges();

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
                $model->filteredAttributes(),
                [],
                "Deleted {$model->getModelLabel()}: " . ($model->name ?? $model->id)
            );
        });
    }

    protected function shouldAudit(string $event): bool
    {
        return true;
    }

    protected function getModelLabel(): string
    {
        $parts = explode('\\', static::class);
        return end($parts);
    }

    protected function getAuditExclude(): array
    {
        return ['password', 'remember_token'];
    }

    /**
     * Attributes minus excluded fields.
     */
    protected function filteredAttributes(): array
    {
        return array_diff_key(
            $this->getAttributes(),
            array_flip($this->getAuditExclude())
        );
    }

    /**
     * Changes minus excluded fields, except updated_at.
     */
    protected function filteredChanges(): array
    {
        $changes = $this->getChanges();
        unset($changes['updated_at']);

        return array_diff_key(
            $changes,
            array_flip($this->getAuditExclude())
        );
    }
}