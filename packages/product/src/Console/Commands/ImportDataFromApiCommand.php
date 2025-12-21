<?php

namespace Vendor\Product\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Vendor\Product\Models\Product;
use Vendor\Product\Models\ProductCategory;

class ImportDataFromApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:import-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from external API';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting import...');


        $products = Http::get('https://admin.deji.vn/api/products');
        $products = $products->json();
        $categories = Http::get('https://admin.deji.vn/api/categories/all');
        $categories = $categories->json();

        DB::beginTransaction();
        try {
            foreach ($categories['data'] as $category) {
                $img = $category['thumbnail'];
                $img = preg_replace('#^https?://admin.deji\.vn#i', '', $img);
                $img =  preg_replace('#^https?://deji\.vn#i', '', $img);
                //  $img = trim($img);
                $img = preg_replace('#/+#', '/', $img);
                ProductCategory::create(
                    [
                        'id' => $category['id'],
                        'name' => $category['name'],
                        'slug' => $category['slug'],
                        'description' => $category['description'],
                        //   'parent_id' => $category['parent_id'],
                        'image' => $img,
                        'is_active' => 1,
                        'is_featured' => $category['is_featured'],
                        'sort_order' => $category['order'],
                    ]
                );
            }
            foreach ($categories['data'] as $category) {
                ProductCategory::where('id', $category['id'])->update(
                    [
                        'parent_id' => $category['parent_id'],
                    ]
                );
            }
            foreach ($products['data'] as $product) {
                $img = $product['image'];
                $img = preg_replace('#^https?://admin.deji\.vn#i', '', $img);
                $img =  preg_replace('#^https?://deji\.vn#i', '', $img);
                $img = preg_replace('#/+#', '/', $img);

                $images = [];
                foreach ($product['foreign_images'] as $image) {
                    $image = preg_replace('#^https?://admin.deji\.vn#i', '', $image);
                    $image =  preg_replace('#^https?://deji\.vn#i', '', $image);
                    //  $image = trim($image);
                    $image = preg_replace('#/+#', '/', $image);
                    $images[] = $image;
                }
                $product = Product::firstOrCreate([
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'slug' => $product['slug'],
                    'description' => $product['description'],
                    'short_description' => $product['shortdescription'],
                    'price' => $product['price'],
                    //'sale_price' => $product['price_offf'],
                    // 'stock_quantity',
                    //    'sku' => 'SP' . $product['id'],

                    'images' => $images,
                    'featured_image' => $img,
                    'is_active' => $product['status'] == 'published' ? true : false,
                    'is_featured' => $product['is_featured'],
                    // 'view_count' => $product['view_count'],
                    //   'sort_order' => $product['sort_order'],
                    'meta_data' => ['specifications' => $product['specifications']],
                ]);
                $categories = [];
                foreach ($product['categories'] as $category) {
                    $categories[] = $category['id'];
                }

                $product->categories()->attach($categories);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // TODO: Implement your import logic here

        $this->info('Import completed!');

        return Command::SUCCESS;
    }
}
