# Product

A Laravel package

## Cài đặt

```bash
composer require vendor/product
```

## Sử dụng

### Publish config

```bash
php artisan vendor:publish --tag=product-config
```

### Publish views

```bash
php artisan vendor:publish --tag=product-views
```

### Publish migrations

```bash
php artisan vendor:publish --tag=product-migrations
php artisan migrate
```

### Publish assets

```bash
php artisan vendor:publish --tag=product-assets
```

## Cấu hình

Sau khi publish config, bạn có thể cấu hình package trong file `config/product.php`

## Testing

```bash
composer test
```

## License

MIT License
