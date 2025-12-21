<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateParrentCategory extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        DB::table('product_categories')->get()->each(function ($category) {
            $category_v2 = DB::table('product_categories_v2')->where('id', $category->id)->first()->parent_id;
            DB::table('product_categories')->where('id', $category->id)->update(['parent_id' => $category_v2]);
        });
    }
}
