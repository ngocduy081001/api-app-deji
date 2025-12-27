<?php

namespace Vendor\Settings\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Vendor\Settings\Models\Slider;
use Vendor\Settings\Http\Resources\SliderResource;
use Vendor\Settings\Http\Resources\BannerResource;
use Vendor\Settings\Models\Banner;

class BannerController extends Controller
{
    public function getMain()
    {
        $banners = Banner::byPosition('main')->active()->orderBy('order')->get();
        return BannerResource::collection($banners);
    }
}
