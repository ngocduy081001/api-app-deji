<?php

namespace Vendor\Product\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Vendor\Product\Http\Resources\ProductResource;
use Vendor\Product\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::active()->featured()->orderBy('created_at', 'desc')->paginate(16);
        return ProductResource::collection($products);
    }

    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        return ProductResource::make($product);
    }
}
