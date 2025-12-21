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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_group_id')->constrained('menu_groups')->onDelete('cascade');
            $table->string('type')->default('custom'); // custom, category, page
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->onDelete('set null');
            $table->foreignId('page_id')->nullable()->constrained('pages')->onDelete('set null');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('url')->nullable();
            $table->string('route')->nullable();
            $table->string('icon')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('menus')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->string('target')->nullable(); // _blank, _self, etc.
            $table->boolean('is_active')->default(true);
            $table->json('attributes')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('menu_group_id');
            $table->index('type');
            $table->index('parent_id');
            $table->index('order');
            $table->index('is_active');
            $table->index(['menu_group_id', 'parent_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
