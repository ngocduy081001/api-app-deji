<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;
use Vendor\Order\Http\Controllers\BookingController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');


// Route::get('/convert-data', [APIController::class, 'convertData']);

// Route::get('/proxy/provinces', function () {
//     try {
//         $response = Http::timeout(5)->get('https://provinces.open-api.vn/api/p');

//         if ($response->successful()) {
//             return response()->json($response->json());
//         }

//         return response()->json(['error' => 'Failed to fetch provinces'], $response->status());
//     } catch (\Exception $e) {

//         return response()->json(['error' => 'Unable to reach provinces API'], 500);
//     }
// });


// // Proxy for fetching cities by province code
// Route::get('/proxy/cities/{provinceCode}', function ($provinceCode) {
//     try {
//         $response = Http::timeout(5)->get("https://provinces.open-api.vn/api/p/{$provinceCode}?depth=2");


//         if ($response->successful()) {
//             return response()->json($response->json());
//         }

//         return response()->json(['error' => 'Failed to fetch cities'], $response->status());
//     } catch (\Exception $e) {

//         return response()->json(['error' => 'Unable to reach cities API'], 500);
//     }
// });

// // Proxy for fetching districts by city code
// Route::get('/proxy/districts/{cityCode}', function ($cityCode) {
//     try {
//         $response = Http::timeout(5)->get("https://provinces.open-api.vn/api/d/{$cityCode}?depth=2");


//         if ($response->successful()) {
//             return response()->json($response->json());
//         }

//         return response()->json(['error' => 'Failed to fetch districts'], $response->status());
//     } catch (\Exception $e) {

//         return response()->json(['error' => 'Unable to reach districts API'], 500);
//     }
// });




// Route::get('/page/{slug}', [APIController::class, 'page']);
// Route::get('/post/{slug}', [APIController::class, 'post']);
// Route::get('/posts', [APIController::class, 'posts']);
// Route::get('/product/{slug}', [APIController::class, 'product']);
// Route::get('/products', [APIController::class, 'products']);

// Route::get('/category/{slug}', [APIController::class, 'category']);
// Route::get('/categories', [APIController::class, 'categories']);
// Route::get('/categories/all', [APIController::class, 'categoriesAll']);
// Route::get('/categories/featured', [APIController::class, 'categoriesIsFeatured']);
// Route::get('/showrooms', [APIController::class, 'showrooms']);
// Route::get('/sliders/{id}', [APIController::class, 'slider']);
// Route::get('/settings', [APIController::class, 'settings']);
// Route::get('/blocks', [APIController::class, 'blocks']);
// Route::get('/menu', [APIController::class, 'menu']);
// Route::get('/slug', [APIController::class, 'slug']);
// Route::get('/warranty-lookup', [APIController::class, 'warrantyLookup']);
// Route::get('/warranty-lookup-code', [APIController::class, 'warrantyLookupCode']);
// Route::post('/warranty-active', [APIController::class, 'WarrantyActive']);
// Route::post('/order', [APIController::class, 'order']);
// Route::post('/cart', [APIController::class, 'order']);
// Route::post('/booking', [\Vendor\Order\Http\Controllers\Web\BookingController::class, 'store']);
// Route::get('/search', [APIController::class, 'search']);
// Route::get('/product-feature', [APIController::class, 'getProductFeature']);
// Route::get('/post-feature', [APIController::class, 'getPostFeature']);
// Route::get('/categories/{id}/children', [APIController::class, 'getSubCategoriesChildrents']);
// Route::get('/main-menu', [APIController::class, 'getMainMenu']);
// Route::get('/main-menu2', [APIController::class, 'getMainMenu2']);
// Route::get('/categories/{id}/children2', [APIController::class, 'getSubCategoriesChildrents2']);
// Route::get('/banner/top', [\Vendor\Settings\Http\Controllers\Api\BannerController::class, 'getBannerTop']);
// Route::get('/banner/left', [\Vendor\Settings\Http\Controllers\Api\BannerController::class, 'getBannerLeft']);
// Route::get('/warranty-lookup-phone', [APIController::class, 'getWarrantyLookupPhone']);
// Route::get('/partners', [APIController::class, 'partners']);
// Route::get('/seo', [APIController::class, 'seo']);
// Route::get('/footer', [APIController::class, 'getFooter']);
