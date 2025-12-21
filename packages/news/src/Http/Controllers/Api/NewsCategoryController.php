<?php

namespace Vendor\News\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Vendor\News\Http\Requests\StoreNewsCategoryRequest;
use Vendor\News\Http\Requests\UpdateNewsCategoryRequest;
use Vendor\News\Models\NewsCategory;

class NewsCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = NewsCategory::query();

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by parent_id (null for root categories)
        if ($request->has('parent_id')) {
            $parentId = $request->input('parent_id');
            if ($parentId === 'null' || $parentId === null) {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $parentId);
            }
        }

        // Include children relationships
        if ($request->boolean('with_children')) {
            $query->with('children');
        }

        // Include parent relationship
        if ($request->boolean('with_parent')) {
            $query->with('parent');
        }

        // Include all descendants (children, grandchildren, etc.)
        if ($request->boolean('with_descendants')) {
            $query->with('descendants');
        }

        // Include articles count
        if ($request->boolean('with_articles_count')) {
            $query->withCount('articles');
        }

        // Search by name
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Sort
        $sortBy = $request->input('sort_by', 'sort_order');
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->input('per_page', 15);

        if ($request->has('per_page') && $request->input('per_page') === 'all') {
            $categories = $query->get();
            return response()->json([
                'success' => true,
                'data' => $categories,
            ]);
        }

        $categories = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $categories->items(),
            'meta' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param StoreNewsCategoryRequest $request
     * @return JsonResponse
     */
    public function store(StoreNewsCategoryRequest $request): JsonResponse
    {
        try {
            $category = NewsCategory::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'News category created successfully',
                'data' => $category->load(['parent', 'children']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create news category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $query = NewsCategory::query();

        // Include relationships
        if ($request->boolean('with_children')) {
            $query->with('children');
        }

        if ($request->boolean('with_parent')) {
            $query->with('parent');
        }

        if ($request->boolean('with_descendants')) {
            $query->with('descendants');
        }

        if ($request->boolean('with_articles')) {
            $query->with(['articles' => function ($q) {
                $q->published()->orderBy('published_at', 'desc');
            }]);
        }

        if ($request->boolean('with_articles_count')) {
            $query->withCount('articles');
        }

        $category = $query->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'News category not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category,
        ]);
    }

    /**
     * Display the specified resource by slug.
     * 
     * @param Request $request
     * @param string $slug
     * @return JsonResponse
     */
    public function showBySlug(Request $request, string $slug): JsonResponse
    {
        $query = NewsCategory::query()->where('slug', $slug);

        // Include relationships
        if ($request->boolean('with_children')) {
            $query->with('children');
        }

        if ($request->boolean('with_parent')) {
            $query->with('parent');
        }

        if ($request->boolean('with_descendants')) {
            $query->with('descendants');
        }

        if ($request->boolean('with_articles')) {
            $query->with(['articles' => function ($q) {
                $q->published()->orderBy('published_at', 'desc');
            }]);
        }

        if ($request->boolean('with_articles_count')) {
            $query->withCount('articles');
        }

        $category = $query->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'News category not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param UpdateNewsCategoryRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateNewsCategoryRequest $request, int $id): JsonResponse
    {
        $category = NewsCategory::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'News category not found',
            ], 404);
        }

        try {
            $category->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'News category updated successfully',
                'data' => $category->load(['parent', 'children']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update news category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $category = NewsCategory::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'News category not found',
            ], 404);
        }

        try {
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'News category deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete news category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restore the specified soft deleted resource.
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        $category = NewsCategory::withTrashed()->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'News category not found',
            ], 404);
        }

        if (!$category->trashed()) {
            return response()->json([
                'success' => false,
                'message' => 'News category is not deleted',
            ], 400);
        }

        try {
            $category->restore();

            return response()->json([
                'success' => true,
                'message' => 'News category restored successfully',
                'data' => $category,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore news category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get category tree structure.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function tree(Request $request): JsonResponse
    {
        $query = NewsCategory::query()
            ->whereNull('parent_id')
            ->with('descendants');

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $query->orderBy('sort_order');

        $categories = $query->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}
