<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'subtitle',
        'description',
        'price_bdt',
        'price_usd',
        'discount_price',
        'image',
        'gallery',
        'quantity',
        'stock',
        'sku',
        'category',
        'brand',
        'tags',
        'rating',
        'review_count',
        'is_active',
        'is_featured',
        'published_at',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'gallery'      => 'array',
        'tags'         => 'array',
        'is_active'    => 'boolean',
        'is_featured'  => 'boolean',
        'published_at' => 'datetime',
        'rating'       => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name) . '-' . Str::random(4);
            }
        });

        // SKU generate AFTER insert (uses auto-increment ID)
        static::created(function ($product) {
            if (empty($product->sku)) {
                $product->updateQuietly([
                    'sku' => 'SKU-' . str_pad($product->id, 5, '0', STR_PAD_LEFT),
                ]);
            }
        });
    }

    // Scopes
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
    public function scopeFeatured($q)
    {
        return $q->where('is_featured', true);
    }
    public function scopeInStock($q)
    {
        return $q->where('stock', '>', 0);
    }

    // Helpers
    public function getFinalPriceBdtAttribute(): float
    {
        return (float) ($this->discount_price ?? $this->price_bdt);
    }

    public function getFinalPriceUsdAttribute(): float
    {
        return (float) $this->price_usd;
    }

    public function getStockBadgeAttribute(): array
    {
        if ($this->stock <= 0) return ['label' => 'Out of stock', 'class' => 'stock-out'];
        if ($this->stock <= 3) return ['label' => "Only {$this->stock} left", 'class' => 'stock-low'];
        return ['label' => 'In stock', 'class' => 'stock-ok'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Atomically decrement stock & quantity by 1.
     * Returns false if out of stock (race-safe).
     */
    public function decrementStock(): bool
    {
        $affected = static::where('id', $this->id)
            ->where('stock', '>', 0)
            ->update([
                'stock'    => DB::raw('stock - 1'),
                'quantity' => DB::raw('GREATEST(quantity - 1, 0)'),
            ]);

        if ($affected) {
            $this->refresh();
            return true;
        }

        return false;
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
