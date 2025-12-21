# Warranty

A Laravel package

## Cài đặt

```bash
composer require vendor/warranty
```

## Sử dụng

### Publish config

```bash
php artisan vendor:publish --tag=warranty-config
```

### Publish views

```bash
php artisan vendor:publish --tag=warranty-views
```

### Publish migrations

```bash
php artisan vendor:publish --tag=warranty-migrations
php artisan migrate
```

### Publish assets

```bash
php artisan vendor:publish --tag=warranty-assets
```

## Cấu hình

Sau khi publish config, bạn có thể cấu hình package trong file `config/warranty.php`

## Testing

```bash
composer test
```

## License

MIT License
