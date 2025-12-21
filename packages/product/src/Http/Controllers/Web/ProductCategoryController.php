<?php

namespace Vendor\Product\Http\Controllers\Web;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Vendor\Product\Models\ProductCategory;
use Vendor\Product\Http\Requests\StoreProductCategoryRequest;
use Vendor\Product\Http\Requests\UpdateProductCategoryRequest;

class ProductCategoryController extends Controller
{
    /**
     * Display categories management page.
     */
    public function index()
    {
        $categoryTree = $this->buildCategoryTree();
        $allCategories = $this->getAllCategories();

        return view('product::admin.categories.form', [
            'category' => null,
            'categoryTree' => $categoryTree,
            'allCategories' => $allCategories,
        ]);
    }

    /**
     * Store a newly created category via AJAX.
     */
    public function store(StoreProductCategoryRequest $request): JsonResponse
    {
        try {
            $category = ProductCategory::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Danh mục đã được tạo thành công.',
                'data' => $category,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo danh mục.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified category via AJAX.
     */
    public function update(UpdateProductCategoryRequest $request, ProductCategory $category): JsonResponse
    {
        try {
            $category->update($request->validated());
            $category->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Danh mục đã được cập nhật thành công.',
                'data' => $category,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật danh mục.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get category data for editing via AJAX.
     */
    public function getCategory(ProductCategory $category): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $category,
        ]);
    }

    /**
     * Remove the specified category via AJAX.
     */
    public function destroy(ProductCategory $category): JsonResponse
    {
        try {
            // Prevent deleting category with children
            if ($category->children()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa danh mục đang có danh mục con.',
                ], 422);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Danh mục đã được xóa thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa danh mục.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the order of categories via AJAX.
     */
    public function updateOrder(Request $request): JsonResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:product_categories,id',
            'parent_id' => 'nullable|integer|exists:product_categories,id',
        ]);

        try {
            $parentId = $request->input('parent_id');

            foreach ($request->order as $index => $categoryId) {
                $updateData = ['sort_order' => $index + 1];

                // Update parent_id if provided (when moving between parents)
                if ($parentId !== null) {
                    $category = ProductCategory::find($categoryId);
                    if ($category && $category->parent_id != $parentId) {
                        $updateData['parent_id'] = $parentId;
                    }
                }

                ProductCategory::where('id', $categoryId)->update($updateData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Thứ tự đã được cập nhật thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật thứ tự.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Build category tree structure with nested eager loading.
     */
    private function buildCategoryTree(?int $excludeId = null)
    {
        $query = ProductCategory::whereNull('parent_id');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $eagerLoad = $this->buildNestedEagerLoad(5, $excludeId);

        return $query->with($eagerLoad)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Build nested eager load array recursively.
     */
    private function buildNestedEagerLoad(int $depth, ?int $excludeId = null, string $prefix = 'children'): array
    {
        $eagerLoad = [];

        if ($depth <= 0) {
            return $eagerLoad;
        }

        $eagerLoad[$prefix] = function ($query) use ($excludeId) {
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            $query->orderBy('sort_order')->orderBy('name');
        };

        if ($depth > 1) {
            $deeper = $this->buildNestedEagerLoad($depth - 1, $excludeId, $prefix . '.children');
            $eagerLoad = array_merge($eagerLoad, $deeper);
        }

        return $eagerLoad;
    }

    /**
     * Get all categories for parent selector.
     */
    private function getAllCategories(?int $excludeId = null)
    {
        $query = ProductCategory::query();

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
}
