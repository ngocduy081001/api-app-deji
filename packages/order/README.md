# Order

A Laravel package

## Cài đặt

```bash
composer require vendor/order
```

## Sử dụng

### Publish config

```bash
php artisan vendor:publish --tag=order-config
```

### Publish views

```bash
php artisan vendor:publish --tag=order-views
```

### Publish migrations

```bash
php artisan vendor:publish --tag=order-migrations
php artisan migrate
```

### Publish assets

```bash
php artisan vendor:publish --tag=order-assets
```

## Cấu hình

Sau khi publish config, bạn có thể cấu hình package trong file `config/order.php`

## Testing

```bash
composer test
```

## License

MIT License
