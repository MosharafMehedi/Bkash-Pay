<?php

namespace App\Models;
use App\Services\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'subtitle',
        'description',
        'price_bdt',
        'price_usd',
        'discount_price',
        'quantity',
        'stock',
        'sku',
        'brand',
        'tags',
        'rating',
        'review_count',
        'is_featured',
        'is_deal',
        'deal_price',
        'deal_ends_at',
        'is_active',
        'image',
        'gallery',
        'published_at',
        'meta_title',
        'meta_description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'gallery'      => 'array',
        'tags'         => 'array',
        'is_active'    => 'boolean',
        'is_featured'  => 'boolean',
        'is_deal'      => 'boolean',
        'published_at' => 'datetime',
        'deal_ends_at' => 'datetime',
        'price_bdt'    => 'decimal:2',
        'price_usd'    => 'decimal:2',
        'discount_price' => 'decimal:2',
        'deal_price'   => 'decimal:2',
        'rating'       => 'decimal:2',
        'quantity'     => 'integer',
        'stock'        => 'integer',
        'review_count' => 'integer',
    ];

    // ═══ Boot ═══

    protected static function booted(): void
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name) . '-' . Str::random(4);
            }
        });

        static::created(function ($product) {
            if (empty($product->sku)) {
                $product->updateQuietly([
                    'sku' => 'SKU-' . str_pad($product->id, 5, '0', STR_PAD_LEFT),
                ]);
            }
        });
    }

    // ═══ Relations ═══

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ═══ Scopes ═══

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

    public function scopeDeals($q)
    {
        return $q->where('is_deal', true)
            ->whereNotNull('deal_price')
            ->where(function ($w) {
                $w->whereNull('deal_ends_at')->orWhere('deal_ends_at', '>=', now());
            });
    }

    // ═══ Price Helpers ═══

    public function getFinalPriceBdtAttribute(): float
    {
        return (float) ($this->discount_price ?? $this->price_bdt);
    }

    public function getFinalPriceUsdAttribute(): float
    {
        return (float) $this->price_usd;
    }

    public function isDealActive(): bool
    {
        return $this->is_deal
            && $this->deal_price !== null
            && (! $this->deal_ends_at || $this->deal_ends_at->isFuture());
    }

    public function getEffectivePriceAttribute(): float
    {
        if ($this->isDealActive()) {
            return (float) $this->deal_price;
        }

        return (float) ($this->discount_price ?? $this->price_bdt);
    }

    // ═══ Badge ═══

    public function getStockBadgeAttribute(): array
    {
        if ($this->stock <= 0) {
            return ['label' => 'Out of stock', 'class' => 'stock-out'];
        }
        if ($this->stock <= 3) {
            return ['label' => "Only {$this->stock} left", 'class' => 'stock-low'];
        }
        return ['label' => 'In stock', 'class' => 'stock-ok'];
    }

    // ═══ Media ═══

    public function getImageUrlAttribute(): string
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::disk('public')->url($this->image);
        }
        return '';
    }

    public function hasImage(): bool
    {
        return ! empty($this->image) && Storage::disk('public')->exists($this->image);
    }

    // ═══ Stock ═══

    public function decrementStock(int $qty = 1): bool
    {
        $affected = static::where('id', $this->id)
            ->where('stock', '>=', $qty)
            ->update([
                'stock'    => DB::raw('stock - ' . $qty),
                'quantity' => DB::raw('GREATEST(quantity - ' . $qty . ', 0)'),
            ]);

        if ($affected) {
            $this->refresh();
            return true;
        }

        return false;
    }

    // ═══ Rating ═══

    public function recalculateRating(): void
    {
        $stats = $this->reviews()
            ->where('is_approved', true)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')
            ->first();

        $this->update([
            'rating'       => round($stats->avg_rating ?? 0, 2),
            'review_count' => $stats->total ?? 0,
        ]);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}