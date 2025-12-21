# Settings Package

Package quản lý cài đặt hệ thống cho Laravel, bao gồm:

- **Settings**: Cài đặt hệ thống (email, logo, SEO tags, ...)
- **Menus**: Quản lý menu động
- **Banners**: Quản lý banner
- **Slides**: Quản lý slide

## Cài đặt

Package đã được đăng ký trong `composer.json` và `bootstrap/providers.php`.

## Migration

Chạy migrations để tạo các bảng:

```bash
php artisan migrate
```

## Publish Package

### Publish tất cả (khuyến nghị)

```bash
php artisan vendor:publish --tag=settings
```

Lệnh này sẽ publish:

- Config: `config/settings.php`
- Views: `resources/views/vendor/settings`
- Migrations: `database/migrations`
- Seeders: `database/seeders`

### Publish từng phần

```bash
# Publish config
php artisan vendor:publish --tag=settings-config

# Publish views
php artisan vendor:publish --tag=settings-views

# Publish migrations
php artisan vendor:publish --tag=settings-migrations

# Publish seeders
php artisan vendor:publish --tag=settings-seeders
```

### Publish và ghi đè file đã tồn tại

```bash
php artisan vendor:publish --tag=settings --force
```

## Seeder

Chạy seeder để tạo các settings mặc định:

```bash
php artisan db:seed --class="Vendor\Settings\Database\Seeders\SettingsSeeder"
```

Hoặc thêm vào `DatabaseSeeder`:

```php
$this->call([
    Vendor\Settings\Database\Seeders\SettingsSeeder::class,
]);
```

## Sử dụng

### Settings Helper

```php
use Vendor\Settings\Helpers\SettingsHelper;

// Lấy giá trị setting
$logo = SettingsHelper::get('site_logo', 'default-logo.png');
$email = SettingsHelper::get('contact_email', 'contact@example.com');

// Lưu giá trị setting
SettingsHelper::set('site_logo', '/path/to/logo.png', 'image', 'general');

// Lấy tất cả settings theo nhóm
$generalSettings = SettingsHelper::getByGroup('general');

// Lấy settings dạng array (key => value)
$settings = SettingsHelper::getArray('seo'); // Lấy nhóm seo
$allSettings = SettingsHelper::getArray(); // Lấy tất cả

// Lấy nhiều settings theo keys
$settings = SettingsHelper::getMultiple(['site_name', 'site_logo', 'contact_email']);
```

### Model Setting

```php
use Vendor\Settings\Models\Setting;

// Lấy giá trị
$logo = Setting::getValue('site_logo', 'default-logo.png');
$email = Setting::getValue('contact_email', 'contact@example.com');

// Lưu giá trị
Setting::setValue('site_logo', '/path/to/logo.png', 'image', 'general');
Setting::setValue('contact_email', 'new@example.com', 'email', 'contact');

// Lấy settings theo nhóm
$seoSettings = Setting::getByGroup('seo');
```

### Menus

Lấy menu theo vị trí:

```php
use Vendor\Settings\Models\Menu;

$headerMenus = Menu::byLocation('header')->active()->root()->orderBy('order')->get();
```

### Banners

Lấy banner theo vị trí:

```php
use Vendor\Settings\Models\Banner;

$topBanners = Banner::byPosition('top')->active()->orderBy('order')->get();
```

### Slides

Lấy slides đang hoạt động:

```php
use Vendor\Settings\Models\Slide;

$slides = Slide::active()->orderBy('order')->get();
```

## API Routes

### Get Settings

```bash
# Lấy tất cả settings
GET /api/settings

# Lấy settings theo nhóm (position)
GET /api/settings/group/{group}
# Ví dụ: GET /api/settings/group/seo

# Lấy setting theo key
GET /api/settings/{key}
# Ví dụ: GET /api/settings/site_logo

# Lấy settings theo query params
GET /api/settings?group=seo
GET /api/settings?keys=site_name,site_logo,contact_email

# Lấy nhiều settings theo keys (POST)
POST /api/settings/keys
Body: { "keys": ["site_name", "site_logo", "contact_email"] }
```

### Response Format

```json
{
  "success": true,
  "data": {
    "site_name": "Ecommerce Store",
    "site_logo": "/path/to/logo.png",
    "contact_email": "contact@example.com"
  }
}
```

## Các nhóm Settings (Groups/Areas)

- **general**: Cài đặt chung (site_name, site_logo, site_description, ...)
- **contact**: Thông tin liên hệ (contact_email, contact_phone, contact_address, ...)
- **seo**: SEO (seo_title, seo_description, seo_keywords, seo_og_image)
- **social**: Mạng xã hội (social_facebook, social_instagram, social_twitter, ...)
- **appearance**: Giao diện (primary_color, secondary_color, header_background, ...)
- **email**: Email (email_from_name, email_from_address, email_admin)
- **payment**: Thanh toán (payment_methods, cod_enabled)
- **shipping**: Vận chuyển (shipping_fee, free_ship_threshold)

## Admin Routes

- `/admin/settings` - Quản lý cài đặt hệ thống
- `/admin/menus` - Quản lý menu
- `/admin/banners` - Quản lý banner
- `/admin/slides` - Quản lý slide

## Cấu trúc

```
packages/settings/
├── config/
│   └── settings.php
├── database/
│   ├── migrations/
│   │   ├── 2025_01_15_000001_create_settings_table.php
│   │   ├── 2025_01_15_000002_create_menus_table.php
│   │   ├── 2025_01_15_000003_create_banners_table.php
│   │   └── 2025_01_15_000004_create_slides_table.php
│   └── seeders/
│       └── SettingsSeeder.php
├── resources/
│   └── views/
│       └── admin/
│           ├── settings/
│           ├── menus/
│           ├── banners/
│           └── slides/
├── routes/
│   ├── web.php
│   └── api.php
└── src/
    ├── Helpers/
    │   └── SettingsHelper.php
    ├── Http/
    │   └── Controllers/
    │       ├── Api/
    │       │   └── SettingsApiController.php
    │       └── Web/
    ├── Models/
    │   ├── Setting.php
    │   ├── Menu.php
    │   ├── Banner.php
    │   └── Slide.php
    └── SettingsServiceProvider.php
```
