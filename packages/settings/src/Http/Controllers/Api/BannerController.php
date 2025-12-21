<?php

namespace Vendor\Settings\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Vendor\Settings\Models\Slider;
use Vendor\Settings\Http\Resources\SliderResource;
use Vendor\Settings\Http\Resources\BannerResource;

class BannerController extends Controller
{
    /**
     * Get top banner for home page
     *
     * @return JsonResponse
     */
    public function getBannerTop(): JsonResponse
    {
        try {
            $slider = Slider::where('key', 'home-banner-top')
                ->where('status', 'active')
                ->with(['items' => function ($query) {
                    $query->orderBy('order', 'asc');
                }])
                ->first();

            if ($slider && $slider->items->count() > 0) {
                return response()->json([
                    'success' => true,
                    'data' => [new SliderResource($slider)],
                ]);
            }

            // Return fake data if no banner found
            return response()->json([
                'success' => true,
                'data' => [
                    [
                        'id' => 1,
                        'key' => 'home-banner-top',
                        'name' => 'Home Banner Top',
                        'description' => 'Banner slider for home page',
                        'status' => 'active',
                        'items' => [
                            [
                                'id' => 1,
                                'title' => 'Banner 1',
                                'image' => 'https://dummyimage.com/400x200/E70214/FFFFFF.png&text=Banner+1',
                                'image_mobile' => 'https://dummyimage.com/400x200/E70214/FFFFFF.png&text=Banner+1',
                                'link' => null,
                                'order' => 1,
                                'description' => null,
                            ],
                            [
                                'id' => 2,
                                'title' => 'Banner 2',
                                'image' => 'https://dummyimage.com/400x200/0066CC/FFFFFF.png&text=Banner+2',
                                'image_mobile' => 'https://dummyimage.com/400x200/0066CC/FFFFFF.png&text=Banner+2',
                                'link' => null,
                                'order' => 2,
                                'description' => null,
                            ],
                            [
                                'id' => 3,
                                'title' => 'Banner 3',
                                'image' => 'https://dummyimage.com/400x200/00AA44/FFFFFF.png&text=Banner+3',
                                'image_mobile' => 'https://dummyimage.com/400x200/00AA44/FFFFFF.png&text=Banner+3',
                                'link' => null,
                                'order' => 3,
                                'description' => null,
                            ],
                        ],
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching banner: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    /**
     * Get left banner
     *
     * @return JsonResponse
     */
    public function getBannerLeft(): JsonResponse
    {
        try {
            $slider = Slider::where('key', 'home-banner-left')
                ->where('status', 'active')
                ->with(['items' => function ($query) {
                    $query->orderBy('order', 'asc');
                }])
                ->first();

            if ($slider && $slider->items->count() > 0) {
                return response()->json([
                    'success' => true,
                    'data' => [new SliderResource($slider)],
                ]);
            }

            // Return empty array if no banner found
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching banner: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}

