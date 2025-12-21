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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('name'); // Tên biến thể (VD: "Size M - Màu Đỏ")
            $table->string('sku')->unique()->nullable(); // SKU riêng cho biến thể
            $table->json('attributes'); // Lưu các thuộc tính như {"size": "M", "color": "Đỏ"}
            $table->decimal('price', 15, 2)->nullable(); // Giá riêng (null = dùng giá của product)
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->string('image')->nullable(); // Ảnh riêng cho biến thể
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('product_id');
            $table->index('sku');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};

