<?php

namespace Vendor\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Vendor\Product\Models\Attribute;
use Vendor\Product\Models\AttributeValue;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [
            [
                'name' => 'Size',
                'slug' => 'size',
                'description' => 'Kích thước sản phẩm',
                'type' => Attribute::TYPE_SELECT,
                'is_required' => true,
                'is_visible' => true,
                'is_filterable' => true,
                'sort_order' => 1,
                'values' => [
                    ['value' => 'S', 'label' => 'Small', 'sort_order' => 1],
                    ['value' => 'M', 'label' => 'Medium', 'sort_order' => 2],
                    ['value' => 'L', 'label' => 'Large', 'sort_order' => 3],
                    ['value' => 'XL', 'label' => 'Extra Large', 'sort_order' => 4],
                    ['value' => 'XXL', 'label' => '2XL', 'sort_order' => 5],
                ],
            ],
            [
                'name' => 'Màu sắc',
                'slug' => 'color',
                'description' => 'Màu sắc sản phẩm',
                'type' => Attribute::TYPE_COLOR,
                'is_required' => true,
                'is_visible' => true,
                'is_filterable' => true,
                'sort_order' => 2,
                'values' => [
                    ['value' => 'Đen', 'label' => 'Đen', 'color_code' => '#000000', 'sort_order' => 1],
                    ['value' => 'Trắng', 'label' => 'Trắng', 'color_code' => '#FFFFFF', 'sort_order' => 2],
                    ['value' => 'Xám', 'label' => 'Xám', 'color_code' => '#808080', 'sort_order' => 3],
                    ['value' => 'Xanh Dương', 'label' => 'Xanh Dương', 'color_code' => '#3498DB', 'sort_order' => 4],
                    ['value' => 'Đỏ', 'label' => 'Đỏ', 'color_code' => '#E74C3C', 'sort_order' => 5],
                    ['value' => 'Xanh Lá', 'label' => 'Xanh Lá', 'color_code' => '#2ECC71', 'sort_order' => 6],
                    ['value' => 'Vàng', 'label' => 'Vàng', 'color_code' => '#F1C40F', 'sort_order' => 7],
                    ['value' => 'Hồng', 'label' => 'Hồng', 'color_code' => '#E91E63', 'sort_order' => 8],
                ],
            ],
            [
                'name' => 'Chất liệu',
                'slug' => 'material',
                'description' => 'Chất liệu sản phẩm',
                'type' => Attribute::TYPE_SELECT,
                'is_required' => false,
                'is_visible' => true,
                'is_filterable' => true,
                'sort_order' => 3,
                'values' => [
                    ['value' => 'Cotton', 'label' => '100% Cotton', 'price_adjustment' => 0, 'sort_order' => 1],
                    ['value' => 'Polyester', 'label' => 'Polyester', 'price_adjustment' => 0, 'sort_order' => 2],
                    ['value' => 'Cotton/Polyester', 'label' => 'Cotton/Polyester', 'price_adjustment' => 0, 'sort_order' => 3],
                    ['value' => 'Denim', 'label' => 'Denim', 'price_adjustment' => 20000, 'sort_order' => 4],
                    ['value' => 'Kaki', 'label' => 'Kaki', 'price_adjustment' => 15000, 'sort_order' => 5],
                    ['value' => 'Lụa', 'label' => 'Lụa', 'price_adjustment' => 50000, 'sort_order' => 6],
                ],
            ],
            [
                'name' => 'Kiểu dáng',
                'slug' => 'style',
                'description' => 'Kiểu dáng sản phẩm',
                'type' => Attribute::TYPE_SELECT,
                'is_required' => false,
                'is_visible' => true,
                'is_filterable' => true,
                'sort_order' => 4,
                'values' => [
                    ['value' => 'Slim Fit', 'label' => 'Slim Fit', 'sort_order' => 1],
                    ['value' => 'Regular Fit', 'label' => 'Regular Fit', 'sort_order' => 2],
                    ['value' => 'Oversize', 'label' => 'Oversize', 'sort_order' => 3],
                    ['value' => 'Skinny', 'label' => 'Skinny', 'sort_order' => 4],
                    ['value' => 'Straight', 'label' => 'Straight', 'sort_order' => 5],
                ],
            ],
        ];

        foreach ($attributes as $attributeData) {
            $values = $attributeData['values'];
            unset($attributeData['values']);

            $attribute = Attribute::create($attributeData);

            foreach ($values as $valueData) {
                $valueData['attribute_id'] = $attribute->id;
                $valueData['is_active'] = true;
                AttributeValue::create($valueData);
            }
        }

        $this->command->info('✓ Created ' . Attribute::count() . ' attributes');
        $this->command->info('✓ Created ' . AttributeValue::count() . ' attribute values');
    }
}

