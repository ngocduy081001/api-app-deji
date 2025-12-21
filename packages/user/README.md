# User Management Package

Package quản lý người dùng cho Laravel.

## Cài đặt

Package đã được cấu hình trong `composer.json` và `bootstrap/providers.php`.

Chạy lệnh sau để cập nhật autoload:

```bash
composer dump-autoload
```

## Tính năng

- Quản lý danh sách người dùng
- Tạo người dùng mới
- Chỉnh sửa thông tin người dùng
- Xem chi tiết người dùng
- Xóa người dùng (không thể xóa chính mình)
- Tìm kiếm người dùng theo tên hoặc email

## Routes

Tất cả routes được đăng ký với prefix `admin/users` và yêu cầu authentication và admin middleware:

- `GET /admin/users` - Danh sách người dùng
- `GET /admin/users/create` - Form tạo người dùng mới
- `POST /admin/users` - Lưu người dùng mới
- `GET /admin/users/{user}` - Chi tiết người dùng
- `GET /admin/users/{user}/edit` - Form chỉnh sửa người dùng
- `PUT /admin/users/{user}` - Cập nhật người dùng
- `DELETE /admin/users/{user}` - Xóa người dùng

## Cấu hình

File cấu hình: `config/user.php`

- `enabled`: Bật/tắt package
- `model`: Model class sử dụng cho users (mặc định: `App\Models\User`)
- `per_page`: Số lượng items mỗi trang (mặc định: 20)

## Views

Tất cả views được đặt trong `resources/views/admin/`:

- `index.blade.php` - Danh sách người dùng
- `create.blade.php` - Form tạo mới
- `edit.blade.php` - Form chỉnh sửa
- `show.blade.php` - Chi tiết người dùng

## Menu

Package tự động đăng ký menu item "Người dùng" trong admin sidebar với icon "users".
