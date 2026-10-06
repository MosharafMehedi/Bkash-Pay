<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'text', 'icon', 'link', 'bg_color',
        'sort_order', 'is_active', 'is_dismissible',
        'starts_at', 'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_dismissible' => 'boolean',
        'sort_order' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    // ── Scopes ──
    public function scopeActive($q)
    {
        return $q->where('is_active', true)
            ->where(function ($w) {
                $w->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($w) {
                $w->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order')->orderByDesc('id');
    }
}