<?php

namespace Vendor\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Vendor\Product\Models\PopularSearch;

/**
 * Admin Popular Search Controller (API)
 * 
 * API endpoints for managing popular searches (admin only)
 */
class AdminPopularSearchController extends Controller
{
    /**
     * Get all popular searches (paginated)
     * 
     * Query params:
     * - search: Filter by keyword
     * - status: Filter by active/inactive (active|inactive)
     * - per_page: Items per page (default: 20)
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

        $perPage = (int) $request->input('per_page', 20);
        $popularSearches = $query->ordered()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $popularSearches->items(),
            'pagination' => [
                'current_page' => $popularSearches->currentPage(),
                'last_page' => $popularSearches->lastPage(),
                'per_page' => $popularSearches->perPage(),
                'total' => $popularSearches->total(),
            ],
        ]);
    }

    /**
     * Get single popular search
     */
    public function show(PopularSearch $popularSearch)
    {
        return response()->json([
            'success' => true,
            'data' => $popularSearch,
        ]);
    }

    /**
     * Create new popular search
     * 
     * Request body:
     * {
     *   "keyword": "string",
     *   "sort_order": 0,
     *   "is_active": true
     * }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'keyword' => 'required|string|max:255|unique:popular_searches,keyword',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $popularSearch = PopularSearch::create([
            'keyword' => trim($validated['keyword']),
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Popular search created successfully',
            'data' => $popularSearch,
        ], 201);
    }

    /**
     * Update popular search
     */
    public function update(Request $request, PopularSearch $popularSearch)
    {
        $validated = $request->validate([
            'keyword' => 'required|string|max:255|unique:popular_searches,keyword,' . $popularSearch->id,
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $popularSearch->update([
            'keyword' => trim($validated['keyword']),
            'sort_order' => $validated['sort_order'] ?? $popularSearch->sort_order,
            'is_active' => $validated['is_active'] ?? $popularSearch->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Popular search updated successfully',
            'data' => $popularSearch->fresh(),
        ]);
    }

    /**
     * Delete popular search
     */
    public function destroy(PopularSearch $popularSearch)
    {
        $popularSearch->delete();

        return response()->json([
            'success' => true,
            'message' => 'Popular search deleted successfully',
        ]);
    }

    /**
     * Bulk update sort order
     * 
     * Request body:
     * {
     *   "items": [
     *     { "id": 1, "sort_order": 0 },
     *     { "id": 2, "sort_order": 1 }
     *   ]
     * }
     */
    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:popular_searches,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['items'] as $item) {
            PopularSearch::where('id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sort order updated successfully',
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(PopularSearch $popularSearch)
    {
        $popularSearch->update([
            'is_active' => !$popularSearch->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'data' => $popularSearch->fresh(),
        ]);
    }
}

