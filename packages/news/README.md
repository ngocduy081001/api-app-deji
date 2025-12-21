# News Module

Module quản lý tin tức với categories và articles cho Laravel.

## Tính năng

### News Categories (Danh mục tin tức)

-   ✅ Quản lý danh mục tin tức (CRUD)
-   ✅ Hỗ trợ danh mục đa cấp (parent-child)
-   ✅ Slug tự động từ tên danh mục
-   ✅ Sắp xếp danh mục theo thứ tự
-   ✅ Soft delete (xóa mềm)
-   ✅ Active/Inactive status

### Articles (Bài viết)

-   ✅ Quản lý bài viết (CRUD)
-   ✅ Trạng thái: Draft, Published, Archived
-   ✅ Hỗ trợ featured articles
-   ✅ Đếm lượt xem
-   ✅ Tags cho bài viết
-   ✅ Tự động tính thời gian đọc
-   ✅ Tìm kiếm bài viết
-   ✅ Lọc theo category, author, tags
-   ✅ Soft delete

## Cài đặt

### 1. Đăng ký Service Provider

File này đã được tự động đăng ký trong `composer.json`:

```json
"extra": {
    "laravel": {
        "providers": [
            "Vendor\\News\\NewsServiceProvider"
        ]
    }
}
```

### 2. Chạy migrations

```bash
php artisan migrate
```

### 3. Seed dữ liệu mẫu (tùy chọn)

```bash
php artisan db:seed --class="Vendor\\News\\Database\\Seeders\\NewsSeeder"
```

## API Endpoints

### News Categories

#### Lấy danh sách categories

```
GET /api/news/categories
```

Query parameters:

-   `is_active` (boolean): Lọc theo trạng thái active
-   `parent_id` (int|null): Lọc theo category cha
-   `with_children` (boolean): Bao gồm danh mục con
-   `with_parent` (boolean): Bao gồm danh mục cha
-   `with_descendants` (boolean): Bao gồm tất cả danh mục con cháu
-   `with_articles_count` (boolean): Bao gồm số lượng bài viết
-   `search` (string): Tìm kiếm theo tên
-   `sort_by` (string): Sắp xếp theo (mặc định: sort_order)
-   `sort_order` (string): asc|desc
-   `per_page` (int|all): Số item mỗi trang (mặc định: 15)

#### Lấy cây danh mục

```
GET /api/news/categories/tree
```

#### Lấy chi tiết category theo ID

```
GET /api/news/categories/{id}
```

#### Lấy chi tiết category theo slug

```
GET /api/news/categories/slug/{slug}
```

#### Tạo category mới

```
POST /api/news/categories
```

Body:

```json
{
    "name": "Công nghệ",
    "slug": "cong-nghe",
    "description": "Tin tức về công nghệ",
    "parent_id": null,
    "image": "/images/tech.jpg",
    "is_active": true,
    "sort_order": 1
}
```

#### Cập nhật category

```
PUT /api/news/categories/{id}
PATCH /api/news/categories/{id}
```

#### Xóa category

```
DELETE /api/news/categories/{id}
```

#### Khôi phục category đã xóa

```
POST /api/news/categories/{id}/restore
```

### Articles

#### Lấy danh sách articles

```
GET /api/news/articles
```

Query parameters:

-   `status` (string): draft|published|archived
-   `all_status` (boolean): Hiển thị tất cả trạng thái
-   `category_id` (int): Lọc theo category
-   `author_id` (int): Lọc theo tác giả
-   `is_featured` (boolean): Lọc bài viết nổi bật
-   `tag` (string): Lọc theo tag
-   `with_category` (boolean): Bao gồm thông tin category
-   `with_author` (boolean): Bao gồm thông tin tác giả
-   `search` (string): Tìm kiếm trong title, excerpt, content
-   `sort_by` (string): published_at|most_viewed|recent
-   `sort_order` (string): asc|desc
-   `per_page` (int|all): Số item mỗi trang

#### Lấy bài viết nổi bật

```
GET /api/news/articles/featured?limit=5
```

#### Lấy bài viết xem nhiều nhất

```
GET /api/news/articles/most-viewed?limit=5
```

#### Lấy bài viết mới nhất

```
GET /api/news/articles/recent?limit=5
```

#### Lấy chi tiết article theo ID

```
GET /api/news/articles/{id}?increment_view=true
```

Query parameters:

-   `increment_view` (boolean): Tăng view count

#### Lấy chi tiết article theo slug

```
GET /api/news/articles/slug/{slug}
```

#### Lấy bài viết liên quan

```
GET /api/news/articles/{id}/related?limit=5
```

#### Tạo article mới

```
POST /api/news/articles
```

Body:

```json
{
    "title": "iPhone 16 Pro Max Review",
    "slug": "iphone-16-pro-max-review",
    "excerpt": "Đánh giá chi tiết iPhone 16 Pro Max",
    "content": "<p>Nội dung bài viết...</p>",
    "category_id": 1,
    "author_id": 1,
    "featured_image": "/images/iphone.jpg",
    "images": ["/images/1.jpg", "/images/2.jpg"],
    "status": "published",
    "is_featured": true,
    "published_at": "2024-10-28 10:00:00",
    "tags": ["iPhone", "Apple", "Review"],
    "meta_data": {
        "meta_title": "iPhone 16 Pro Max Review",
        "meta_description": "...",
        "meta_keywords": "iphone, apple"
    }
}
```

#### Cập nhật article

```
PUT /api/news/articles/{id}
PATCH /api/news/articles/{id}
```

#### Thay đổi trạng thái article

```
POST /api/news/articles/{id}/status
```

Body:

```json
{
    "status": "published"
}
```

#### Xóa article

```
DELETE /api/news/articles/{id}
```

#### Khôi phục article đã xóa

```
POST /api/news/articles/{id}/restore
```

## Sử dụng trong code

### Models

#### NewsCategory Model

```php
use Vendor\News\Models\NewsCategory;

// Lấy tất cả categories active
$categories = NewsCategory::active()->get();

// Lấy root categories
$rootCategories = NewsCategory::root()->get();

// Lấy category với children
$category = NewsCategory::with('children')->find(1);

// Lấy full path của category
$category = NewsCategory::find(1);
echo $category->full_path; // "Công nghệ > Điện thoại"

// Đếm tổng số bài viết (bao gồm cả children)
echo $category->total_articles;
```

#### Article Model

```php
use Vendor\News\Models\Article;

// Lấy tất cả bài viết đã publish
$articles = Article::published()->get();

// Lấy bài viết nổi bật
$featured = Article::published()->featured()->get();

// Lấy bài viết xem nhiều nhất
$mostViewed = Article::published()->mostViewed()->limit(10)->get();

// Tìm kiếm bài viết
$articles = Article::search('iphone')->published()->get();

// Lọc theo category
$articles = Article::byCategory(1)->published()->get();

// Lọc theo tag
$articles = Article::byTag('Technology')->published()->get();

// Tăng view count
$article = Article::find(1);
$article->incrementViewCount();

// Lấy thời gian đọc
echo $article->reading_time; // 5 (phút)

// Kiểm tra trạng thái
if ($article->is_published) {
    echo "Bài viết đã được xuất bản";
}
```

## Validation Rules

### News Category

```php
[
    'name' => 'required|string|max:255',
    'slug' => 'nullable|string|max:255|unique:news_categories,slug',
    'description' => 'nullable|string',
    'parent_id' => 'nullable|integer|exists:news_categories,id',
    'image' => 'nullable|string',
    'is_active' => 'nullable|boolean',
    'sort_order' => 'nullable|integer|min:0',
]
```

### Article

```php
[
    'title' => 'required|string|max:255',
    'slug' => 'nullable|string|max:255|unique:articles,slug',
    'excerpt' => 'nullable|string|max:1000',
    'content' => 'nullable|string',
    'category_id' => 'nullable|integer|exists:news_categories,id',
    'author_id' => 'nullable|integer|exists:users,id',
    'featured_image' => 'nullable|string',
    'images' => 'nullable|array',
    'status' => 'nullable|string|in:draft,published,archived',
    'is_featured' => 'nullable|boolean',
    'published_at' => 'nullable|date',
    'tags' => 'nullable|array',
    'meta_data' => 'nullable|array',
]
```

## Database Schema

### news_categories

| Column      | Type      | Description                 |
| ----------- | --------- | --------------------------- |
| id          | bigint    | Primary key                 |
| name        | string    | Tên danh mục                |
| slug        | string    | URL slug (unique)           |
| description | text      | Mô tả                       |
| parent_id   | bigint    | ID danh mục cha (nullable)  |
| image       | string    | Đường dẫn ảnh               |
| is_active   | boolean   | Trạng thái active           |
| sort_order  | integer   | Thứ tự sắp xếp              |
| created_at  | timestamp | Thời gian tạo               |
| updated_at  | timestamp | Thời gian cập nhật          |
| deleted_at  | timestamp | Thời gian xóa (soft delete) |

### articles

| Column         | Type      | Description              |
| -------------- | --------- | ------------------------ |
| id             | bigint    | Primary key              |
| title          | string    | Tiêu đề bài viết         |
| slug           | string    | URL slug (unique)        |
| excerpt        | text      | Tóm tắt                  |
| content        | longtext  | Nội dung                 |
| category_id    | bigint    | ID danh mục              |
| author_id      | bigint    | ID tác giả               |
| featured_image | string    | Ảnh đại diện             |
| images         | json      | Danh sách ảnh            |
| status         | string    | draft/published/archived |
| is_featured    | boolean   | Bài viết nổi bật         |
| view_count     | integer   | Lượt xem                 |
| sort_order     | integer   | Thứ tự sắp xếp           |
| published_at   | timestamp | Thời gian xuất bản       |
| meta_data      | json      | Metadata (SEO)           |
| tags           | json      | Tags                     |
| created_at     | timestamp | Thời gian tạo            |
| updated_at     | timestamp | Thời gian cập nhật       |
| deleted_at     | timestamp | Thời gian xóa            |

## License

MIT License
