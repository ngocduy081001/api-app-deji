<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menu_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->string('location')->default('header'); // header, footer, sidebar, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['location', 'is_active']);
        });

        DB::table('menu_groups')->insert([

            [
                'id' => 1,
                'name' => 'Menu Main',
                'slug' => 'main',
                'description' => 'Menu chính hiển thị ở nền trắng',
                'location' => 'main',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Menu Category',
                'slug' => 'category',
                'description' => 'Menu hiển thị ở nền đen',
                'location' => 'footer',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_groups');
    }
};
