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
        Schema::table('product_flats', function (Blueprint $table) {
            $table->decimal('price_off', 15, 2)->default(0);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price_off', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_flats', function (Blueprint $table) {
            $table->dropColumn('price_off');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('price_off');
        });
    }
};
