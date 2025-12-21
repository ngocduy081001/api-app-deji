<?php

namespace Vendor\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Vendor\Settings\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding Settings...');
        $this->command->newLine();

        $settings = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'Ecommerce Store',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Tên website',
                'order' => 1,
            ],
            [
                'key' => 'site_logo',
                'value' => '',
                'type' => 'image',
                'group' => 'general',
                'description' => 'Logo website',
                'order' => 2,
            ],
            [
                'key' => 'site_favicon',
                'value' => '',
                'type' => 'image',
                'group' => 'general',
                'description' => 'Favicon website',
                'order' => 3,
            ],
            [
                'key' => 'site_description',
                'value' => 'Mô tả website của bạn',
                'type' => 'textarea',
                'group' => 'general',
                'description' => 'Mô tả chung về website',
                'order' => 4,
            ],

            // Contact Settings
            [
                'key' => 'contact_email',
                'value' => 'contact@example.com',
                'type' => 'email',
                'group' => 'contact',
                'description' => 'Email liên hệ',
                'order' => 1,
            ],
            [
                'key' => 'contact_phone',
                'value' => '0123456789',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'Số điện thoại liên hệ',
                'order' => 2,
            ],
            [
                'key' => 'contact_address',
                'value' => 'Địa chỉ của bạn',
                'type' => 'textarea',
                'group' => 'contact',
                'description' => 'Địa chỉ liên hệ',
                'order' => 3,
            ],
            [
                'key' => 'contact_hotline',
                'value' => '1900xxxx',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'Hotline',
                'order' => 4,
            ],

            // SEO Settings
            [
                'key' => 'seo_title',
                'value' => 'Ecommerce Store - Trang chủ',
                'type' => 'text',
                'group' => 'seo',
                'description' => 'Tiêu đề SEO mặc định',
                'order' => 1,
            ],
            [
                'key' => 'seo_description',
                'value' => 'Mô tả SEO mặc định cho website',
                'type' => 'textarea',
                'group' => 'seo',
                'description' => 'Mô tả SEO mặc định',
                'order' => 2,
            ],
            [
                'key' => 'seo_keywords',
                'value' => 'ecommerce, shop, online',
                'type' => 'text',
                'group' => 'seo',
                'description' => 'Từ khóa SEO',
                'order' => 3,
            ],
            [
                'key' => 'seo_og_image',
                'value' => '',
                'type' => 'image',
                'group' => 'seo',
                'description' => 'Hình ảnh Open Graph',
                'order' => 4,
            ],

            // Social Media Settings
            [
                'key' => 'social_facebook',
                'value' => '',
                'type' => 'text',
                'group' => 'social',
                'description' => 'Link Facebook',
                'order' => 1,
            ],
            [
                'key' => 'social_instagram',
                'value' => '',
                'type' => 'text',
                'group' => 'social',
                'description' => 'Link Instagram',
                'order' => 2,
            ],
            [
                'key' => 'social_twitter',
                'value' => '',
                'type' => 'text',
                'group' => 'social',
                'description' => 'Link Twitter/X',
                'order' => 3,
            ],
            [
                'key' => 'social_youtube',
                'value' => '',
                'type' => 'text',
                'group' => 'social',
                'description' => 'Link YouTube',
                'order' => 4,
            ],
            [
                'key' => 'social_zalo',
                'value' => '',
                'type' => 'text',
                'group' => 'social',
                'description' => 'Link Zalo',
                'order' => 5,
            ],
            [
                'key' => 'social_tiktok',
                'value' => '',
                'type' => 'text',
                'group' => 'social',
                'description' => 'Link TikTok',
                'order' => 6,
            ],

            // Appearance Settings
            [
                'key' => 'primary_color',
                'value' => '#000000',
                'type' => 'text',
                'group' => 'appearance',
                'description' => 'Màu chủ đạo',
                'order' => 1,
            ],
            [
                'key' => 'secondary_color',
                'value' => '#666666',
                'type' => 'text',
                'group' => 'appearance',
                'description' => 'Màu phụ',
                'order' => 2,
            ],
            [
                'key' => 'header_background',
                'value' => '#ffffff',
                'type' => 'text',
                'group' => 'appearance',
                'description' => 'Màu nền header',
                'order' => 3,
            ],
            [
                'key' => 'footer_background',
                'value' => '#f5f5f5',
                'type' => 'text',
                'group' => 'appearance',
                'description' => 'Màu nền footer',
                'order' => 4,
            ],

            // Email Settings
            [
                'key' => 'email_from_name',
                'value' => 'Ecommerce Store',
                'type' => 'text',
                'group' => 'email',
                'description' => 'Tên người gửi email',
                'order' => 1,
            ],
            [
                'key' => 'email_from_address',
                'value' => 'noreply@example.com',
                'type' => 'email',
                'group' => 'email',
                'description' => 'Địa chỉ email gửi',
                'order' => 2,
            ],
            [
                'key' => 'email_admin',
                'value' => 'admin@example.com',
                'type' => 'email',
                'group' => 'email',
                'description' => 'Email quản trị viên',
                'order' => 3,
            ],

            // Payment Settings
            [
                'key' => 'payment_methods',
                'value' => 'cod,bank_transfer,credit_card',
                'type' => 'text',
                'group' => 'payment',
                'description' => 'Các phương thức thanh toán (phân cách bằng dấu phẩy)',
                'order' => 1,
            ],
            [
                'key' => 'cod_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'payment',
                'description' => 'Bật thanh toán khi nhận hàng',
                'order' => 2,
            ],

            // Shipping Settings
            [
                'key' => 'shipping_fee',
                'value' => '30000',
                'type' => 'number',
                'group' => 'shipping',
                'description' => 'Phí vận chuyển mặc định (VNĐ)',
                'order' => 1,
            ],
            [
                'key' => 'free_ship_threshold',
                'value' => '500000',
                'type' => 'number',
                'group' => 'shipping',
                'description' => 'Giá trị đơn hàng để được miễn phí ship (VNĐ)',
                'order' => 2,
            ],
        ];

        $count = 0;
        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
            $count++;
        }

        $this->command->info("✓ Created/Updated {$count} settings");
        $this->command->newLine();

        // Display summary by group
        $groups = Setting::select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        $this->command->info('Settings by group:');
        foreach ($groups as $group) {
            $groupCount = Setting::where('group', $group)->count();
            $this->command->info("  - {$group}: {$groupCount} settings");
        }
    }
}
