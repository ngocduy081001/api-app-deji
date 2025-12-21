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
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained('attributes')->onDelete('cascade');
            $table->string('value'); // VD: "S", "M", "L", "Red", "Blue"
            $table->string('label')->nullable(); // Label hiển thị (nếu khác value)
            $table->string('color_code')->nullable(); // Mã màu nếu type là color (#FF0000)
            $table->string('image')->nullable(); // Ảnh cho giá trị (VD: ảnh màu sắc)
            $table->decimal('price_adjustment', 15, 2)->default(0); // Điều chỉnh giá (+/-)
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('attribute_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
    }
};

