<?php

namespace Vendor\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Vendor\Product\Models\Product;
use Vendor\Product\Models\ProductCategory;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ProductCategory::all();
        
        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Please run ProductCategorySeeder first.');
            return;
        }

        $products = [
            // Áo Nam
            [
                'name' => 'Áo Thun Nam Basic',
                'description' => 'Áo thun nam basic form rộng, chất liệu cotton 100% mềm mại, thoáng mát. Thiết kế tối giản phù hợp mọi phong cách.',
                'short_description' => 'Áo thun nam basic, chất cotton 100%',
                'price' => 199000,
                'sale_price' => 149000,
                'stock_quantity' => 0, // Will use variants
                'category_slug' => 'ao-nam',
                'featured_image' => 'https://via.placeholder.com/800x600/2C3E50/ffffff?text=Ao+Thun+Nam+Basic',
                'is_featured' => true,
            ],
            [
                'name' => 'Áo Sơ Mi Nam Trắng',
                'description' => 'Áo sơ mi nam trắng công sở, chất liệu cotton pha, form slim fit tôn dáng. Phù hợp đi làm và đi chơi.',
                'short_description' => 'Áo sơ mi nam công sở, form slim fit',
                'price' => 350000,
                'sale_price' => null,
                'stock_quantity' => 0,
                'category_slug' => 'ao-nam',
                'featured_image' => 'https://via.placeholder.com/800x600/ECF0F1/2C3E50?text=Ao+So+Mi+Nam',
                'is_featured' => true,
            ],
            [
                'name' => 'Áo Polo Nam',
                'description' => 'Áo polo nam cao cấp, chất liệu pique cotton thoáng mát, thiết kế cổ bẻ sang trọng.',
                'short_description' => 'Áo polo nam cao cấp',
                'price' => 250000,
                'sale_price' => 199000,
                'stock_quantity' => 0,
                'category_slug' => 'ao-nam',
                'featured_image' => 'https://via.placeholder.com/800x600/3498DB/ffffff?text=Ao+Polo+Nam',
                'is_featured' => false,
            ],
            
            // Quần Nam
            [
                'name' => 'Quần Jean Nam Slim Fit',
                'description' => 'Quần jean nam slim fit, chất liệu denim cao cấp co giãn 4 chiều, form dáng ôm vừa phải tôn dáng.',
                'short_description' => 'Quần jean nam slim fit co giãn',
                'price' => 450000,
                'sale_price' => 350000,
                'stock_quantity' => 0,
                'category_slug' => 'quan-nam',
                'featured_image' => 'https://via.placeholder.com/800x600/34495E/ffffff?text=Quan+Jean+Nam',
                'is_featured' => true,
            ],
            [
                'name' => 'Quần Kaki Nam',
                'description' => 'Quần kaki nam công sở, chất liệu kaki cao cấp, form regular fit thoải mái.',
                'short_description' => 'Quần kaki nam công sở',
                'price' => 380000,
                'sale_price' => null,
                'stock_quantity' => 0,
                'category_slug' => 'quan-nam',
                'featured_image' => 'https://via.placeholder.com/800x600/7F8C8D/ffffff?text=Quan+Kaki+Nam',
                'is_featured' => false,
            ],

            // Áo Nữ
            [
                'name' => 'Áo Kiểu Nữ Công Sở',
                'description' => 'Áo kiểu nữ công sở thiết kế thanh lịch, chất liệu voan mềm mại, thoáng mát.',
                'short_description' => 'Áo kiểu nữ công sở thanh lịch',
                'price' => 280000,
                'sale_price' => 220000,
                'stock_quantity' => 0,
                'category_slug' => 'ao-nu',
                'featured_image' => 'https://via.placeholder.com/800x600/E91E63/ffffff?text=Ao+Kieu+Nu',
                'is_featured' => true,
            ],
            [
                'name' => 'Áo Thun Nữ Basic',
                'description' => 'Áo thun nữ basic form rộng thoải mái, chất cotton 100% mềm mại.',
                'short_description' => 'Áo thun nữ basic cotton',
                'price' => 180000,
                'sale_price' => 149000,
                'stock_quantity' => 0,
                'category_slug' => 'ao-nu',
                'featured_image' => 'https://via.placeholder.com/800x600/9C27B0/ffffff?text=Ao+Thun+Nu',
                'is_featured' => false,
            ],

            // Váy Đầm
            [
                'name' => 'Váy Công Sở',
                'description' => 'Váy công sở thiết kế thanh lịch, chất liệu thoáng mát, form dáng tôn dáng.',
                'short_description' => 'Váy công sở thanh lịch',
                'price' => 450000,
                'sale_price' => 380000,
                'stock_quantity' => 0,
                'category_slug' => 'vay-dam',
                'featured_image' => 'https://via.placeholder.com/800x600/FF5722/ffffff?text=Vay+Cong+So',
                'is_featured' => true,
            ],

            // Giày Thể Thao
            [
                'name' => 'Giày Chạy Bộ',
                'description' => 'Giày chạy bộ chuyên nghiệp, đế êm, thoáng khí, hỗ trợ vận động tối ưu.',
                'short_description' => 'Giày chạy bộ chuyên nghiệp',
                'price' => 890000,
                'sale_price' => 750000,
                'stock_quantity' => 0,
                'category_slug' => 'giay-the-thao',
                'featured_image' => 'https://via.placeholder.com/800x600/009688/ffffff?text=Giay+Chay+Bo',
                'is_featured' => true,
            ],

            // Áo Thể Thao
            [
                'name' => 'Áo Thể Thao Nam',
                'description' => 'Áo thể thao nam chất liệu polyester thoáng mát, thấm hút mồ hôi tốt.',
                'short_description' => 'Áo thể thao nam thoáng mát',
                'price' => 220000,
                'sale_price' => 180000,
                'stock_quantity' => 0,
                'category_slug' => 'ao-the-thao',
                'featured_image' => 'https://via.placeholder.com/800x600/8BC34A/ffffff?text=Ao+The+Thao',
                'is_featured' => false,
            ],
        ];

        foreach ($products as $productData) {
            $categorySlug = $productData['category_slug'];
            unset($productData['category_slug']);

            $category = $categories->firstWhere('slug', $categorySlug);
            if ($category) {
                $productData['category_id'] = $category->id;
                $productData['images'] = [
                    $productData['featured_image'],
                    str_replace('?text=', '?text=View+2+-+', $productData['featured_image']),
                    str_replace('?text=', '?text=View+3+-+', $productData['featured_image']),
                ];
                $productData['is_active'] = true;
                $productData['view_count'] = rand(10, 500);
                $productData['sort_order'] = rand(0, 100);
          
                Product::create($productData);
            }
        }

        $this->command->info('✓ Created ' . Product::count() . ' products');
    }
}

