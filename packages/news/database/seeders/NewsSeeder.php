<?php

namespace Vendor\News\Database\Seeders;

use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            NewsCategorySeeder::class,
            ArticleSeeder::class,
        ]);

        $this->command->info('News module seeded successfully!');
    }
}

