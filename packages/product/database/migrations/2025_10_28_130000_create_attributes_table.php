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
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // VD: "Size", "Color", "Material"
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type')->default('select'); // select, color, text, number
            $table->boolean('is_required')->default(false); // Bắt buộc khi tạo variant
            $table->boolean('is_visible')->default(true); // Hiển thị cho khách hàng
            $table->boolean('is_filterable')->default(true); // Có thể dùng để lọc sản phẩm
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('is_visible');
            $table->index('is_filterable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};

