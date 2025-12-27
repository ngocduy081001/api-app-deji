<?php

namespace App\Http\Controllers;

use App\Http\Resources\NewsResource;
use App\Http\Resources\ProductResource;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Vendor\News\Models\Article;
use Vendor\Product\Models\Product;
use Vendor\Settings\Models\Menu;
use Vendor\Product\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Notification;
use Vendor\Order\Models\OrderItem;
use Vendor\Settings\Models\Setting;
use Vendor\Settings\Models\Slider;

class APIController extends Controller
{

    protected $perPageDefault = 10;
    protected $pageDefault = 1;

    public function post($slug)
    {
        $post = Article::where('slug', $slug)->first();

        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        return response()->json([
            'data' => $post,
        ]);
    }
    public function posts(Request $request)
    {
        $posts = Article::orderBy('updated_at', 'desc')->paginate(5);
        foreach ($posts as $post) {
            $post->image = $post->featured_image;
        }
        return response()->json([
            'data' => $posts,
        ]);
    }

    public function product($slug)
    {
        $product = Product::where('slug', $slug)->with('categories:id,name,slug')->first();
        $product = ProductResource::make($product);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }



        return response()->json([
            'data' => $product,

        ]);
    }
    public function category(Request $request, $slug)
    {
        $category = ProductCategory::where('slug', $slug)->with('parent')->first();


        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        // Paginate products
        $perPage = $request->query('per_page', $this->perPageDefault);
        $page = $request->query('page', $this->pageDefault);

        $products = $category->products()->paginate($perPage, ['*'], 'page', $page);
        foreach ($products as $product) {
            $product->image = $product->featured_image;
        }
        return response()->json([
            'category' => $category,

            'products' => $products,
        ]);
    }

    public function products(Request $request)
    {
        $products = Product::with('categories:id', 'foreignImages')->get();
        return response()->json([
            'data' => $products,
        ]);
    }



    public function showrooms()
    {
        $showrooms = \Vendor\Order\Models\Showroom::orderBy('order', 'asc')->get();

        return response()->json([
            'data' => $showrooms,
        ]);
    }

    public function slider($id)
    {
        $slider = Slider::find($id)->load(['items' => function ($query) {
            $query->orderBy('order', 'asc');
        }]);

        if (!$slider) {
            return response()->json(['error' => 'Slider not found'], 404);
        }

        return response()->json([
            'data' => $slider,
        ]);
    }


    public function settings()
    {
        $settings = \Vendor\Settings\Models\Setting::all()->pluck('value', 'key');

        if ($settings->isEmpty()) {
            return response()->json(['error' => 'Settings not found'], 404);
        }

        $hiddenKeys = [
            'admin_email',
            'admin_title',
            'google_analytics',
            'analytics_view_id',
            'analytics_service_account_credentials',
            'admin_logo',
            'admin_login_screen_backgrounds',
            'admin_favicon',
            'newsletter_mailchimp_api_key',
            'newsletter_mailchimp_list_id',
            'newsletter_sendgrid_api_key',
            'newsletter_sendgrid_list_id',
            'working_dir',
            'email_driver',
            'email_hostName',
            'email_encryption',
            'email_port',
            'email_userName',
            'email_password',
            'email_senderName',
            'email_senderEmail',
            'facebook_chat_enabled',
            'facebook_page_id',
            'facebook_comment_enabled_in_post',
            'facebook_app_id',
        ];


        $filteredSettings = $settings->except($hiddenKeys);

        return response()->json([
            'data' => $filteredSettings,
        ]);
    }



    public function menu(Request $request)
    {
        $parentId = $request->query('parent_id');
        $main_id = $request->query('main_id');

        $parentId = is_numeric($parentId) ? (int) $parentId : null;
        $main_id = is_numeric($main_id) ? (int) $main_id : null;

        $menuItems = Menu::with('children')->where('parent_id', $parentId)->where('main_id', $main_id)->orderBy('order', 'asc')->get();

        if ($menuItems->isEmpty()) {
            return response()->json(['error' => 'No menu items found'], 404);
        }

        return response()->json([
            'data' => $menuItems,
        ]);
    }



    public function warrantyLookup(Request $request)
    {
        $phone = $request->query('phone');

        if (!$phone) {
            return response()->json(['error' => 'Phone number not provided'], 400);
        }

        $customer = \Vendor\Customer\Models\Customer::where('phone', $phone)->with('warranties')->first();

        $products = $customer ? $customer->warranties->pluck('product') : collect();


        if (!$customer) {
            return response()->json(['error' => 'Warranty not found'], 404);
        }

        return response()->json([
            'data' => $customer,
        ]);
    }

    public function warrantyLookupCode(Request $request)
    {
        $warrantyCode = $request->query('warranty_code');

        if (!$warrantyCode) {
            return response()->json(['error' => 'Warranty code not provided'], 400);
        }

        $warranties = \Vendor\Warranty\Models\Warranty::where('warranty_code', $warrantyCode)->with('customer', 'product')->get();

        $warranty     = \Vendor\Warranty\Models\Warranty::where('warranty_code', $warrantyCode)->with('customer', 'product')->first();
        if (!$warranty) {
            $customer = \Vendor\Customer\Models\Customer::where('phone', $warrantyCode)->first();
            if (!$customer) {
                return response()->json(['error' => 'Customer not found', 'status' => 404]);
            }
            $warranties = \Vendor\Warranty\Models\Warranty::where('customer_id', $customer->id)->with('customer', 'product')->get();
            foreach ($warranties as $warranty) {

                $warranty->time_expired = \Carbon\Carbon::parse($warranty->active_date)->addMonthsNoOverflow(12)->format('d/m/Y');
                $warranty->active_date = \Carbon\Carbon::parse($warranty->active_date)->format('d/m/Y');
            }
            return response()->json([
                'data' => $warranties,
                "has_active" => false,
                "customer" => $customer,
            ]);
        }

        foreach ($warranties as $warranty) {
            $warranty->time_expired = \Carbon\Carbon::parse($warranty->active_date)->addMonthsNoOverflow(12)->format('d/m/Y');
            $warranty->active_date = \Carbon\Carbon::parse($warranty->active_date)->format('d/m/Y');
        }

        return response()->json([
            'data' => $warranties,
            'customer' => $warranty->customer,
            "has_active" => false,
        ]);
    }

    public function WarrantyActive(Request $request)
    {
        try {
            // Validate incoming request
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable',
                'phone' => 'required|string|max:15',
                'active_date' => 'required',
                'warranty_code' => 'required',
            ]);

            // Check if the customer already exists by phone
            $customer = \Vendor\Customer\Models\Customer::where('phone', $validatedData['phone'])->first();
            if (!$customer) {
                // Create a new customer if not found
                $customer = \Vendor\Customer\Models\Customer::create([
                    'name' => $validatedData['name'],
                    'phone' => $validatedData['phone'],
                    'email' => $validatedData['email'] ?? '',
                ]);
            }

            // Retrieve warranty by code and check if it exists
            $warranty = \Vendor\Warranty\Models\Warranty::where('warranty_code', $validatedData['warranty_code'])
                ->where('status', '!=', 'active')
                ->first();

            if (!$warranty) {
                return response()->json([
                    'message' => 'Không tìm thấy mã bảo hành hợp lệ hoặc đã được kích hoạt.',
                ], 404);
            }

            // Update warranty
            $warranty->active_date = $validatedData['active_date'];
            $warranty->customer_id = $customer->id;
            $warranty->status = "active";



            $warranty->save();

            // Fetch with relationships for response
            $warranty = \Vendor\Warranty\Models\Warranty::where('id', $warranty->id)->with('customer', 'product')->first();

            $warranty->time_expired = \Carbon\Carbon::parse($warranty->active_date)->addMonthsNoOverflow(12)->format('d/m/Y');
            $warranty->active_date = \Carbon\Carbon::parse($warranty->active_date)->format('d/m/Y');

            return response()->json([
                'message' => 'Kích hoạt bảo hành thành công!',
                'warranties' => $warranty,
                "has_active" => true,
                "customer" => $customer,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi kích hoạt bảo hành.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function order(Request $request)
    {
        try {
            // Validate the incoming request
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable',
                'phone' => 'required|string|max:15',
                'address' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'city' => 'string|max:255',
                'district' => 'required|string|max:255',
                'price' => 'required|numeric',
                'product' => 'required|array', // Expect an array of products
                'product.*.item.id' => 'required|integer|exists:products,id',
                'product.*.quantity' => 'required|integer|min:1',
                'payment' => 'required',
            ]);

            \Illuminate\Support\Facades\Log::info('Validation successful', ['validatedData' => $validatedData]);

            // Check if the customer already exists by phone
            $customer = \Vendor\Customer\Models\Customer::where('phone', $validatedData['phone'])->first();
            if (!$customer) {
                // Create a new customer if not found
                $customer = \Vendor\Customer\Models\Customer::create([
                    'name' => $validatedData['name'],
                    'phone' => $validatedData['phone'],
                    'email' => $validatedData['email'],
                ]);
            }



            // Generate unique payment_code if not provided
            //$validatedData['payment_code'] = $validatedData['payment_code'] ?? $this->generateUniquePaymentCode();

            // Create the cart with the unique payment_code
            $cart = \Vendor\Order\Models\Order::create([
                'customer_id' => $customer->id,
                'order_number' => Str::random(10),
                'price' => $validatedData['price'],
                'address' => $validatedData['address'],
                'province' => $validatedData['province'],
                'district' => $validatedData['district'],
                'city' => $validatedData['city'] ?? null,
                'payment' => $validatedData['payment'],
                'customer_name' => $validatedData['name'],
                'customer_email' => $validatedData['email'],
                'customer_phone' => $validatedData['phone'],
                'customer_address' => $validatedData['address'],
                'total' => $validatedData['price'],
                'tax' => $validatedData['price'] * 0.1,
                'total' => $validatedData['price'] + $validatedData['price'] * 0.1,
                // 'payment_code' => $validatedData['payment'], // Add unique payment_code
            ]);

            // Attach products to the cart
            foreach ($validatedData['product'] as &$productData) {
                // Fetch the product by its ID
                $product = Product::find($productData['item']['id']);
                if (!$product) {
                    throw new \Exception("Product with ID {$productData['item']['id']} not found.");
                }

                // Attach the product to the cart with quantity

                OrderItem::create([
                    'order_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $productData['quantity'],
                    'price' => $product->price,
                    'total' => $product->price * $productData['quantity'],
                ]);

                // Add the product name for email details
                $productData['item']['name'] = $product->name;
            }


            // Fetch email configurations
            $arr = [
                "email_driver",
                "email_hostName",
                "email_encryption",
                "email_port",
                "email_userName",
                "email_password",
                "email_senderName",
                "email_senderEmail",
                "admin_email",
                "Logo",
            ];

            $configurations = Setting::whereIn('key', $arr)->pluck('value', 'key');

            // Set email configuration dynamically
            // config([
            //     'mail.mailers.smtp.host' => $configurations["email_hostName"] ?? 'default_host',
            //     'mail.mailers.smtp.port' => $configurations["email_port"] ?? 587,
            //     'mail.mailers.smtp.encryption' => $configurations["email_encryption"] ?? 'tls',
            //     'mail.mailers.smtp.username' => $configurations["email_userName"] ?? 'default_username',
            //     'mail.mailers.smtp.password' => $configurations["email_password"] ?? 'default_password',
            //     'mail.from.address' => $configurations["email_senderEmail"] ?? 'default_sender@example.com',
            //     'mail.from.name' => $configurations["email_senderName"] ?? 'Default Sender',
            // ]);

            // Prepare email details
            $details = [
                'return' => 'Order',
                'title' => 'Deji Việt Nam',
                'body' => 'Thông tin đơn hàng',
                'logo' => $configurations["Logo"],
                'customer' => $customer,
                'order' => $cart,
                'product' => $validatedData['product'],
                'price' => $validatedData['price'],
            ];

            // Prepare CC emails
            $ccEmails = [];
            if (!empty($customer->email) && filter_var($customer->email, FILTER_VALIDATE_EMAIL)) {
                $ccEmails[] = $customer->email;
            }

            // Send order email to admin_email from settings
            if (!empty($configurations["admin_email"]) && filter_var($configurations["admin_email"], FILTER_VALIDATE_EMAIL)) {
                \Illuminate\Support\Facades\Mail::to($configurations["admin_email"])
                    ->cc($ccEmails)
                    ->send(new \App\Mail\ContactFormMail($details));
            }

            // Send order email to MAIL_ADMIN from env
            $mailAdmin = env('MAIL_ADMIN');
            if (!empty($mailAdmin) && filter_var($mailAdmin, FILTER_VALIDATE_EMAIL)) {
                \Illuminate\Support\Facades\Mail::to($mailAdmin)
                    ->cc($ccEmails)
                    ->send(new \App\Mail\ContactFormMail($details));
            }

            // Create notification for new order
            Notification::create([
                'type' => 'order',
                'title' => 'Đơn hàng mới',
                'message' => "Có đơn hàng mới từ khách hàng {$customer->name} - Mã đơn: {$cart->order_number}",
                'link' => route('admin.orders.show', $cart),
                'is_read' => false,
            ]);

            // Lưu thông tin địa chỉ giao hàng để sử dụng lần sau
            try {
                $ward = $request->input('ward', '');
                \Vendor\Order\Models\CustomerAddress::updateOrCreate(
                    [
                        'phone' => $validatedData['phone'],
                        'province' => $validatedData['province'],
                        'district' => $validatedData['district'],
                        'ward' => $ward,
                        'address' => $validatedData['address'],
                    ],
                    [
                        'customer_id' => $customer->id,
                        'name' => $validatedData['name'],
                        'email' => $validatedData['email'] ?? null,
                        'note' => $request->input('note', null),
                        'last_used_at' => now(),
                    ]
                );
            } catch (\Exception $e) {
                // Log error nhưng không block order flow
                \Illuminate\Support\Facades\Log::error('Error saving customer address: ' . $e->getMessage());
            }

            $cart->load('customer');

            return response()->json(['message' => 'Bạn đã đặt hàng thành công ! Kiểm tra email của bạn để xem thông tin chi tiết.', 'Cart' => $cart], 201);
        } catch (\Exception $e) {

            return response()->json(['error' =>  $e->getMessage()], 500);
        }
    }

    public function generateUniquePaymentCode()
    {
        do {
            // Generate a unique payment code
            $paymentCode = strtoupper(uniqid('PAY_'));

            // Check if the generated payment code already exists in the database
            $existingCart = \Vendor\Order\Models\Order::where('payment_code', $paymentCode)->first();
        } while ($existingCart); // Repeat until no duplicate is found

        return $paymentCode;
    }

    public function getWarrantyLookupPhone(Request $request)
    {
        $phone = $request->query('phone');
        $customer = \Vendor\Customer\Models\Customer::where('phone', $phone)->first();
        $warranty = \Vendor\Warranty\Models\Warranty::where('customer_id', $customer->id)->get();
        return response()->json([
            'data' => $warranty,
        ]);
    }



    // Booking logic has been moved to \Vendor\Order\Http\Controllers\BookingController


    public function search(Request $request)
    {
        // Retrieve the search query from the request
        $query = $request->query('search');
        $query = str_replace('-', ' ', $query);
        $products = Product::where('is_active', true)
            ->where('name', 'LIKE', '%' . $query . '%')

            ->paginate(100);

        $products = ProductResource::collection($products);
        return response()->json([
            'data' => [
                'data' => $products
            ],
        ]);
    }


    public function getProductFeature()
    {
        $products = Product::where('is_featured', true)->with('categories:id,name,slug')->get();

        if (!$products) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // $metaBoxes = MetaBoxes::where('reference_id', $product->id)
        //     ->where('reference', 'product')
        //     ->get();

        return response()->json([
            'data' => $products->random(5),
            //   'meta_boxes' => $metaBoxes,
        ]);
    }

    public function getPostFeature()
    {
        $featuredPosts = Article::where('is_featured', 1)->orderBy('updated_at', 'desc')->get();
        $posts = $featuredPosts->random(min(3, $featuredPosts->count()));
        $posts = NewsResource::collection($posts);
        return response()->json([
            'data' => $posts,
        ]);
    }
    public function getPostFeatureFixed()
    {
        $featuredPosts = Article::where('is_fixed', 1)->orderBy('updated_at', 'desc')->first();

        return response()->json(['data' => $featuredPosts]);
    }

    public function getSubCategoriesChildrents($id)
    {
        $category = ProductCategory::where('id', $id)->first();
        if ($category->parent_id) {
            $subCategories = ProductCategory::where('parent_id', $category->parent_id)->get();
        } else {
            $subCategories = ProductCategory::where('parent_id', $category->id)->get();
        }
        return response()->json(['data' => $subCategories]);
    }
    public function getSubCategoriesChildrents2($id)
    {
        $category = ProductCategory::where('id', $id)->first();

        return response()->json(['data' => $category->children]);
    }

    public function getMainMenu()
    {
        $menu = Menu::where('menu_group_id', 5)->with('category')->whereNull('parent_id')->with('children.category.products')->get();

        foreach ($menu as $item) {
            if ($item->children->count() > 0) {
                foreach ($item->children as $child) {
                    $category = ProductCategory::where('slug', $item->slug)->first();

                    $categoryChildren = ProductCategory::where('parent_id', $category->id)
                        //  ->where('slug', $child->slug)
                        ->with('products')
                        ->first();
                    /// $categoryChildren->categoy = $categoryChildren;
                    $child->category = $categoryChildren;
                    $child->products = $child?->products;
                    $child->link = $child->slug;
                }
            }
        }

        return response()->json($menu);
    }

    public function categoriesIsFeatured()
    {
        // Lấy featured categories với thông tin menu để sắp xếp theo thứ tự menu
        $categories = ProductCategory::where('is_featured', true)->with('products')->get();

        foreach ($categories as $category) {
            $category->thumbnail = $category->image;
            if ($category->products->count() > 0) {
                foreach ($category->products as $product) {
                    $product->image = $product->featured_image;
                }
            }
        }

        if ($categories->isEmpty()) {
            return response()->json(['error' => 'No featured categories found'], 404);
        }

        return response()->json([
            'data' => $categories,
        ]);
    }

    public function categories(Request $request)
    {
        $parentId = $request->query('parent_id');

        $parentId = is_numeric($parentId) ? (int) $parentId : null;
        // dd($parentId);
        // Lấy categories với thông tin menu để sắp xếp theo thứ tự menu
        $categories = ProductCategory::where('parent_id', $parentId)
            ->with('children')
            ->leftJoin('menu', function ($join) {
                $join->on('categories.slug', '=', 'menu.link');
            })
            ->orderBy('menu.order', 'asc')
            ->orderBy('categories.order', 'asc')
            ->select('categories.*')
            ->get();

        if ($categories->isEmpty()) {
            return response()->json(['error' => 'No categories found'], 404);
        }

        return response()->json([
            'data' => $categories,
        ]);
    }

    public function categoriesAll()
    {
        $categories = ProductCategory::all();
        return response()->json([
            'data' => $categories,
        ]);
    }

    public function getMainMenu2()
    {
        $menu = Menu::where('menu_group_id', 5)->with('category')->whereNull('parent_id')->with('children')->get();

        foreach ($menu as $item) {
            if ($item->children->count() > 0) {
                foreach ($item->children as $child) {
                    $category = ProductCategory::where('slug', $item->slug)->first();
                    $categoryChildren = ProductCategory::where('parent_id', $category->id)
                        ->where('slug', $child->slug)
                        ->with('products')
                        ->first();
                    $child->category = $categoryChildren;
                }
            }
        }


        return response()->json($menu);
    }

    public function getBannerTop()
    {
        $banner = Slider::where('key', 'home-banner-top')->with('items')->get();
        return response()->json($banner);
    }

    public function getBannerLeft()
    {
        $banner = Slider::where('key', 'home-banner-left')->with('items')->get();
        return response()->json($banner);
    }

    public function getLogo()
    {
        $logo = Setting::where('key', 'admin_favicon')->first();
        return response()->json($logo);
    }

    public function partners()
    {
        $partner = Slider::where('key', 'partner')->with('items')->first();

        return response()->json($partner);
    }

    public function seo()
    {


        return response()->json([
            'page_name' => Setting::where('key', 'page_name')->first()->value,
            'seo_title' => Setting::where('key', 'seo_title')->first()->value,
            'seo_description' => Setting::where('key', 'seo_description')->first()->value,
            'seo_image' => Setting::where('key', 'seo_image')->first()->value,
        ]);
    }

    public function getFooter()
    {
        return response()->json([
            'hotline' => Setting::where('key', 'hotline')->first()->value,
            'name_company' => Setting::where('key', 'name_company')->first()->value,
            'address_company' => Setting::where('key', 'address_company')->first()->value,
            'email_company' => Setting::where('key', 'email_company')->first()->value,
            'bao_hanh' => Setting::where('key', 'bao_hanh')->first()->value,
            'doi_tra' => Setting::where('key', 'doi_tra')->first()->value,
            'thanh_toan' => Setting::where('key', 'thanh_toan')->first()->value,
            'Customer_Service' => Setting::where('key', 'Customer_Service')->first()->value,
            'Complaint_Service' => Setting::where('key', 'Complaint_Service')->first()->value,
            'Support_Service' => Setting::where('key', 'Support_Service')->first()->value,
            'chinh_sach' => Setting::where('key', 'chinh_sach')->first()->value,
            'tuyen_dung' => Setting::where('key', 'tuyen_dung')->first()->value,
        ]);
    }

    /**
     * Get notifications for admin
     */
    public function getNotifications(Request $request)
    {
        $limit = $request->query('limit', 10);
        $unreadOnly = $request->query('unread_only', false);

        $query = Notification::query()->orderBy('created_at', 'desc');

        if ($unreadOnly) {
            $query->unread();
        }

        $notifications = $query->limit($limit)->get();
        $unreadCount = Notification::unread()->count();

        return response()->json([
            'data' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read',
            'data' => $notification,
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        Notification::unread()->update(['is_read' => true]);

        return response()->json([
            'message' => 'All notifications marked as read',
        ]);
    }
}
