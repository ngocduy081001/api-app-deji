# Customer Management Package

Package quản lý khách hàng cho Laravel.

## Cài đặt

Package đã được cấu hình trong `composer.json` và `bootstrap/providers.php`.

Chạy lệnh sau để cập nhật autoload:

```bash
composer dump-autoload
```

## Tính năng

- Quản lý danh sách khách hàng
- Tạo khách hàng mới
- Chỉnh sửa thông tin khách hàng
- Xem chi tiết khách hàng
- Xóa khách hàng
- Tìm kiếm khách hàng theo tên, email hoặc số điện thoại

## Routes

Tất cả routes được đăng ký với prefix `admin/customers` và yêu cầu authentication và admin middleware:

- `GET /admin/customers` - Danh sách khách hàng
- `GET /admin/customers/create` - Form tạo khách hàng mới
- `POST /admin/customers` - Lưu khách hàng mới
- `GET /admin/customers/{customer}` - Chi tiết khách hàng
- `GET /admin/customers/{customer}/edit` - Form chỉnh sửa khách hàng
- `PUT /admin/customers/{customer}` - Cập nhật khách hàng
- `DELETE /admin/customers/{customer}` - Xóa khách hàng

## Cấu hình

File cấu hình: `config/customer.php`

- `enabled`: Bật/tắt package
- `model`: Model class sử dụng cho customers (mặc định: `App\Models\Customer`)
- `per_page`: Số lượng items mỗi trang (mặc định: 20)

## Views

Tất cả views được đặt trong `resources/views/admin/`:

- `index.blade.php` - Danh sách khách hàng
- `create.blade.php` - Form tạo mới
- `edit.blade.php` - Form chỉnh sửa
- `show.blade.php` - Chi tiết khách hàng

## Menu

Package tự động đăng ký menu item "Khách hàng" trong admin sidebar với icon "user-group".

## Model

Package sử dụng model `App\Models\Customer` với các trường:

- `name` - Tên khách hàng
- `email` - Email (unique)
- `phone` - Số điện thoại
