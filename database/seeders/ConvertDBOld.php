<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Vendor\Order\Models\Order;

class ConvertDBOld extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Increase memory limit
            // ini_set('memory_limit', '512M');
            // set_time_limit(0);

            // Check if mysql2 connection exists
            try {
                DB::connection('mysql2')->getPdo();
                $this->command->info('✓ Connected to mysql2 database');
            } catch (\Exception $e) {
                $this->command->error('✗ Cannot connect to mysql2 database: ' . $e->getMessage());
                Log::error('Cannot connect to mysql2 database: ' . $e->getMessage());
                throw new \Exception('Cannot connect to mysql2 database. Please check your database configuration.');
            }

            $this->command->info('Starting data migration...');

            $this->command->info('Migrating users...');
            $this->seedUsers();
            $this->clearMemory();

            $this->command->info('Migrating customers...');
            $this->seedCustomers();
            $this->clearMemory();

            $this->command->info('Migrating showrooms...');
            $this->seedShowrooms();
            $this->clearMemory();

            $this->command->info('Migrating categories...');
            $this->seedCategories();
            $this->clearMemory();

            $this->command->info('Migrating pages...');
            $this->seedPages();
            $this->clearMemory();

            $this->command->info('Migrating articles...');
            $this->seedArticles();
            $this->clearMemory();

            $this->command->info('Migrating menus...');
            $this->seedMenus();
            $this->clearMemory();

            $this->command->info('Migrating settings...');
            $this->seedSettings();
            $this->clearMemory();

            $this->command->info('Migrating sliders...');
            $this->seedSliders();
            $this->clearMemory();

            $this->command->info('Migrating products...');
            $this->seedProducts();
            $this->clearMemory();

            $this->command->info('Migrating category_product...');
            $this->seedCategoryProduct();
            $this->clearMemory();

            $this->command->info('Migrating warranties...');
            $this->seedWarranties();
            $this->clearMemory();

            $this->command->info('Migrating orders...');
            $this->seedOrders();
            $this->clearMemory();

            $this->command->info('Migrating bookings...');
            $this->seedBookings();
            $this->clearMemory();

            $this->command->info('✓ Data migration completed successfully!');
        } catch (\Exception $e) {
            $this->command->error('✗ Seeder Error: ' . $e->getMessage());
            Log::error('ConvertDBOld Seeder Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function clearMemory(): void
    {
        // DB::connection('mysql')->getPdo()->exec('SET SESSION query_cache_type = OFF');
        // if (function_exists('gc_collect_cycles')) {
        //     gc_collect_cycles();
        // }
    }

    protected function seedUsers(): void
    {
        $total = DB::connection('mysql2')->table('users')->count();
        $processed = 0;
        $inserted = 0;
        $skipped = 0;

        DB::connection('mysql2')->table('users')->orderBy('id')->chunk(100, function ($users) use (&$processed, &$inserted, &$skipped, $total) {
            foreach ($users as $user) {
                $processed++;

                // Check if user already exists, if yes, skip
                $existingUser = DB::connection('mysql')->table('users')->where('id', $user->id)->first();
                if ($existingUser) {
                    $skipped++;
                    continue; // Skip if already exists
                }

                try {
                    DB::connection('mysql')->table('users')->insert([
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'email_verified_at' => $user->email_verified_at,
                        'password' => $user->password,
                        'remember_token' => $user->remember_token,
                        'role' => $user->role,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ]);
                    $inserted++;
                } catch (\Exception $e) {
                    Log::warning("Failed to insert user {$user->id}: " . $e->getMessage());
                    continue;
                }
            }

            if ($this->command) {
                $this->command->info("  Processed: {$processed}/{$total} | Inserted: {$inserted} | Skipped: {$skipped}");
            }
        });
    }

    protected function seedCustomers(): void
    {
        $total = DB::connection('mysql2')->table('customers')->count();
        $processed = 0;
        $inserted = 0;
        $skipped = 0;

        DB::connection('mysql2')->table('customers')->orderBy('id')->chunk(100, function ($customers) use (&$processed, &$inserted, &$skipped, $total) {
            foreach ($customers as $customer) {
                $processed++;

                // Check if customer already exists, if yes, skip
                $existingCustomer = DB::connection('mysql')->table('customers')->where('id', $customer->id)->first();
                if ($existingCustomer) {
                    $skipped++;
                    continue; // Skip if already exists
                }

                try {
                    DB::connection('mysql')->table('customers')->insert([
                        'id' => $customer->id,
                        'name' => $customer->name ?? 'Customer',
                        'email' => $customer->email ?? null,
                        'phone' => $customer->phone ?? '',
                        'password' => null, // Old customers don't have password
                        'email_verified_at' => null,
                        'remember_token' => null,
                        'google_id' => null,
                        'avatar' => null,
                        'created_at' => $customer->created_at ?? now(),
                        'updated_at' => $customer->updated_at ?? now(),
                    ]);
                    $inserted++;
                } catch (\Exception $e) {
                    Log::warning("Failed to insert customer {$customer->id}: " . $e->getMessage());
                    continue;
                }
            }

            if ($this->command) {
                $this->command->info("  Processed: {$processed}/{$total} | Inserted: {$inserted} | Skipped: {$skipped}");
            }
        });
    }

    protected function seedShowrooms(): void
    {
        $total = DB::connection('mysql2')->table('showrooms')->count();
        $processed = 0;
        $inserted = 0;
        $skipped = 0;

        DB::connection('mysql2')->table('showrooms')->orderBy('id')->chunk(100, function ($showrooms) use (&$processed, &$inserted, &$skipped, $total) {
            foreach ($showrooms as $showroom) {
                $processed++;

                try {
                    $existing = DB::connection('mysql')->table('showrooms')->where('id', $showroom->id)->first();
                    if ($existing) {
                        $skipped++;
                        continue;
                    }

                    DB::connection('mysql')->table('showrooms')->insert([
                        'id' => $showroom->id,
                        'name' => $showroom->name,
                        'email' => $showroom->email,
                        'phone' => $showroom->phone,
                        'address' => $showroom->address,
                        'description' => $showroom->description,
                        'status' => $showroom->status,
                        'order' => $showroom->order,
                        'iframe' => $showroom->iframe,
                        'created_at' => $showroom->created_at ?? now(),
                        'updated_at' => $showroom->updated_at ?? now(),
                        'image' => $showroom->image,
                    ]);
                    $inserted++;
                } catch (\Exception $e) {
                    Log::warning("Failed to insert showroom {$showroom->id}: " . $e->getMessage());
                    $skipped++;
                    continue;
                }
            }

            if ($this->command) {
                $this->command->info("  Processed: {$processed}/{$total} | Inserted: {$inserted} | Skipped: {$skipped}");
            }
        });
    }

    protected function seedCategories(): void
    {
        $total = DB::connection('mysql2')->table('categories')->count();
        $processed = 0;
        $inserted = 0;
        $skipped = 0;

        DB::connection('mysql2')->table('categories')->orderBy('id')->chunk(100, function ($categories) use (&$processed, &$inserted, &$skipped, $total) {
            foreach ($categories as $category) {
                $processed++;

                try {
                    // Find parent category by old parent_id
                    $parentId = null;
                    if ($category->parent_id) {
                        $parentCategory = DB::connection('mysql')->table('product_categories')
                            ->where('id', $category->parent_id)
                            ->first();
                        $parentId = $parentCategory?->id;
                    }

                    $existing = DB::connection('mysql')->table('product_categories')->where('id', $category->id)->first();
                    if ($existing) {
                        $skipped++;
                        continue;
                    }

                    DB::connection('mysql')->table('product_categories')->insert([
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'description' => $category->description,
                        'image' => $category->thumbnail,
                        'parent_id' => $parentId,
                        'is_featured' => $category->is_featured ?? 0,
                        'sort_order' => $category->order ?? 0,
                        'created_at' => $category->created_at ?? now(),
                        'updated_at' => $category->updated_at ?? now(),
                    ]);
                    $inserted++;
                } catch (\Exception $e) {
                    Log::warning("Failed to insert category {$category->id}: " . $e->getMessage());
                    $skipped++;
                    continue;
                }
            }

            if ($this->command) {
                $this->command->info("  Processed: {$processed}/{$total} | Inserted: {$inserted} | Skipped: {$skipped}");
            }
        });
    }

    protected function seedPages(): void
    {
        $total = DB::connection('mysql2')->table('pages')->count();
        $processed = 0;
        $inserted = 0;
        $skipped = 0;

        DB::connection('mysql2')->table('pages')->orderBy('id')->chunk(100, function ($pages) use (&$processed, &$inserted, &$skipped, $total) {
            foreach ($pages as $page) {
                $processed++;

                try {
                    $existing = DB::connection('mysql')->table('news_categories')->where('id', $page->id)->first();
                    if ($existing) {
                        $skipped++;
                        continue;
                    }

                    DB::connection('mysql')->table('news_categories')->insert([
                        'id' => $page->id,
                        'name' => $page->name,
                        'slug' => $page->slug,
                        'description' => null,
                        'parent_id' => null,
                        'image' => null,
                        'is_active' => true,
                        'sort_order' => 0,
                        'created_at' => $page->created_at ?? now(),
                        'updated_at' => $page->updated_at ?? now(),
                    ]);
                    $inserted++;
                } catch (\Exception $e) {
                    Log::warning("Failed to insert page {$page->id}: " . $e->getMessage());
                    $skipped++;
                    continue;
                }
            }

            if ($this->command) {
                $this->command->info("  Processed: {$processed}/{$total} | Inserted: {$inserted} | Skipped: {$skipped}");
            }
        });
    }

    protected function seedArticles(): void
    {
        $total = DB::connection('mysql2')->table('posts')->count();
        $processed = 0;
        $inserted = 0;
        $skipped = 0;

        DB::connection('mysql2')->table('posts')->orderBy('id')->chunk(100, function ($articles) use (&$processed, &$inserted, &$skipped, $total) {
            foreach ($articles as $item) {
                $processed++;

                try {
                    $existing = DB::connection('mysql')->table('articles')->where('id', $item->id)->first();
                    if ($existing) {
                        $skipped++;
                        continue;
                    }

                    DB::connection('mysql')->table('articles')->insert([
                        'id' => $item->id,
                        'title' => $item->name,
                        'slug' => $item->slug,
                        'excerpt' => $item->description,
                        'content' => $item->content,
                        'category_id' => DB::connection('mysql2')->table('page_post')->where('post_id', $item->id)->first()?->page_id,
                        'author_id' => 1,
                        'featured_image' => $item->image,
                        'is_featured' => $item->is_featured ?? 0,
                        'is_fixed' => $item->is_fixed ?? 0,
                        'created_at' => $item->created_at ?? now(),
                        'updated_at' => $item->updated_at ?? now(),
                    ]);
                    $inserted++;
                } catch (\Exception $e) {
                    Log::warning("Failed to insert article {$item->id}: " . $e->getMessage());
                    $skipped++;
                    continue;
                }
            }

            if ($this->command) {
                $this->command->info("  Processed: {$processed}/{$total} | Inserted: {$inserted} | Skipped: {$skipped}");
            }
        });
    }

    protected function seedMenus(): void
    {
        $total = DB::connection('mysql2')->table('menu')->count();
        $processed = 0;
        $inserted = 0;
        $skipped = 0;

        DB::connection('mysql2')->table('menu')->orderBy('id')->chunk(100, function ($menus) use (&$processed, &$inserted, &$skipped, $total) {
            foreach ($menus as $menu) {
                $processed++;

                try {
                    $existing = DB::connection('mysql')->table('menus')->where('id', $menu->id)->first();
                    if ($existing) {
                        $skipped++;
                        continue;
                    }

                    $parentId = $menu->parent_id && $menu->parent_id > 0 ? $menu->parent_id : null;
                    $categoryId = DB::connection('mysql')->table('product_categories')->where('slug', $menu->link)->first()?->id ?? null;

                    DB::connection('mysql')->table('menus')->insert([
                        'id' => $menu->id,
                        'name' => $menu->name,
                        'slug' => $menu->link,
                        'menu_group_id' => $menu->main_id,
                        'parent_id' => $parentId,
                        'icon' => $menu->icon,
                        'order' => $menu->order ?? 0,
                        'is_active' => true,
                        'category_id' => $categoryId,
                        'attributes' => json_encode([]),
                        'type' => 'category',
                        'created_at' => $menu->created_at ?? now(),
                        'updated_at' => $menu->updated_at ?? now(),
                    ]);
                    $inserted++;
                } catch (\Exception $e) {
                    Log::warning("Failed to insert menu {$menu->id}: " . $e->getMessage());
                    $skipped++;
                    continue;
                }
            }

            if ($this->command) {
                $this->command->info("  Processed: {$processed}/{$total} | Inserted: {$inserted} | Skipped: {$skipped}");
            }
        });
    }

    protected function seedSettings(): void
    {
        $total = DB::connection('mysql2')->table('settings')->count();
        $processed = 0;
        $inserted = 0;
        $skipped = 0;

        DB::connection('mysql2')->table('settings')->orderBy('id')->chunk(100, function ($settings) use (&$processed, &$inserted, &$skipped, $total) {
            foreach ($settings as $setting) {
                $processed++;

                try {
                    $existing = DB::connection('mysql')->table('settings')->where('id', $setting->id)->first();
                    if ($existing) {
                        $skipped++;
                        continue;
                    }

                    DB::connection('mysql')->table('settings')->insert([
                        'id' => $setting->id,
                        'key' => $setting->key,
                        'value' => $setting->value,
                    ]);
                    $inserted++;
                } catch (\Exception $e) {
                    Log::warning("Failed to insert setting {$setting->id}: " . $e->getMessage());
                    $skipped++;
                    continue;
                }
            }

            if ($this->command) {
                $this->command->info("  Processed: {$processed}/{$total} | Inserted: {$inserted} | Skipped: {$skipped}");
            }
        });
    }

    protected function seedSliders(): void
    {
        $total = DB::connection('mysql2')->table('sliders')->count();
        $processed = 0;
        $inserted = 0;
        $skipped = 0;

        DB::connection('mysql2')->table('sliders')->orderBy('id')->chunk(100, function ($sliders) use (&$processed, &$inserted, &$skipped, $total) {
            foreach ($sliders as $slider) {
                $processed++;

                try {
                    $existing = DB::connection('mysql')->table('sliders')->where('id', $slider->id)->first();
                    if ($existing) {
                        $skipped++;
                        continue;
                    }

                    DB::connection('mysql')->table('sliders')->insert([
                        'id' => $slider->id,
                        'name' => $slider->name,
                        'key' => $slider->key,
                        'description' => $slider->description,
                        'status' => $slider->status == 'Published' ? 'active' : 'inactive',
                        'created_at' => $slider->created_at ?? now(),
                        'updated_at' => $slider->updated_at ?? now(),
                    ]);
                    $inserted++;
                } catch (\Exception $e) {
                    Log::warning("Failed to insert slider {$slider->id}: " . $e->getMessage());
                    $skipped++;
                    continue;
                }
            }

            if ($this->command) {
                $this->command->info("  Processed: {$processed}/{$total} | Inserted: {$inserted} | Skipped: {$skipped}");
            }
        });

        // Seed slider items
        $totalItems = DB::connection('mysql2')->table('slider_items')->count();
        $processedItems = 0;
        $insertedItems = 0;
        $skippedItems = 0;

        DB::connection('mysql2')->table('slider_items')->orderBy('id')->chunk(100, function ($sliderItems) use (&$processedItems, &$insertedItems, &$skippedItems, $totalItems) {
            foreach ($sliderItems as $sliderItem) {
                $processedItems++;

                try {
                    $existing = DB::connection('mysql')->table('slider_items')->where('id', $sliderItem->id)->first();
                    if ($existing) {
                        $skippedItems++;
                        continue;
                    }

                    DB::connection('mysql')->table('slider_items')->insert([
                        'id' => $sliderItem->id,
                        'slider_id' => $sliderItem->slider_id,
                        'image' => $sliderItem->image,
                        'title' => $sliderItem->title,
                        'description' => $sliderItem->description,
                        'image_mobile' => $sliderItem->image_mobile,
                        'link' => $sliderItem->link,
                        'order' => $sliderItem->order ?? 0,
                        'created_at' => $sliderItem->created_at ?? now(),
                        'updated_at' => $sliderItem->updated_at ?? now(),
                    ]);
                    $insertedItems++;
                } catch (\Exception $e) {
                    Log::warning("Failed to insert slider_item {$sliderItem->id}: " . $e->getMessage());
                    $skippedItems++;
                    continue;
                }
            }

            if ($this->command) {
                $this->command->info("  Slider Items - Processed: {$processedItems}/{$totalItems} | Inserted: {$insertedItems} | Skipped: {$skippedItems}");
            }
        });
    }

    protected function seedProducts(): void
    {
        $total = DB::connection('mysql2')->table('products')->count();
        $processed = 0;
        $inserted = 0;
        $skipped = 0;

        DB::connection('mysql2')->table('products')->orderBy('id')->chunk(50, function ($products) use (&$processed, &$inserted, &$skipped, $total) {
            foreach ($products as $product) {
                $processed++;

                try {
                    $existing = DB::connection('mysql')->table('products')->where('id', $product->id)->first();
                    if ($existing) {
                        $skipped++;
                        continue;
                    }

                    $images = DB::connection('mysql2')->table('foreign_images')->where('product_id', $product->id)->get()->map(function ($image) {
                        $image = $image->image ? str_replace('https://admin.deji.vn/', '', $image->image) : null;
                        $image = $image ? str_replace('https://deji.vn', '', $image) : null;
                        return $image;
                    })->toArray();
                    $image = $product->image ? str_replace('https://admin.deji.vn/', '', $product->image) : null;
                    $image = $image ? str_replace('https://deji.vn', '', $image) : null;

                    DB::connection('mysql')->table('products')->insert([
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'description' => $product->description,
                        'short_description' => $product->shortdescription,
                        'price' => $product->price ?? 0,
                        'price_off' => $product->priceoff ?? null,
                        'sku' => Str::random(10),
                        'category_id' => DB::connection('mysql2')->table('category_product')->where('product_id', $product->id)->first()?->category_id ?? null,
                        'images' => json_encode($images),
                        'featured_image' => $image,
                        'is_active' => $product->status == 'published' ? true : false,
                        'is_featured' => $product->is_featured == 1 ? true : false,
                        'meta_data' => json_encode(['specifications' => $product->specifications ?? []]),
                        'created_at' => $product->created_at ?? now(),
                        'updated_at' => $product->updated_at ?? now(),
                    ]);
                    $inserted++;
                } catch (\Exception $e) {
                    Log::warning("Failed to insert product {$product->id}: " . $e->getMessage());
                    $skipped++;
                    continue;
                }
            }

            if ($this->command) {
                $this->command->info("  Processed: {$processed}/{$total} | Inserted: {$inserted} | Skipped: {$skipped}");
            }
        });
    }

    protected function seedCategoryProduct(): void
    {
        $total = DB::connection('mysql2')->table('category_product')->count();
        $processed = 0;
        $inserted = 0;
        $skipped = 0;

        DB::connection('mysql2')->table('category_product')->orderBy('id')->chunk(100, function ($category_product) use (&$processed, &$inserted, &$skipped, $total) {
            foreach ($category_product as $item) {
                $processed++;

                try {
                    $existing = DB::connection('mysql')->table('category_product')->where('id', $item->id)->first();
                    if ($existing) {
                        $skipped++;
                        continue;
                    }

                    DB::connection('mysql')->table('category_product')->insert([
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'category_id' => $item->category_id,
                    ]);
                    $inserted++;
                } catch (\Exception $e) {
                    Log::warning("Failed to insert category_product {$item->id}: " . $e->getMessage());
                    $skipped++;
                    continue;
                }
            }

            if ($this->command) {
                $this->command->info("  Processed: {$processed}/{$total} | Inserted: {$inserted} | Skipped: {$skipped}");
            }
        });
    }

    protected function seedWarranties(): void
    {
        $total = DB::connection('mysql2')->table('warranties')->count();
        $processed = 0;
        $inserted = 0;
        $skipped = 0;

        DB::connection('mysql2')->table('warranties')->orderBy('id')->chunk(100, function ($warranties) use (&$processed, &$inserted, &$skipped, $total) {
            foreach ($warranties as $warranty) {
                $processed++;

                try {
                    if (!$warranty->product_id) {
                        $skipped++;
                        continue;
                    }

                    $existing = DB::connection('mysql')->table('warranties')->where('id', $warranty->id)->first();
                    if ($existing) {
                        $skipped++;
                        continue;
                    }

                    DB::connection('mysql')->table('warranties')->insert([
                        'id' => $warranty->id,
                        'product_id' => $warranty->product_id,
                        'warranty_code' => $warranty->warranty_code,
                        'status' => $warranty->status,
                        'active_date' => $warranty->active_date,
                        'time_expired' => $warranty->time_expired,
                        'month' => $warranty->month,
                        'customer_id' => $warranty->customer_id,
                    ]);
                    $inserted++;
                } catch (\Exception $e) {
                    Log::warning("Failed to insert warranty {$warranty->id}: " . $e->getMessage());
                    $skipped++;
                    continue;
                }
            }

            if ($this->command) {
                $this->command->info("  Processed: {$processed}/{$total} | Inserted: {$inserted} | Skipped: {$skipped}");
            }
        });
    }

    protected function seedOrders(): void
    {
        $total = DB::connection('mysql2')->table('carts')->count();
        $processed = 0;
        $inserted = 0;
        $skipped = 0;
        $errors = 0;

        if ($this->command) {
            $this->command->info("  Found {$total} carts to migrate");
        }

        DB::connection('mysql2')->table('carts')->orderBy('id')->chunk(50, function ($orders) use (&$processed, &$inserted, &$skipped, &$errors, $total) {
            foreach ($orders as $order) {
                $processed++;

                try {
                    $existing = DB::connection('mysql')->table('orders')->where('id', $order->id)->first();
                    if ($existing) {
                        $skipped++;
                        continue;
                    }

                    $customer = DB::connection('mysql')->table('customers')->where('id', $order->customer_id)->first();

                    // Skip if customer not found
                    if (!$customer) {
                        if ($this->command) {
                            $this->command->warn("  Cart {$order->id}: Customer {$order->customer_id} not found, skipping");
                        }
                        $skipped++;
                        continue;
                    }

                    $orderNumber = Str::random(10);
                    $totalAmount = 0;
                    $orderItems = DB::connection('mysql2')->table('cart_product')->where('cart_id', $order->id)->get();
                    foreach ($orderItems as $orderItem) {
                        $product = DB::connection('mysql')->table('products')->where('id', $orderItem->product_id)->first();
                        if ($product) {
                            $totalAmount += ($product->price ?? 0) * ($orderItem->quantity ?? 0);
                        }
                    }

                    DB::connection('mysql')->table('orders')->insert([
                        'id' => $order->id,
                        'order_number' => $orderNumber,
                        'customer_id' => $customer->id,
                        'customer_name' => $customer->name ?? 'Customer',
                        'customer_email' => $customer->email ?? null,
                        'customer_phone' => $customer->phone ?? '',
                        'subtotal' => $totalAmount,
                        'tax' => $totalAmount * 0.1,
                        'shipping_fee' => 0,
                        'total' => $totalAmount + $totalAmount * 0.1,
                        'payment_method' => $order->payment ?? null,
                        'payment_status' => Order::PAYMENT_STATUS_SUCCESS,
                        'status' => Order::STATUS_COMPLETED,
                        'district' => $order->district ?? null,
                        'city' => $order->city ?? null,
                        'province' => $order->province ?? null,
                        'created_at' => $order->created_at ?? now(),
                        'updated_at' => $order->updated_at ?? now(),
                    ]);
                    $inserted++;

                    // Insert order items
                    $orderItems = DB::connection('mysql2')->table('cart_product')->where('cart_id', $order->id)->get();
                    foreach ($orderItems as $orderItem) {
                        try {
                            $existing = DB::connection('mysql')->table('order_items')->where('id', $orderItem->id)->first();
                            if ($existing) {
                                continue;
                            }

                            $product = DB::connection('mysql')->table('products')->where('id', $orderItem->product_id)->first();

                            // Skip if product not found
                            if (!$product) {
                                continue;
                            }

                            DB::connection('mysql')->table('order_items')->insert([
                                'id' => $orderItem->id,
                                'order_id' => $order->id,
                                'product_id' => $orderItem->product_id,
                                'quantity' => $orderItem->quantity ?? 1,
                                'price' => $product->price ?? 0,
                                'total' => ($product->price ?? 0) * ($orderItem->quantity ?? 1),
                                'created_at' => $orderItem->created_at ?? now(),
                                'updated_at' => $orderItem->updated_at ?? now(),
                            ]);
                        } catch (\Exception $e) {
                            Log::warning("Failed to insert order_item {$orderItem->id}: " . $e->getMessage());
                            continue;
                        }
                    }
                } catch (\Exception $e) {
                    $errors++;
                    if ($this->command) {
                        $this->command->error("  Failed to insert order {$order->id}: " . $e->getMessage());
                    }
                    Log::error("Failed to insert order {$order->id}: " . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                    $skipped++;
                    continue;
                }
            }

            if ($this->command) {
                $this->command->info("  Processed: {$processed}/{$total} | Inserted: {$inserted} | Skipped: {$skipped} | Errors: {$errors}");
            }
        });

        if ($this->command) {
            $this->command->info("  ✓ Orders migration completed: {$inserted} orders inserted");
        }
    }

    protected function seedBookings(): void
    {
        $total = DB::connection('mysql2')->table('bookings')->count();
        $processed = 0;
        $inserted = 0;
        $skipped = 0;

        DB::connection('mysql2')->table('bookings')->orderBy('id')->chunk(100, function ($bookings) use (&$processed, &$inserted, &$skipped, $total) {
            foreach ($bookings as $booking) {
                $processed++;

                try {
                    $existing = DB::connection('mysql')->table('bookings')->where('id', $booking->id)->first();
                    if ($existing) {
                        $skipped++;
                        continue;
                    }

                    DB::connection('mysql')->table('bookings')->insert([
                        'id' => $booking->id,
                        'customer_id' => $booking->customer_id,
                        'product_id' => $booking->product_id,
                        'showroom_id' => $booking->showroom_id,
                        'price' => $booking->price,
                        'date' => $booking->date,
                        'time' => $booking->time,
                    ]);
                    $inserted++;
                } catch (\Exception $e) {
                    Log::warning("Failed to insert booking {$booking->id}: " . $e->getMessage());
                    $skipped++;
                    continue;
                }
            }

            if ($this->command) {
                $this->command->info("  Processed: {$processed}/{$total} | Inserted: {$inserted} | Skipped: {$skipped}");
            }
        });
    }
}
