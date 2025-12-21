<?php

namespace Vendor\Product\Http\Controllers\Web;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Vendor\Product\Models\Product;
use Vendor\Product\Models\ProductCategory;
use Vendor\Product\Http\Requests\StoreProductRequest;
use Vendor\Product\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of products (admin view).
     */
    public function index(Request $request)
    {

        $query = Product::with('category');

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);
        $categories = ProductCategory::orderBy('name')->get();


        return view('product::admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = $this->getCategoriesTree();
        return view('product::admin.products.create', compact('categories'));
    }

    /**
     * Get categories in tree structure
     */
    protected function getCategoriesTree()
    {
        $categories = ProductCategory::with('children')->whereNull('parent_id')->orderBy('sort_order')->orderBy('name')->get();

        // Set level for each category and its children recursively
        $this->setCategoryLevels($categories, 0);

        return $categories;
    }

    /**
     * Set level for categories recursively
     */
    protected function setCategoryLevels($categories, $level = 0)
    {
        foreach ($categories as $category) {
            $category->level = $level;

            if ($category->children && $category->children->count() > 0) {
                $this->setCategoryLevels($category->children, $level + 1);
            }
        }
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $categories = $data['categories'] ?? [];
        $specifications = $data['specifications'] ?? [];
        $replacementPrice = $data['replacement_price'] ?? null;

        // Remove categories and specifications from data to avoid mass assignment issues
        unset($data['categories'], $data['specifications'], $data['replacement_price']);

        // Prepare meta_data
        $metaData = $data['meta_data'] ?? [];
        if (!empty($specifications)) {
            // Filter out empty specifications
            $specifications = array_filter($specifications, function ($spec) {
                return !empty($spec['name']) && !empty($spec['value']);
            });
            if (!empty($specifications)) {
                $metaData['specifications'] = array_values($specifications);
            }
        }
        if ($replacementPrice !== null) {
            $metaData['replacement_price'] = $replacementPrice;
        }

        $data['meta_data'] = !empty($metaData) ? $metaData : null;

        $product = Product::create($data);

        // Sync categories
        if (!empty($categories)) {
            $product->categories()->sync($categories);
        }

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'categories', 'variants']);
        return view('product::admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $product->load('categories');
        $categories = $this->getCategoriesTree();

        return view('product::admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $categories = $data['categories'] ?? [];
        $specifications = $data['specifications'] ?? [];
        $replacementPrice = $data['replacement_price'] ?? null;

        // Remove categories and specifications from data to avoid mass assignment issues
        unset($data['categories'], $data['specifications'], $data['replacement_price']);

        // Prepare meta_data - merge with existing meta_data
        $existingMetaData = $product->meta_data ?? [];
        $metaData = $data['meta_data'] ?? $existingMetaData;

        // Handle specifications - check if specifications key exists in request (even if empty)
        if ($request->has('specifications')) {
            // Filter out empty specifications
            $specifications = array_filter($specifications, function ($spec) {
                return !empty($spec['name']) && !empty($spec['value']);
            });
            if (!empty($specifications)) {
                $metaData['specifications'] = array_values($specifications);
            } else {
                // Remove specifications if all are empty or array is empty
                unset($metaData['specifications']);
            }
        } elseif (isset($existingMetaData['specifications'])) {
            // Keep existing specifications if not provided in request
            $metaData['specifications'] = $existingMetaData['specifications'];
        }

        if ($replacementPrice !== null) {
            $metaData['replacement_price'] = $replacementPrice;
        } elseif (isset($existingMetaData['replacement_price'])) {
            // Keep existing replacement_price if not provided
            $metaData['replacement_price'] = $existingMetaData['replacement_price'];
        }

        $data['meta_data'] = !empty($metaData) ? $metaData : null;

        $product->update($data);

        // Sync categories
        $product->categories()->sync($categories);

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product (soft delete).
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
