<?php

namespace Vendor\Product\Http\Controllers\Web;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Vendor\Product\Models\Product;
use Vendor\Product\Models\ProductCategory;

class CatalogController extends Controller
{
    /**
     * Display product catalog (public storefront).
     */
    public function index(Request $request)
    {
        $query = Product::active()->with('category');

        // Filter by category
        if ($request->has('category')) {
            $category = ProductCategory::where('slug', $request->input('category'))->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        // Sort
        $sortBy = $request->input('sort', 'created_at');
        $sortOrder = $request->input('order', 'desc');

        if ($sortBy === 'popularity') {
            $query->orderBy('view_count', 'desc');
        } elseif ($sortBy === 'price') {
            $query->orderByRaw('COALESCE(sale_price, price) ' . $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $products = $query->paginate(24);
        $categories = ProductCategory::active()->orderBy('sort_order')->orderBy('name')->get();

        return view('product::catalog.index', compact('products', 'categories'));
    }

    /**
     * Display product detail page.
     */
    public function show(string $slug)
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with(['category', 'variants.attributeValues.attribute'])
            ->firstOrFail();

        // Increment view count
        $product->incrementViewCount();

        // Get related products
        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inStock()
            ->take(4)
            ->get();

        return view('product::catalog.show', compact('product', 'relatedProducts'));
    }

    /**
     * Display products by category.
     */
    public function category(string $slug)
    {
        $category = ProductCategory::active()
            ->where('slug', $slug)
            ->with('children')
            ->firstOrFail();

        $query = Product::active()->where('category_id', $category->id);

        $products = $query->paginate(24);

        return view('product::catalog.category', compact('category', 'products'));
    }
}
