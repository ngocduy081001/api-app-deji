# Lệnh Publish Package Settings

## Tổng quan

Package Settings cung cấp các lệnh publish để copy các file cấu hình, views, migrations và seeders ra ngoài package để bạn có thể tùy chỉnh.

## Các lệnh Publish

### 1. Publish tất cả (Khuyến nghị)

```bash
php artisan vendor:publish --tag=settings
```

Lệnh này sẽ publish:

- ✅ Config: `config/settings.php`
- ✅ Views: `resources/views/vendor/settings`
- ✅ Migrations: `database/migrations`
- ✅ Seeders: `database/seeders`

### 2. Publish từng phần

#### Publish Config

```bash
php artisan vendor:publish --tag=settings-config
```

Copy file config ra `config/settings.php`

#### Publish Views

```bash
php artisan vendor:publish --tag=settings-views
```

Copy views ra `resources/views/vendor/settings`

#### Publish Migrations

```bash
php artisan vendor:publish --tag=settings-migrations
```

Copy migrations ra `database/migrations`

#### Publish Seeders

```bash
php artisan vendor:publish --tag=settings-seeders
```

Copy seeders ra `database/seeders`

### 3. Publish và ghi đè file đã tồn tại

```bash
php artisan vendor:publish --tag=settings --force
```

Sử dụng `--force` để ghi đè các file đã được publish trước đó.

### 4. Publish chỉ các file đã tồn tại

```bash
php artisan vendor:publish --tag=settings --existing
```

Chỉ publish và ghi đè các file đã được publish trước đó.

## Khi nào cần Publish?

### ✅ Nên Publish khi:

- Bạn muốn tùy chỉnh views
- Bạn muốn chỉnh sửa migrations
- Bạn muốn thêm/customize seeders
- Bạn muốn thay đổi config

### ❌ Không cần Publish khi:

- Chỉ sử dụng package như một thư viện
- Không cần tùy chỉnh gì
- Muốn giữ nguyên code gốc của package

## Lưu ý

1. **Migrations**: Nếu đã chạy migrations từ package, không cần publish migrations nữa. Package tự động load migrations từ `packages/settings/database/migrations`.

2. **Views**: Views được load từ package, nhưng nếu bạn publish, Laravel sẽ ưu tiên sử dụng views đã publish.

3. **Config**: Config được merge tự động, nhưng nếu bạn publish, bạn có thể tùy chỉnh hoàn toàn.

4. **Seeders**: Nếu bạn muốn chỉnh sửa seeder, hãy publish và chỉnh sửa file đã publish.

## Ví dụ sử dụng

### Setup ban đầu (khuyến nghị)

```bash
# 1. Publish tất cả
php artisan vendor:publish --tag=settings

# 2. Chạy migrations
php artisan migrate

# 3. Chạy seeder để tạo settings mặc định
php artisan db:seed --class="Vendor\Settings\Database\Seeders\SettingsSeeder"
```

### Chỉ tùy chỉnh views

```bash
# Chỉ publish views
php artisan vendor:publish --tag=settings-views

# Sau đó chỉnh sửa file trong resources/views/vendor/settings
```

### Chỉ tùy chỉnh config

```bash
# Chỉ publish config
php artisan vendor:publish --tag=settings-config

# Sau đó chỉnh sửa file config/settings.php
```

## Kiểm tra các tags có sẵn

Để xem tất cả các tags publish có sẵn:

```bash
php artisan vendor:publish
```

Lệnh này sẽ hiển thị danh sách tất cả các packages và tags có thể publish.
