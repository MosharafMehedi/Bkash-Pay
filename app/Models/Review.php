<?php

namespace App\Models;

use App\Models\ReviewReply;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'rating',
        'title',
        'comment',
        'is_approved',
        'is_verified',
        'edited_at',
    ];

    protected $casts = [
        'rating'      => 'integer',
        'is_approved' => 'boolean',
        'is_verified' => 'boolean',
        'edited_at'   => 'datetime',
    ];

    // ── Relations ──
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function replies()
    {
        return $this->hasMany(ReviewReply::class)->oldest();
    }

    /**
     * Only top-level replies (no parent).
     */
    public function topLevelReplies()
    {
        return $this->hasMany(ReviewReply::class)
            ->whereNull('parent_id')
            ->oldest();
    }

    // ── Scopes ──
    public function scopeApproved($q)
    {
        return $q->where('is_approved', true);
    }

    public function scopeRating($q, int $rating)
    {
        return $q->where('rating', $rating);
    }

    // ── Helpers ──
    public function isOwnedBy(?User $user): bool
    {
        return $user && $this->user_id === $user->id;
    }

    public function isEdited(): bool
    {
        return $this->edited_at !== null;
    }

    public function repliesCount(): int
    {
        return $this->replies()->count();
    }
}