<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
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


        $users =   DB::connection('mysql2')->table('users')->get();
        foreach ($users as $user) {
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
        }

        $customers =   DB::connection('mysql2')->table('customers')->get();
        foreach ($customers as $customer) {
            DB::connection('mysql')->table('customers')->insert([
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
            ]);
        }


        $showrooms = DB::connection('mysql2')->table('showrooms')->get();
        foreach ($showrooms as $showroom) {
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
                'created_at' => $showroom->created_at,
                'updated_at' => $showroom->updated_at,
                'image' => $showroom->image,
            ]);
        }

        $categories = DB::connection('mysql2')->table('categories')->get();
        foreach ($categories as $category) {
            DB::connection('mysql')->table('product_categories')->insert([
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                // 'is_active' => $category->status == 'published' ? 1 : 0,
                'image' => $category->thumbnail,
                'parent_id' => DB::connection('mysql')->table('product_categories')->where('parent_id', $category->parent_id)->first()?->id ?? null,
                'is_featured' => $category->is_featured,
                'sort_order' => $category->order,
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at,
            ]);
        }


        $pages = DB::connection('mysql2')->table('pages')->get();
        foreach ($pages as $page) {
            DB::connection('mysql')->table('news_categories')->insert([
                'id' => $page->id,
                'name' => $page->name,
                'slug' => $page->slug,
                'description' => null,
                'parent_id' => null,
                'image' => null,
                'is_active' => true,
                'sort_order' => 0,
            ]);
        }


        $article =   DB::connection('mysql2')->table('posts')->get();
        foreach ($article as $item) {
            DB::connection('mysql')->table('articles')->insert([
                'id' => $item->id,
                'title' => $item->name,
                'slug' => $item->slug,
                'excerpt' => $item->description,
                'content' => $item->content,
                'category_id' => DB::connection('mysql2')->table('page_post')->where('post_id', $item->id)->first()?->page_id,
                'author_id' => 1,
                'featured_image' => $item->image,
                //'images' => $item->images,
                'is_featured' => $item->is_featured,
                'is_fixed' => $item->is_fixed,

            ]);
        }


        $menus = DB::connection('mysql2')->table('menu')->get();
        foreach ($menus as $menu) {
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
            ]);
        }



        $settings = DB::connection('mysql2')->table('settings')->get();
        foreach ($settings as $setting) {
            DB::connection('mysql')->table('settings')->insert([
                'id' => $setting->id,
                'key' => $setting->key,
                'value' => $setting->value,
            ]);
        }

        $sliders = DB::connection('mysql2')->table('sliders')->get();
        foreach ($sliders as $slider) {
            DB::connection('mysql')->table('sliders')->insert([
                'id' => $slider->id,
                'name' => $slider->name,
                'key' => $slider->key,
                'description' => $slider->description,
                'status' => $slider->status == 'Published' ? 'active' : 'inactive',

            ]);
        }
        $sliderItems = DB::connection('mysql2')->table('slider_items')->get();
        foreach ($sliderItems as $sliderItem) {
            DB::connection('mysql')->table('slider_items')->insert([
                'id' => $sliderItem->id,
                'slider_id' => $sliderItem->slider_id,
                'image' => $sliderItem->image,
                'title' => $sliderItem->title,
                'description' => $sliderItem->description,
                'image_mobile' => $sliderItem->image_mobile,
                'link' => $sliderItem->link,
                'order' => $sliderItem->order,
            ]);
        }


        $products = DB::connection('mysql2')->table('products')->get();
        foreach ($products as $product) {
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
                'price' => $product->price,
                'price_off' => $product->priceoff,
                'sku' => \Str::random(10),
                'category_id' => DB::connection('mysql2')->table('category_product')->where('product_id', $product->id)->first()?->category_id ?? null,
                'images' => json_encode($images),
                'featured_image' => $image,
                'is_active' => $product->status == 'published' ? true : false,
                'is_featured' => $product->is_featured == 1 ? true : false,
                'meta_data' => json_encode(['specifications' => $product->specifications]),
            ]);
        }

        $category_product = DB::connection('mysql2')->table('category_product')->get();
        foreach ($category_product as $item) {
            DB::connection('mysql')->table('category_product')->insert([
                'id' => $item->id,
                'product_id' => $item->product_id,
                'category_id' => $item->category_id,
            ]);
        }

        $warranties = DB::connection('mysql2')->table('warranties')->get();
        foreach ($warranties as $warranty) {
            if (!$warranty->product_id) {
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
        }

        $orders = DB::connection('mysql2')->table('carts')->get();
        foreach ($orders as $order) {
            $orderNumber = \Str::random(10);
            $customer = DB::connection('mysql')->table('customers')->where('id', $order->customer_id)->first();
            $total  = 0;
            $orderItems = DB::connection('mysql2')->table('cart_product')->where('cart_id', $order->id)->get();
            foreach ($orderItems as $orderItem) {
                $product = DB::connection('mysql')->table('products')->where('id', $orderItem->product_id)->first();
                $total += $product->price * $orderItem->quantity;
            }
            DB::connection('mysql')->table('orders')->insert([
                'id' => $order->id,
                'order_number' => $orderNumber,
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone,
                //   'customer_address' => $customer->address,
                'subtotal' => $total,
                'tax' => $total * 0.1,
                'total' => $total + $total * 0.1,
                'payment_method' => $order->payment,
                'payment_status' => Order::PAYMENT_STATUS_SUCCESS,
                'status' => Order::STATUS_COMPLETED,
                // 'appointment_date' => $order->appointment_date,
                // 'appointment_time' => $order->appointment_time,
                // 'appointment_note' => $order->appointment_note,
                // 'appointment_status' => $order->appointment_status,
                // 'notes' => $order->notes,
                // 'metadata' => json_encode($order->metadata),
                'district' => $order->district,
                'city' => $order->city,
                'province' => $order->province,
                //  'address' => $order->address,
            ]);
            $orderItems = DB::connection('mysql2')->table('cart_product')->where('cart_id', $order->id)->get();
            foreach ($orderItems as $orderItem) {
                $product = DB::connection('mysql')->table('products')->where('id', $orderItem->product_id)->first();
                DB::connection('mysql')->table('order_items')->insert([
                    'id' => $orderItem->id,
                    'order_id' => $order->id,
                    'product_id' => $orderItem->product_id,
                    'quantity' => $orderItem->quantity,
                    'price' => $product->price,
                    'total' => $product->price * $orderItem->quantity,
                ]);
            }
        }

        $bookings = DB::connection('mysql2')->table('bookings')->get();
        foreach ($bookings as $booking) {
            DB::connection('mysql')->table('bookings')->insert([
                'id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'product_id' => $booking->product_id,
                'showroom_id' => $booking->showroom_id,
                'price' => $booking->price,
                'date' => $booking->date,
                'time' => $booking->time,
            ]);
        }
    }
}
