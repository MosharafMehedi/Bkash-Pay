<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Slider extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'subtitle',
        'image', 'bg_image',
        'button_text', 'link_type', 'link_value',
        'text_position', 'badge_text', 'badge_color',
        'sort_order', 'is_active',
        'starts_at', 'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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

    // ── Helpers ──
    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? url('storage/' . $this->image)
            : '';
    }

    public function getBgImageUrlAttribute(): string
    {
        return $this->bg_image
            ? url('storage/' . $this->bg_image)
            : '';
    }

    public function hasImage(): bool
    {
        return ! empty($this->image)
            && Storage::disk('public')->exists($this->image);
    }

    public function hasBgImage(): bool
    {
        return ! empty($this->bg_image)
            && \Storage::disk('public')->exists($this->bg_image);
    }

    /**
     * Get CTA link URL based on link_type.
     */
    public function getCtaUrlAttribute(): string
    {
        if (! $this->link_value) {
            return '#';
        }

        return match ($this->link_type) {
            'product'  => route('products.show', $this->link_value),
            'category' => route('products.index', ['category' => $this->link_value]),
            'custom'   => url($this->link_value),
            default    => $this->link_value, // direct URL
        };
    }

    public function isScheduled(): bool
    {
        return $this->starts_at || $this->ends_at;
    }
}