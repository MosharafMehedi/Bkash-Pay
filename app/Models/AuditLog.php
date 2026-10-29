<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'description',
        'url',
        'ip_address',
        'user_agent',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // ═══ Relations ═══

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Polymorphic relation to the audited model.
     * Usage: $log->auditable (returns Product, Order, etc.)
     */
    public function auditable()
    {
        return $this->morphTo();
    }

    // ═══ Scopes ═══

    public function scopeForUser($q, int $userId)
    {
        return $q->where('user_id', $userId);
    }

    public function scopeForEvent($q, string $event)
    {
        return $q->where('event', $event);
    }

    public function scopeForModel($q, string $modelClass, ?int $modelId = null)
    {
        $query = $q->where('auditable_type', $modelClass);
        if ($modelId) {
            $query->where('auditable_id', $modelId);
        }
        return $query;
    }

    public function scopeRecent($q, int $days = 7)
    {
        return $q->where('created_at', '>=', now()->subDays($days));
    }

    // ═══ Helpers ═══

    /**
     * Human-readable event badge.
     */
    public function eventBadge(): array
    {
        return match ($this->event) {
            'created' => ['label' => 'Created', 'color' => '#34d399'],
            'updated' => ['label' => 'Updated', 'color' => '#29e7ff'],
            'deleted' => ['label' => 'Deleted', 'color' => '#f87171'],
            'login'   => ['label' => 'Login',   'color' => '#a78bfa'],
            'logout'  => ['label' => 'Logout',  'color' => '#94a3b8'],
            'custom'  => ['label' => 'Action',  'color' => '#fbbf24'],
            default   => ['label' => ucfirst($this->event), 'color' => '#94a3b8'],
        };
    }

    /**
     * Short model name (e.g. "Product" from "App\Models\Product").
     */
    public function getModelShortNameAttribute(): string
    {
        if (! $this->auditable_type) {
            return '—';
        }

        $parts = explode('\\', $this->auditable_type);
        return end($parts);
    }

    /**
     * Which fields changed? (for "updated" event)
     */
    public function getChangedFieldsAttribute(): array
    {
        if (! $this->old_values || ! $this->new_values) {
            return [];
        }

        $changed = [];
        foreach ($this->new_values as $key => $newVal) {
            $oldVal = $this->old_values[$key] ?? null;
            if ($oldVal != $newVal) {
                $changed[$key] = [
                    'old' => $oldVal,
                    'new' => $newVal,
                ];
            }
        }

        return $changed;
    }
}