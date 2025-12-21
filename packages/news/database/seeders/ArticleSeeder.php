<?php

namespace Vendor\News\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Vendor\News\Models\Article;
use Vendor\News\Models\NewsCategory;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy categories
        $techCategory = NewsCategory::where('slug', 'cong-nghe')->first();
        $phoneCategory = NewsCategory::where('slug', 'dien-thoai')->first();
        $laptopCategory = NewsCategory::where('slug', 'laptop')->first();
        $aiCategory = NewsCategory::where('slug', 'ai-machine-learning')->first();
        $businessCategory = NewsCategory::where('slug', 'kinh-doanh')->first();
        $entertainmentCategory = NewsCategory::where('slug', 'giai-tri')->first();
        $sportsCategory = NewsCategory::where('slug', 'the-thao')->first();
        $travelCategory = NewsCategory::where('slug', 'du-lich')->first();
        $healthCategory = NewsCategory::where('slug', 'suc-khoe')->first();

        // Lấy user đầu tiên làm author
        $author = User::first();

        if (!$author) {
            $this->command->warn('No users found. Please seed users first.');
            return;
        }

        $articles = [
            // Công nghệ
            [
                'title' => 'iPhone 16 Pro Max: Đánh giá chi tiết sau 1 tháng sử dụng',
                'slug' => 'iphone-16-pro-max-danh-gia-chi-tiet',
                'excerpt' => 'Sau một tháng trải nghiệm thực tế, chúng tôi đưa ra đánh giá toàn diện về chiếc iPhone 16 Pro Max mới nhất của Apple.',
                'content' => '<p>iPhone 16 Pro Max là flagship mới nhất của Apple với nhiều cải tiến đáng chú ý. Máy được trang bị chip A18 Pro mạnh mẽ, camera 48MP cải tiến và pin lớn hơn.</p><p>Thiết kế của iPhone 16 Pro Max không có nhiều thay đổi so với thế hệ trước, nhưng Apple đã tối ưu hóa các chi tiết nhỏ để mang lại trải nghiệm tốt hơn.</p><p>Camera là điểm sáng nhất với khả năng chụp ảnh trong điều kiện ánh sáng yếu được cải thiện đáng kể. Video 4K 120fps mang lại chất lượng tuyệt vời cho người dùng.</p>',
                'category_id' => $phoneCategory?->id,
                'author_id' => $author->id,
                'status' => Article::STATUS_PUBLISHED,
                'is_featured' => true,
                'published_at' => now()->subDays(1),
                'tags' => ['iPhone', 'Apple', 'Smartphone', 'Review'],
                'view_count' => rand(500, 5000),
                'sort_order' => 1,
            ],
            [
                'title' => 'Top 5 laptop gaming giá rẻ đáng mua nhất năm 2024',
                'slug' => 'top-5-laptop-gaming-gia-re-2024',
                'excerpt' => 'Tổng hợp những chiếc laptop gaming có giá thành phải chăng nhưng vẫn đảm bảo hiệu năng chơi game mượt mà.',
                'content' => '<p>Laptop gaming không nhất thiết phải đắt tiền. Năm 2024, nhiều hãng đã cho ra mắt các sản phẩm với cấu hình mạnh mẽ nhưng giá thành hợp lý.</p><p>Trong bài viết này, chúng tôi sẽ giới thiệu 5 chiếc laptop gaming tốt nhất trong tầm giá dưới 20 triệu đồng, phù hợp cho cả sinh viên và game thủ có ngân sách hạn chế.</p>',
                'category_id' => $laptopCategory?->id,
                'author_id' => $author->id,
                'status' => Article::STATUS_PUBLISHED,
                'is_featured' => true,
                'published_at' => now()->subDays(2),
                'tags' => ['Laptop', 'Gaming', 'Review', 'Giá rẻ'],
                'view_count' => rand(500, 5000),
                'sort_order' => 2,
            ],
            [
                'title' => 'ChatGPT-5 sẽ thay đổi cách chúng ta làm việc như thế nào?',
                'slug' => 'chatgpt-5-thay-doi-cach-lam-viec',
                'excerpt' => 'OpenAI đang phát triển ChatGPT-5 với nhiều tính năng mới hứa hẹn sẽ cách mạng hóa cách chúng ta làm việc.',
                'content' => '<p>ChatGPT-5 được kỳ vọng sẽ có khả năng hiểu ngữ cảnh tốt hơn và đưa ra các phản hồi chính xác hơn. AI này có thể trở thành trợ lý đắc lực cho mọi lĩnh vực công việc.</p><p>Các tính năng mới bao gồm khả năng phân tích dữ liệu phức tạp, tạo nội dung sáng tạo và hỗ trợ lập trình tốt hơn.</p>',
                'category_id' => $aiCategory?->id,
                'author_id' => $author->id,
                'status' => Article::STATUS_PUBLISHED,
                'is_featured' => false,
                'published_at' => now()->subDays(3),
                'tags' => ['AI', 'ChatGPT', 'OpenAI', 'Machine Learning'],
                'view_count' => rand(500, 5000),
                'sort_order' => 3,
            ],
            [
                'title' => 'Machine Learning: Xu hướng và ứng dụng trong năm 2024',
                'slug' => 'machine-learning-xu-huong-2024',
                'excerpt' => 'Khám phá các xu hướng mới nhất của Machine Learning và cách chúng đang được ứng dụng trong thực tế.',
                'content' => '<p>Machine Learning đang phát triển với tốc độ chóng mặt. Năm 2024 chứng kiến nhiều đột phá quan trọng trong lĩnh vực này.</p><p>Các ứng dụng của ML đang được áp dụng rộng rãi từ y tế, tài chính đến giáo dục và giải trí.</p>',
                'category_id' => $aiCategory?->id,
                'author_id' => $author->id,
                'status' => Article::STATUS_PUBLISHED,
                'is_featured' => false,
                'published_at' => now()->subDays(4),
                'tags' => ['Machine Learning', 'AI', 'Technology', 'Trend'],
                'view_count' => rand(500, 5000),
                'sort_order' => 4,
            ],

            // Kinh doanh
            [
                'title' => 'Startup Việt Nam huy động 2 tỷ USD trong năm 2024',
                'slug' => 'startup-viet-nam-huy-dong-2-ty-usd',
                'excerpt' => 'Thị trường startup Việt Nam đang phục hồi mạnh mẽ với tổng vốn đầu tư đạt 2 tỷ USD trong năm nay.',
                'content' => '<p>Sau giai đoạn khó khăn, hệ sinh thái startup Việt Nam đang cho thấy dấu hiệu phục hồi tích cực. Nhiều startup đã huy động thành công vốn từ các nhà đầu tư trong và ngoài nước.</p><p>Các lĩnh vực fintech, edtech và healthtech đang thu hút nhiều sự quan tâm nhất từ các nhà đầu tư.</p>',
                'category_id' => $businessCategory?->id,
                'author_id' => $author->id,
                'status' => Article::STATUS_PUBLISHED,
                'is_featured' => true,
                'published_at' => now()->subDays(5),
                'tags' => ['Startup', 'Đầu tư', 'Kinh doanh', 'Việt Nam'],
                'view_count' => rand(500, 5000),
                'sort_order' => 5,
            ],

            // Giải trí
            [
                'title' => 'Top 10 phim chiếu rạp đáng xem nhất tháng này',
                'slug' => 'top-10-phim-chieu-rap-dang-xem',
                'excerpt' => 'Tổng hợp những bộ phim hay nhất đang chiếu tại các rạp chiếu phim trên toàn quốc.',
                'content' => '<p>Tháng này có rất nhiều bộ phim hay đang được chiếu tại các rạp. Từ phim hành động, kinh dị đến phim tình cảm, tất cả đều có những tác phẩm đáng xem.</p><p>Chúng tôi đã chọn lọc ra 10 bộ phim đáng xem nhất để giúp bạn có lựa chọn tốt nhất cho buổi tối cuối tuần.</p>',
                'category_id' => $entertainmentCategory?->id,
                'author_id' => $author->id,
                'status' => Article::STATUS_PUBLISHED,
                'is_featured' => false,
                'published_at' => now()->subDays(6),
                'tags' => ['Phim', 'Giải trí', 'Review', 'Cinema'],
                'view_count' => rand(500, 5000),
                'sort_order' => 6,
            ],

            // Thể thao
            [
                'title' => 'MU vs Liverpool: Trận derby nước Anh đầy kịch tính',
                'slug' => 'mu-vs-liverpool-tran-derby-day-kich-tinh',
                'excerpt' => 'Trận đấu giữa Manchester United và Liverpool đã mang đến những phút giây hồi hộp cho người hâm mộ.',
                'content' => '<p>Trận derby nước Anh giữa MU và Liverpool luôn là tâm điểm chú ý của người hâm mộ bóng đá. Trận đấu mới nhất không phụ lòng người xem với những tình huống kịch tính.</p><p>Cả hai đội đều cho thấy phong độ tốt với những pha phối hợp đẹp mắt và tinh thần chiến đấu cao.</p>',
                'category_id' => $sportsCategory?->id,
                'author_id' => $author->id,
                'status' => Article::STATUS_PUBLISHED,
                'is_featured' => false,
                'published_at' => now()->subDays(7),
                'tags' => ['Bóng đá', 'MU', 'Liverpool', 'Premier League'],
                'view_count' => rand(500, 5000),
                'sort_order' => 7,
            ],

            // Du lịch
            [
                'title' => '5 địa điểm du lịch đẹp nhất Việt Nam năm 2024',
                'slug' => '5-dia-diem-du-lich-dep-nhat-viet-nam',
                'excerpt' => 'Khám phá những địa điểm du lịch tuyệt đẹp tại Việt Nam mà bạn không thể bỏ lỡ trong năm nay.',
                'content' => '<p>Việt Nam có nhiều địa điểm du lịch đẹp và độc đáo. Từ vịnh Hạ Long đến đồng bằng sông Cửu Long, mỗi nơi đều có vẻ đẹp riêng.</p><p>Trong bài viết này, chúng tôi sẽ giới thiệu 5 địa điểm đẹp nhất để bạn có thể lên kế hoạch cho chuyến du lịch của mình.</p>',
                'category_id' => $travelCategory?->id,
                'author_id' => $author->id,
                'status' => Article::STATUS_PUBLISHED,
                'is_featured' => true,
                'published_at' => now()->subDays(8),
                'tags' => ['Du lịch', 'Việt Nam', 'Travel', 'Destination'],
                'view_count' => rand(500, 5000),
                'sort_order' => 8,
            ],

            // Sức khỏe
            [
                'title' => '10 thói quen tốt giúp bạn sống khỏe mạnh hơn',
                'slug' => '10-thoi-quen-tot-cho-suc-khoe',
                'excerpt' => 'Những thói quen đơn giản nhưng hiệu quả giúp cải thiện sức khỏe và chất lượng cuộc sống.',
                'content' => '<p>Sức khỏe là tài sản quý giá nhất của mỗi người. Để có sức khỏe tốt, bạn cần xây dựng những thói quen lành mạnh trong cuộc sống hàng ngày.</p><p>Bài viết này sẽ giới thiệu 10 thói quen đơn giản nhưng rất hiệu quả giúp bạn cải thiện sức khỏe một cách toàn diện.</p>',
                'category_id' => $healthCategory?->id,
                'author_id' => $author->id,
                'status' => Article::STATUS_PUBLISHED,
                'is_featured' => false,
                'published_at' => now()->subDays(9),
                'tags' => ['Sức khỏe', 'Lối sống', 'Health', 'Lifestyle'],
                'view_count' => rand(500, 5000),
                'sort_order' => 9,
            ],

            // Draft article
            [
                'title' => 'Bài viết đang soạn thảo về công nghệ mới',
                'slug' => 'bai-viet-dang-soan-thao',
                'excerpt' => 'Đây là bài viết đang trong quá trình soạn thảo.',
                'content' => '<p>Nội dung đang được cập nhật...</p>',
                'category_id' => $techCategory?->id,
                'author_id' => $author->id,
                'status' => Article::STATUS_DRAFT,
                'is_featured' => false,
                'published_at' => null,
                'tags' => ['Draft', 'Technology'],
                'view_count' => 0,
                'sort_order' => 10,
            ],
        ];

        foreach ($articles as $articleData) {
            Article::create($articleData);
        }

        $this->command->info('Articles seeded successfully!');
    }
}

