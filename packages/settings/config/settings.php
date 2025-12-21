<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Settings Configuration
    |--------------------------------------------------------------------------
    |
    | Centralised configuration for the settings package.
    |
    */

    'cache' => [
        'enabled' => env('SETTINGS_CACHE_ENABLED', true),
        'ttl' => (int) env('SETTINGS_CACHE_TTL', 3600),
        'key' => env('SETTINGS_CACHE_KEY', 'settings.cache.all'),
    ],

    'loader' => [
        'enabled' => env('SETTINGS_AUTOLOAD', true),
        'config_key' => env('SETTINGS_CONFIG_KEY', 'site'),
    ],

    /*
     * Map database keys into logical groups so the application can
     * access them using config('site.{group}.{alias}').
     */
    'groups_map' => [
        'branding' => [
            'logo' => 'admin_logo',
            'logo_mobile' => 'logo_mobile',
            'footer_logo' => 'footer_logo',
            'favicon' => 'admin_favicon',
            'banner_image' => 'banner_image',
            'banner_image_mobile' => 'banner_image_mobile',
            'login_background' => 'admin_login_screen_backgrounds',
            'page_name' => 'page_name',
            'seo_title' => 'seo_title',
            'seo_description' => 'seo_description',
            'seo_image' => 'seo_image',
        ],
        'contact' => [
            'admin_email' => 'admin_email',
            'company_email' => 'email_company',
            'company_name' => 'name_company',
            'company_address' => 'address_company',
            'hotline' => 'hotline',
            'hotline_alt' => 'hotline_2',
        ],
        'mail' => [
            'driver' => 'email_driver',
            'host' => 'email_hostName',
            'port' => 'email_port',
            'username' => 'email_userName',
            'password' => 'email_password',
            'sender_name' => 'email_senderName',
            'sender_email' => 'email_senderEmail',
            'working_dir' => 'working_dir',
        ],
        'social' => [
            'facebook' => 'Facebook',
            'twitter' => 'Twitter',
            'linkedin' => 'Linkedin',
            'zalo' => 'Zalo',
            'youtube' => 'Youtube',
            'instagram' => 'Instagram',
            'pinterest' => 'Pinterest',
        ],
        'policy' => [
            'customer_service' => 'Customer_Service',
            'complaint_service' => 'Complaint_Service',
            'support_service' => 'Support_Service',
            'bao_hanh' => 'bao_hanh',
            'doi_tra' => 'doi_tra',
            'thanh_toan' => 'thanh_toan',
            'chinh_sach' => 'chinh_sach',
            'tuyen_dung' => 'tuyen_dung',
        ],
        'banking' => [
            'bank_name' => 'admin_bank',
            'bank_code' => 'admin_bank_code',
            'bank_number' => 'admin_bank_number',
            'qr_pay' => 'admin_qr_pay',
        ],
    ],

    'tables' => [
        'settings' => 'settings',
        'menus' => 'menus',
        'banners' => 'banners',
        'slides' => 'slides',
    ],
];
