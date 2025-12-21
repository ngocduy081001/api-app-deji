<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_flats', function (Blueprint $table) {
            $table->id();
            
            // Product Info
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('product_name');
            $table->string('product_slug');
            $table->text('product_description')->nullable();
            $table->text('product_short_description')->nullable();
            $table->string('product_sku');
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->onDelete('set null');
            $table->string('category_name')->nullable();
            
            // Variant Info (null if product has no variants or is main product entry)
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
            $table->string('variant_name')->nullable();
            $table->string('variant_sku')->nullable();
            $table->json('variant_attributes')->nullable(); // {"size": "M", "color": "Red"}
            $table->string('variant_image')->nullable();
            
            // Pricing (final calculated prices)
            $table->decimal('price', 15, 2); // Base price
            $table->decimal('sale_price', 15, 2)->nullable(); // Sale price if available
            $table->decimal('final_price', 15, 2); // Final price (including all adjustments)
            $table->decimal('price_adjustment', 15, 2)->default(0); // From attribute values
            $table->boolean('is_on_sale')->default(false);
            $table->decimal('discount_percentage', 5, 2)->nullable();
            
            // Inventory
            $table->integer('stock_quantity')->default(0);
            $table->boolean('is_in_stock')->default(false);
            
            // Images
            $table->string('featured_image')->nullable();
            $table->json('images')->nullable();
            
            // Attributes for filtering (denormalized for fast queries)
            $table->json('attributes_flat')->nullable(); // All attributes in flat format
            
            // Status flags
            $table->boolean('product_is_active')->default(true);
            $table->boolean('product_is_featured')->default(false);
            $table->boolean('variant_is_active')->default(true);
            $table->boolean('is_available')->default(true); // Product & Variant both active
            
            // Meta
            $table->integer('product_view_count')->default(0);
            $table->integer('product_sort_order')->default(0);
            $table->integer('variant_sort_order')->default(0);
            
            // Searchable text (for full-text search)
            $table->text('searchable_text')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('product_id');
            $table->index('variant_id');
            $table->index('category_id');
            $table->index('product_slug');
            $table->index('product_sku');
            $table->index('variant_sku');
            $table->index(['final_price', 'is_available']);
            $table->index(['is_in_stock', 'is_available']);
            $table->index('is_on_sale');
            $table->index('product_is_featured');
            $table->index(['product_is_active', 'variant_is_active']);
            
            // Composite indexes for common queries
            $table->index(['category_id', 'is_available', 'is_in_stock']);
            $table->index(['is_available', 'final_price']);
            
            // Full-text index for search
            $table->fullText(['product_name', 'product_description', 'searchable_text'], 'product_flats_search_fulltext');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_flats');
    }
};

