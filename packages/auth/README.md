# Auth

A Laravel package

## Cài đặt

```bash
composer require vendor/auth
```

## Sử dụng

### Publish config

```bash
php artisan vendor:publish --tag=auth-config
```

### Publish views

```bash
php artisan vendor:publish --tag=auth-views
```

### Publish migrations

```bash
php artisan vendor:publish --tag=auth-migrations
php artisan migrate
```

### Publish assets

```bash
php artisan vendor:publish --tag=auth-assets
```

## Cấu hình

Sau khi publish config, bạn có thể cấu hình package trong file `config/auth.php`

## Testing

```bash
composer test
```

## License

MIT License
