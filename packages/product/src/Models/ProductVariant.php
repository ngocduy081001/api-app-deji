<?php

namespace Vendor\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Vendor\Product\Database\Factories\ProductVariantFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'attributes',
        'price',
        'sale_price',
        'stock_quantity',
        'image',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'product_id' => 'integer',
        'attributes' => 'array',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
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

        // Auto-generate SKU if not provided
        static::creating(function ($variant) {
            if (empty($variant->sku)) {
                $variant->sku = 'VAR-' . strtoupper(Str::random(8));
            }
            
            // Auto-generate name from attributes if not provided
            if (empty($variant->name) && !empty($variant->attributes)) {
                $variant->name = static::generateNameFromAttributes($variant->attributes);
            }
        });

        static::updating(function ($variant) {
            // Auto-generate SKU if not provided (for existing variants)
            if (empty($variant->sku)) {
                $variant->sku = 'VAR-' . strtoupper(Str::random(8));
            }
            
            // Update name if attributes changed
            if ($variant->isDirty('attributes') && empty($variant->name)) {
                $variant->name = static::generateNameFromAttributes($variant->attributes);
            }
        });
    }

    /**
     * Generate variant name from attributes.
     */
    protected static function generateNameFromAttributes(array $attributes): string
    {
        $parts = [];
        foreach ($attributes as $key => $value) {
            $parts[] = ucfirst($key) . ': ' . $value;
        }
        return implode(' - ', $parts);
    }

    /**
     * Get the product that owns the variant.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the attribute values for this variant.
     */
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'product_variant_attribute_values',
            'product_variant_id',
            'attribute_value_id'
        )->withTimestamps();
    }

    /**
     * Attach attribute values to this variant.
     */
    public function attachAttributeValues(array $attributeValueIds): void
    {
        $this->attributeValues()->sync($attributeValueIds);
    }

    /**
     * Detach attribute values from this variant.
     */
    public function detachAttributeValues(array $attributeValueIds = []): void
    {
        if (empty($attributeValueIds)) {
            $this->attributeValues()->detach();
        } else {
            $this->attributeValues()->detach($attributeValueIds);
        }
    }

    /**
     * Get grouped attribute values (by attribute).
     */
    public function getGroupedAttributeValuesAttribute(): array
    {
        $grouped = [];
        
        foreach ($this->attributeValues as $value) {
            $attributeName = $value->attribute->name;
            if (!isset($grouped[$attributeName])) {
                $grouped[$attributeName] = [];
            }
            $grouped[$attributeName][] = $value;
        }
        
        return $grouped;
    }

    /**
     * Get total price adjustment from attribute values.
     */
    public function getTotalPriceAdjustmentAttribute(): float
    {
        return (float) $this->attributeValues->sum('price_adjustment');
    }

    /**
     * Get final price including attribute value adjustments.
     */
    public function getFinalPriceAttribute(): float
    {
        $basePrice = $this->current_price;
        $adjustment = $this->total_price_adjustment;
        
        return $basePrice + $adjustment;
    }

    /**
     * Scope a query to only include active variants.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include variants in stock.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * Scope a query to filter by attribute.
     */
    public function scopeByAttribute($query, string $key, $value)
    {
        return $query->whereJsonContains('attributes->' . $key, $value);
    }

    /**
     * Get the current price (variant price, sale price, or product price).
     */
    public function getCurrentPriceAttribute(): float
    {
        // If variant has sale price, use it
        if ($this->sale_price !== null) {
            return (float) $this->sale_price;
        }
        
        // If variant has regular price, use it
        if ($this->price !== null) {
            return (float) $this->price;
        }
        
        // Otherwise, use product price
        return $this->product->sale_price ?? $this->product->price;
    }

    /**
     * Get the original price (variant price or product price).
     */
    public function getOriginalPriceAttribute(): float
    {
        if ($this->price !== null) {
            return (float) $this->price;
        }
        
        return (float) $this->product->price;
    }

    /**
     * Check if variant is on sale.
     */
    public function getIsOnSaleAttribute(): bool
    {
        $currentPrice = $this->current_price;
        $originalPrice = $this->original_price;
        
        return $currentPrice < $originalPrice;
    }

    /**
     * Get discount percentage if on sale.
     */
    public function getDiscountPercentageAttribute(): ?float
    {
        if (!$this->is_on_sale) {
            return null;
        }

        $currentPrice = $this->current_price;
        $originalPrice = $this->original_price;

        return round((($originalPrice - $currentPrice) / $originalPrice) * 100, 2);
    }

    /**
     * Check if variant is in stock.
     */
    public function getIsInStockAttribute(): bool
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Get attribute value by key.
     */
    public function getAttribute($key)
    {
        // Check if it's a custom attribute key
        if (is_array($this->attributes) && isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
        
        return parent::getAttribute($key);
    }

    /**
     * Get display image (variant image or product featured image).
     */
    public function getDisplayImageAttribute(): ?string
    {
        return $this->image ?? $this->product->featured_image;
    }
}

