<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Vendor\Product\Models\ProductCategory;
use Illuminate\Support\Facades\DB;

class UpdateCategoryLinkSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     * 
     * This seeder updates the 'link' field for all categories
     * by setting it to the value of the 'slug' field.
     */
    public function run(): void
    {
        $this->command->info('Updating category links...');

        $categories = ProductCategory::all();
        $updatedCount = 0;

        foreach ($categories as $category) {
            if ($category->slug) {
                DB::table('product_categories')
                    ->where('id', $category->id)
                    ->update(['link' => $category->slug]);

                $updatedCount++;
            }
        }

        $this->command->info("Updated {$updatedCount} categories with link = slug.");
    }
}
