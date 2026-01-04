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
        Schema::create('search_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('customers')->onDelete('cascade');
            $table->string('keyword', 255);
            $table->timestamp('searched_at')->useCurrent();
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id');
            $table->index('keyword');
            $table->index('searched_at');
            $table->index(['user_id', 'searched_at']);
            
            // Prevent duplicate keywords per user (update searched_at instead)
            $table->unique(['user_id', 'keyword']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_history');
    }
};
