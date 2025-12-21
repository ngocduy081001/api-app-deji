<?php

namespace Vendor\News\Database\Seeders;

use Illuminate\Database\Seeder;
use Vendor\News\Models\NewsCategory;

class NewsCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Công nghệ',
                'slug' => 'cong-nghe',
                'description' => 'Tin tức về công nghệ, khoa học và kỹ thuật',
                'is_active' => true,
                'sort_order' => 1,
                'children' => [
                    [
                        'name' => 'Điện thoại',
                        'slug' => 'dien-thoai',
                        'description' => 'Tin tức về điện thoại thông minh',
                        'is_active' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Laptop',
                        'slug' => 'laptop',
                        'description' => 'Tin tức về máy tính xách tay',
                        'is_active' => true,
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'AI & Machine Learning',
                        'slug' => 'ai-machine-learning',
                        'description' => 'Tin tức về trí tuệ nhân tạo và máy học',
                        'is_active' => true,
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Kinh doanh',
                'slug' => 'kinh-doanh',
                'description' => 'Tin tức về kinh doanh và khởi nghiệp',
                'is_active' => true,
                'sort_order' => 2,
                'children' => [
                    [
                        'name' => 'Khởi nghiệp',
                        'slug' => 'khoi-nghiep',
                        'description' => 'Tin tức về các startup và khởi nghiệp',
                        'is_active' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Thị trường',
                        'slug' => 'thi-truong',
                        'description' => 'Tin tức về thị trường chứng khoán',
                        'is_active' => true,
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Giải trí',
                'slug' => 'giai-tri',
                'description' => 'Tin tức về giải trí, phim ảnh, âm nhạc',
                'is_active' => true,
                'sort_order' => 3,
                'children' => [
                    [
                        'name' => 'Phim ảnh',
                        'slug' => 'phim-anh',
                        'description' => 'Tin tức về phim ảnh',
                        'is_active' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Âm nhạc',
                        'slug' => 'am-nhac',
                        'description' => 'Tin tức về âm nhạc',
                        'is_active' => true,
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Thể thao',
                'slug' => 'the-thao',
                'description' => 'Tin tức về thể thao',
                'is_active' => true,
                'sort_order' => 4,
                'children' => [
                    [
                        'name' => 'Bóng đá',
                        'slug' => 'bong-da',
                        'description' => 'Tin tức về bóng đá',
                        'is_active' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Tennis',
                        'slug' => 'tennis',
                        'description' => 'Tin tức về tennis',
                        'is_active' => true,
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Du lịch',
                'slug' => 'du-lich',
                'description' => 'Tin tức về du lịch và khám phá',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Sức khỏe',
                'slug' => 'suc-khoe',
                'description' => 'Tin tức về sức khỏe và y tế',
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $category = NewsCategory::create($categoryData);

            // Create children categories
            foreach ($children as $childData) {
                $childData['parent_id'] = $category->id;
                NewsCategory::create($childData);
            }
        }

        $this->command->info('News categories seeded successfully!');
    }
}

