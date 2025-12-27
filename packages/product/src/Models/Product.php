<?php

namespace Vendor\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Vendor\Product\Database\Factories\ProductFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'sale_price',
        'stock_quantity',
        'sku',
        'category_id',
        'images',
        'featured_image',
        'is_active',
        'is_featured',
        'view_count',
        'sort_order',
        'meta_data',
        'price_off'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'view_count' => 'integer',
        'sort_order' => 'integer',
        'images' => 'array',
        'meta_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug if not provided
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }

            // Auto-generate SKU if not provided
            if (empty($product->sku)) {
                $product->sku = 'SP-' . strtoupper(Str::random(8));
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }

            // Auto-generate SKU if not provided (for existing products)
            if (empty($product->sku)) {
                $product->sku = 'SP-' . strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Get the category that owns the product (legacy - for backward compatibility).
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Get the categories that belong to the product.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class, 'category_product', 'product_id', 'category_id')
            ->withTimestamps()
            ->orderBy('sort_order');
    }

    /**
     * Get the variants for the product.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include products in stock.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * Scope a query to filter by category (supports both single category_id and multiple categories).
     */
    public function scopeByCategory($query, $categoryId)
    {
        if (is_array($categoryId)) {
            return $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->whereIn('product_categories.id', $categoryId);
            });
        }

        return $query->where(function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId)
                ->orWhereHas('categories', function ($subQ) use ($categoryId) {
                    $subQ->where('product_categories.id', $categoryId);
                });
        });
    }

    /**
     * Scope a query to search products by name or description.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%");
        });
    }

    /**
     * Get the current price (sale price if available, otherwise regular price).
     */
    public function getCurrentPriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }

    /**
     * Check if product is on sale.
     */
    public function getIsOnSaleAttribute(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    /**
     * Get discount percentage if on sale.
     */
    public function getDiscountPercentageAttribute(): ?float
    {
        if (!$this->is_on_sale) {
            return null;
        }

        return round((($this->price - $this->sale_price) / $this->price) * 100, 2);
    }

    /**
     * Check if product is in stock.
     */
    public function getIsInStockAttribute(): bool
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Get images with app_url prefix.
     */
    public function getImagesAttribute($value): array
    {
        $images = is_string($value) ? json_decode($value, true) : $value;

        if (empty($images) || !is_array($images)) {
            return [];
        }

        $appUrl = rtrim(config('app.url'), '/');

        return array_map(function ($image) use ($appUrl) {
            if (empty($image)) {
                return null;
            }

            // If already a full URL, return as is
            if (preg_match('/^https?:\/\//', $image)) {
                return $image;
            }

            // Remove leading slash if present
            $image = ltrim($image, '/');

            return $appUrl . '/' . $image;
        }, $images);
    }

    /**
     * Get featured_image with app_url prefix.
     */
    public function getFeaturedImageAttribute($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // If already a full URL, return as is
        if (preg_match('/^https?:\/\//', $value)) {
            return $value;
        }

        $appUrl = rtrim(config('app.url'), '/');
        $value = ltrim($value, '/');

        return $appUrl . '/' . $value;
    }

    /**
     * Get image attribute (alias for featured_image).
     */
    public function getImageAttribute(): ?string
    {
        return $this->featured_image;
    }

    /**
     * Increment view count.
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }
}
