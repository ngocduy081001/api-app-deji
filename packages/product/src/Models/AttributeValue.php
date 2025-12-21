<?php

namespace Vendor\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeValue extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Vendor\Product\Database\Factories\AttributeValueFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attribute_id',
        'value',
        'label',
        'color_code',
        'image',
        'price_adjustment',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attribute_id' => 'integer',
        'price_adjustment' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the attribute that owns the value.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Get the product variants that use this attribute value.
     */
    public function productVariants(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductVariant::class,
            'product_variant_attribute_values',
            'attribute_value_id',
            'product_variant_id'
        )->withTimestamps();
    }

    /**
     * Scope a query to only include active values.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by attribute.
     */
    public function scopeByAttribute($query, $attributeId)
    {
        return $query->where('attribute_id', $attributeId);
    }

    /**
     * Get display label (label if set, otherwise value).
     */
    public function getDisplayLabelAttribute(): string
    {
        return $this->label ?? $this->value;
    }

    /**
     * Check if this is a color attribute value.
     */
    public function getIsColorAttribute(): bool
    {
        return $this->attribute && $this->attribute->type === Attribute::TYPE_COLOR;
    }

    /**
     * Get the display value with price adjustment if any.
     */
    public function getDisplayValueWithPriceAttribute(): string
    {
        $display = $this->display_label;
        
        if ($this->price_adjustment != 0) {
            $sign = $this->price_adjustment > 0 ? '+' : '';
            $display .= ' (' . $sign . number_format($this->price_adjustment) . 'đ)';
        }
        
        return $display;
    }
}

