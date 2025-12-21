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
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('warranty_code')->unique();
            $table->string('status')->default('clear')->enum('clear', 'active', 'expired'); // clear, active, expired, etc.
            $table->dateTime('active_date')->nullable();
            $table->dateTime('time_expired')->nullable();
            $table->integer('month')->default(12); // Warranty duration in months
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->timestamps();

            // Indexes for performance
            $table->index('product_id');
            $table->index('warranty_code');
            $table->index('status');
            $table->index('customer_id');
            $table->index('active_date');
            $table->index('time_expired');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranties');
    }
};
