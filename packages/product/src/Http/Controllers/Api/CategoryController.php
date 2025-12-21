<?php

namespace Vendor\Product\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Vendor\Product\Models\ProductCategory;

class CategoryController extends Controller
{
    public function featured()
    {
        $categories = ProductCategory::featured()->get();

        return response()->json([
            'data' => $categories,
        ]);
    }
}
