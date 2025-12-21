<?php

namespace Vendor\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Vendor\Product\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Thời trang Nam',
                'slug' => 'thoi-trang-nam',
                'description' => 'Tất cả sản phẩm thời trang dành cho nam giới',
                'image' => 'https://via.placeholder.com/400x300/3498DB/ffffff?text=Thoi+Trang+Nam',
                'is_active' => true,
                'sort_order' => 1,
                'children' => [
                    [
                        'name' => 'Áo Nam',
                        'slug' => 'ao-nam',
                        'description' => 'Áo thun, áo sơ mi, áo khoác nam',
                        'image' => 'https://via.placeholder.com/400x300/2ECC71/ffffff?text=Ao+Nam',
                        'is_active' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Quần Nam',
                        'slug' => 'quan-nam',
                        'description' => 'Quần jean, quần kaki, quần short',
                        'image' => 'https://via.placeholder.com/400x300/E74C3C/ffffff?text=Quan+Nam',
                        'is_active' => true,
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Phụ kiện Nam',
                        'slug' => 'phu-kien-nam',
                        'description' => 'Giày dép, túi xách, thắt lưng',
                        'image' => 'https://via.placeholder.com/400x300/F39C12/ffffff?text=Phu+Kien+Nam',
                        'is_active' => true,
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Thời trang Nữ',
                'slug' => 'thoi-trang-nu',
                'description' => 'Tất cả sản phẩm thời trang dành cho nữ giới',
                'image' => 'https://via.placeholder.com/400x300/E91E63/ffffff?text=Thoi+Trang+Nu',
                'is_active' => true,
                'sort_order' => 2,
                'children' => [
                    [
                        'name' => 'Áo Nữ',
                        'slug' => 'ao-nu',
                        'description' => 'Áo kiểu, áo thun, áo sơ mi nữ',
                        'image' => 'https://via.placeholder.com/400x300/9C27B0/ffffff?text=Ao+Nu',
                        'is_active' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Váy Đầm',
                        'slug' => 'vay-dam',
                        'description' => 'Váy công sở, váy dạ hội, đầm dự tiệc',
                        'image' => 'https://via.placeholder.com/400x300/FF5722/ffffff?text=Vay+Dam',
                        'is_active' => true,
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Quần Nữ',
                        'slug' => 'quan-nu',
                        'description' => 'Quần jean, quần tây, quần short nữ',
                        'image' => 'https://via.placeholder.com/400x300/00BCD4/ffffff?text=Quan+Nu',
                        'is_active' => true,
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Đồ Thể Thao',
                'slug' => 'do-the-thao',
                'description' => 'Trang phục và phụ kiện thể thao',
                'image' => 'https://via.placeholder.com/400x300/4CAF50/ffffff?text=Do+The+Thao',
                'is_active' => true,
                'sort_order' => 3,
                'children' => [
                    [
                        'name' => 'Giày Thể Thao',
                        'slug' => 'giay-the-thao',
                        'description' => 'Giày chạy bộ, giày tập gym',
                        'image' => 'https://via.placeholder.com/400x300/009688/ffffff?text=Giay+The+Thao',
                        'is_active' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Áo Thể Thao',
                        'slug' => 'ao-the-thao',
                        'description' => 'Áo tập gym, áo chạy bộ',
                        'image' => 'https://via.placeholder.com/400x300/8BC34A/ffffff?text=Ao+The+Thao',
                        'is_active' => true,
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Đồ Trẻ Em',
                'slug' => 'do-tre-em',
                'description' => 'Thời trang cho trẻ em',
                'image' => 'https://via.placeholder.com/400x300/FF9800/ffffff?text=Do+Tre+Em',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Đồ Điện Tử',
                'slug' => 'do-dien-tu',
                'description' => 'Thiết bị điện tử và phụ kiện',
                'image' => 'https://via.placeholder.com/400x300/607D8B/ffffff?text=Do+Dien+Tu',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $parentCategory = ProductCategory::create($categoryData);

            foreach ($children as $childData) {
                $childData['parent_id'] = $parentCategory->id;
                ProductCategory::create($childData);
            }
        }

        $this->command->info('✓ Created ' . ProductCategory::count() . ' product categories');
    }
}

