<?php

namespace Vendor\Product\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Vendor\Product\Http\Resources\ProductResource;
use Vendor\Product\Models\Product;
use Vendor\Product\Models\ProductCategory;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::active()->whereNull('parent_id')->orderBy('sort_order')->orderBy('name')->get();

        return response()->json([
            'data' => $categories,
        ]);
    }
    public function featured()
    {
        $categories = ProductCategory::active()->featured()->orderBy('sort_order')->orderBy('name')->get();
        return response()->json([
            'data' => $categories,
        ]);
    }

    public function getProductByCategoryID(Request $request, $id)
    {
        $category = ProductCategory::where('id', $id)->first();

        if (!$category) {
            return response()->json([
                'error' => 'Category not found',
            ], 404);
        }

        $perPage = $request->query('per_page', 20);
        $page = $request->query('page', 1);

        $products = Product::active()
            ->byCategory($category->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'category' => $category,
            'products' => [
                'data' => ProductResource::collection($products->items()),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * Get products by category slug with pagination.
     */
    public function getProductsByCategorySlug(Request $request, $slug)
    {
        $category = ProductCategory::where('slug', $slug)
            ->orWhere('id', $slug) // Support both slug and ID
            ->first();

        if (!$category) {
            return response()->json([
                'error' => 'Category not found',
            ], 404);
        }

        $perPage = $request->query('per_page', 20);
        $page = $request->query('page', 1);

        $products = Product::active()
            ->byCategory($category->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'category' => $category,
            'products' => [
                'data' => ProductResource::collection($products->items()),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }
}
