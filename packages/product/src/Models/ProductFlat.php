<?php

namespace Vendor\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductFlat extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'product_name',
        'product_slug',
        'product_description',
        'product_short_description',
        'product_sku',
        'category_id',
        'category_name',
        'variant_id',
        'variant_name',
        'variant_sku',
        'variant_attributes',
        'variant_image',
        'price',
        'sale_price',
        'final_price',
        'price_adjustment',
        'is_on_sale',
        'discount_percentage',
        'stock_quantity',
        'is_in_stock',
        'featured_image',
        'images',
        'attributes_flat',
        'product_is_active',
        'product_is_featured',
        'variant_is_active',
        'is_available',
        'product_view_count',
        'product_sort_order',
        'variant_sort_order',
        'searchable_text',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'product_id' => 'integer',
        'category_id' => 'integer',
        'variant_id' => 'integer',
        'variant_attributes' => 'array',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'price_adjustment' => 'decimal:2',
        'is_on_sale' => 'boolean',
        'discount_percentage' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_in_stock' => 'boolean',
        'images' => 'array',
        'attributes_flat' => 'array',
        'product_is_active' => 'boolean',
        'product_is_featured' => 'boolean',
        'variant_is_active' => 'boolean',
        'is_available' => 'boolean',
        'product_view_count' => 'integer',
        'product_sort_order' => 'integer',
        'variant_sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the variant.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Get the category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Scope for available products.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope for in stock products.
     */
    public function scopeInStock($query)
    {
        return $query->where('is_in_stock', true);
    }

    /**
     * Scope for on sale products.
     */
    public function scopeOnSale($query)
    {
        return $query->where('is_on_sale', true);
    }

    /**
     * Scope for featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('product_is_featured', true);
    }

    /**
     * Scope for filtering by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope for price range.
     */
    public function scopePriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('final_price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope for search.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('product_name', 'like', "%{$search}%")
              ->orWhere('product_description', 'like', "%{$search}%")
              ->orWhere('product_sku', 'like', "%{$search}%")
              ->orWhere('variant_sku', 'like', "%{$search}%")
              ->orWhere('searchable_text', 'like', "%{$search}%");
        });
    }

    /**
     * Generate flat entry from product (without variant).
     */
    public static function generateFromProduct(Product $product): array
    {
        $product->load('category');
        
        $salePrice = $product->sale_price;
        $finalPrice = $salePrice ?? $product->price;
        $isOnSale = $salePrice !== null && $salePrice < $product->price;
        $discountPercentage = $isOnSale 
            ? round((($product->price - $salePrice) / $product->price) * 100, 2)
            : null;

        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'product_description' => $product->description,
            'product_short_description' => $product->short_description,
            'product_sku' => $product->sku,
            'category_id' => $product->category_id,
            'category_name' => $product->category?->name,
            'variant_id' => null,
            'variant_name' => null,
            'variant_sku' => null,
            'variant_attributes' => null,
            'variant_image' => null,
            'price' => $product->price,
            'sale_price' => $salePrice,
            'final_price' => $finalPrice,
            'price_adjustment' => 0,
            'is_on_sale' => $isOnSale,
            'discount_percentage' => $discountPercentage,
            'stock_quantity' => $product->stock_quantity,
            'is_in_stock' => $product->stock_quantity > 0,
            'featured_image' => $product->featured_image,
            'images' => $product->images,
            'attributes_flat' => null,
            'product_is_active' => $product->is_active,
            'product_is_featured' => $product->is_featured,
            'variant_is_active' => true,
            'is_available' => $product->is_active,
            'product_view_count' => $product->view_count,
            'product_sort_order' => $product->sort_order,
            'variant_sort_order' => 0,
            'searchable_text' => implode(' ', array_filter([
                $product->name,
                $product->sku,
                $product->description,
                $product->category?->name,
            ])),
        ];
    }

    /**
     * Generate flat entry from product variant.
     */
    public static function generateFromVariant(ProductVariant $variant): array
    {
        $variant->load(['product.category', 'attributeValues.attribute']);
        $product = $variant->product;
        
        // Calculate prices
        $basePrice = $variant->price ?? $product->price;
        $baseSalePrice = $variant->sale_price ?? $product->sale_price;
        
        // Add attribute value adjustments
        $priceAdjustment = $variant->attributeValues->sum('price_adjustment');
        $finalPrice = ($baseSalePrice ?? $basePrice) + $priceAdjustment;
        
        $isOnSale = $baseSalePrice !== null && $baseSalePrice < $basePrice;
        $discountPercentage = $isOnSale
            ? round((($basePrice - $baseSalePrice) / $basePrice) * 100, 2)
            : null;

        // Build attributes flat
        $attributesFlat = [];
        foreach ($variant->attributeValues as $attrValue) {
            $attrName = $attrValue->attribute->slug ?? $attrValue->attribute->name;
            $attributesFlat[$attrName] = $attrValue->value;
        }

        // Build searchable text
        $searchableText = implode(' ', array_filter([
            $product->name,
            $variant->name,
            $product->sku,
            $variant->sku,
            $product->description,
            $product->category?->name,
            implode(' ', array_values($attributesFlat)),
        ]));

        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'product_description' => $product->description,
            'product_short_description' => $product->short_description,
            'product_sku' => $product->sku,
            'category_id' => $product->category_id,
            'category_name' => $product->category?->name,
            'variant_id' => $variant->id,
            'variant_name' => $variant->name,
            'variant_sku' => $variant->sku,
            'variant_attributes' => $variant->attributes,
            'variant_image' => $variant->image,
            'price' => $basePrice,
            'sale_price' => $baseSalePrice,
            'final_price' => $finalPrice,
            'price_adjustment' => $priceAdjustment,
            'is_on_sale' => $isOnSale,
            'discount_percentage' => $discountPercentage,
            'stock_quantity' => $variant->stock_quantity,
            'is_in_stock' => $variant->stock_quantity > 0,
            'featured_image' => $variant->image ?? $product->featured_image,
            'images' => $product->images,
            'attributes_flat' => $attributesFlat,
            'product_is_active' => $product->is_active,
            'product_is_featured' => $product->is_featured,
            'variant_is_active' => $variant->is_active,
            'is_available' => $product->is_active && $variant->is_active,
            'product_view_count' => $product->view_count,
            'product_sort_order' => $product->sort_order,
            'variant_sort_order' => $variant->sort_order,
            'searchable_text' => $searchableText,
        ];
    }

    /**
     * Refresh flat data for a product and all its variants.
     */
    public static function refreshForProduct(int $productId): void
    {
        $product = Product::with(['variants.attributeValues.attribute', 'category'])->find($productId);
        
        if (!$product) {
            return;
        }

        // Delete existing flat entries for this product
        static::where('product_id', $productId)->delete();

        // If product has variants, create entries for each variant
        if ($product->variants->isNotEmpty()) {
            foreach ($product->variants as $variant) {
                static::create(static::generateFromVariant($variant));
            }
        } else {
            // If no variants, create entry for the product itself
            static::create(static::generateFromProduct($product));
        }
    }

    /**
     * Rebuild entire flat table.
     */
    public static function rebuildAll(): int
    {
        // Clear existing data
        static::truncate();

        // Ensure all products have SKU
        Product::whereNull('sku')->orWhere('sku', '')->get()->each(function ($product) {
            $product->sku = 'PRD-' . strtoupper(\Illuminate\Support\Str::random(8));
            $product->save();
        });

        // Ensure all variants have SKU
        ProductVariant::whereNull('sku')->orWhere('sku', '')->get()->each(function ($variant) {
            $variant->sku = 'VAR-' . strtoupper(\Illuminate\Support\Str::random(8));
            $variant->save();
        });

        $count = 0;
        $products = Product::with(['variants.attributeValues.attribute', 'category'])->get();

        foreach ($products as $product) {
            if ($product->variants->isNotEmpty()) {
                foreach ($product->variants as $variant) {
                    static::create(static::generateFromVariant($variant));
                    $count++;
                }
            } else {
                static::create(static::generateFromProduct($product));
                $count++;
            }
        }

        return $count;
    }
}

