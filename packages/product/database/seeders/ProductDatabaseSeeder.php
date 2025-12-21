<?php

namespace Vendor\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Vendor\Product\Models\ProductFlat;

class ProductDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding Product Package Data...');
        $this->command->newLine();

        // Seed in order
        $this->call([
            ProductCategorySeeder::class,
            AttributeSeeder::class,
            ProductSeeder::class,
            ProductVariantSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('🔄 Building Product Flats...');
        
        // Rebuild product flats
        $count = ProductFlat::rebuildAll();
        $this->command->info("✓ Built {$count} product flat entries");

        $this->command->newLine();
        $this->command->info('🎉 Product package seeding completed successfully!');
        $this->command->newLine();
        
        // Display summary
        $this->displaySummary();
    }

    private function displaySummary(): void
    {
        $this->command->table(
            ['Model', 'Count'],
            [
                ['Product Categories', \Vendor\Product\Models\ProductCategory::count()],
                ['Attributes', \Vendor\Product\Models\Attribute::count()],
                ['Attribute Values', \Vendor\Product\Models\AttributeValue::count()],
                ['Products', \Vendor\Product\Models\Product::count()],
                ['Product Variants', \Vendor\Product\Models\ProductVariant::count()],
                ['Product Flats', \Vendor\Product\Models\ProductFlat::count()],
            ]
        );
    }
}

