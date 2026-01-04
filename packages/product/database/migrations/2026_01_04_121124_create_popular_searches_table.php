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
        Schema::create('popular_searches', function (Blueprint $table) {
            $table->id();
            $table->string('keyword', 255)->unique();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('click_count')->default(0); // Track clicks for analytics
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('is_active');
            $table->index('sort_order');
            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('popular_searches');
    }
};
