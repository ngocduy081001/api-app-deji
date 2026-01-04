<?php

namespace Vendor\Product\Http\Controllers\Web;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Vendor\Product\Models\PopularSearch;

/**
 * Admin Popular Search Controller (Web)
 * 
 * Manages popular searches for admin panel
 */
class PopularSearchController extends Controller
{
    /**
     * Display popular searches management page
     */
    public function index(Request $request)
    {
        $query = PopularSearch::query();

        // Search filter
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('keyword', 'like', "%{$search}%");
        }

        // Status filter
        if ($request->has('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $popularSearches = $query->ordered()->paginate(20);

        // Return JSON for API calls or view for web
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $popularSearches,
            ]);
        }

        return view('product::admin.popular-searches.index', compact('popularSearches'));
    }

    /**
     * Store a newly created popular search
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'keyword' => 'required|string|max:255|unique:popular_searches,keyword',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $popularSearch = PopularSearch::create([
                'keyword' => trim($validated['keyword']),
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Từ khóa tìm kiếm phổ biến đã được tạo thành công.',
                'data' => $popularSearch,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo từ khóa.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified popular search
     */
    public function update(Request $request, PopularSearch $popularSearch): JsonResponse
    {
        $validated = $request->validate([
            'keyword' => 'required|string|max:255|unique:popular_searches,keyword,' . $popularSearch->id,
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $popularSearch->update([
                'keyword' => trim($validated['keyword']),
                'sort_order' => $validated['sort_order'] ?? $popularSearch->sort_order,
                'is_active' => $validated['is_active'] ?? $popularSearch->is_active,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Từ khóa tìm kiếm phổ biến đã được cập nhật thành công.',
                'data' => $popularSearch->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật từ khóa.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified popular search
     */
    public function destroy(PopularSearch $popularSearch): JsonResponse
    {
        try {
            $popularSearch->delete();

            return response()->json([
                'success' => true,
                'message' => 'Từ khóa tìm kiếm phổ biến đã được xóa thành công.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa từ khóa.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update sort order (bulk update)
     */
    public function updateOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:popular_searches,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        try {
            foreach ($validated['items'] as $item) {
                PopularSearch::where('id', $item['id'])
                    ->update(['sort_order' => $item['sort_order']]);
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
     * Toggle active status
     */
    public function toggleStatus(PopularSearch $popularSearch): JsonResponse
    {
        try {
            $popularSearch->update([
                'is_active' => !$popularSearch->is_active,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Trạng thái đã được cập nhật thành công.',
                'data' => $popularSearch->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

