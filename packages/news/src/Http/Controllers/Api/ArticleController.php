<?php

namespace Vendor\News\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Vendor\News\Http\Requests\StoreArticleRequest;
use Vendor\News\Http\Requests\UpdateArticleRequest;
use Vendor\News\Models\Article;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Article::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        } else {
            // Default: only show published articles for public access
            if (!$request->boolean('all_status')) {
                $query->published();
            }
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filter by author
        if ($request->has('author_id')) {
            $query->where('author_id', $request->input('author_id'));
        }

        // Filter by featured
        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        // Filter by tag
        if ($request->has('tag')) {
            $query->byTag($request->input('tag'));
        }

        // Include relationships
        if ($request->boolean('with_category')) {
            $query->with('category');
        }

        if ($request->boolean('with_author')) {
            $query->with('author:id,name,email');
        }

        // Search by title, excerpt, or content
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->search($search);
        }

        // Sort
        $sortBy = $request->input('sort_by', 'published_at');
        $sortOrder = $request->input('sort_order', 'desc');

        if ($sortBy === 'most_viewed') {
            $query->mostViewed();
        } elseif ($sortBy === 'recent') {
            $query->recent();
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = $request->input('per_page', 15);

        if ($request->has('per_page') && $request->input('per_page') === 'all') {
            $articles = $query->get();
            return response()->json([
                'success' => true,
                'data' => $articles,
            ]);
        }

        $articles = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $articles->items(),
            'meta' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param StoreArticleRequest $request
     * @return JsonResponse
     */
    public function store(StoreArticleRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Set author_id if authenticated
            $user = auth()->user();
            if ($user && !isset($data['author_id'])) {
                $data['author_id'] = $user->id;
            }

            $article = Article::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Article created successfully',
                'data' => $article->load(['category', 'author']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create article',
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
        $query = Article::query();

        // Include relationships
        if ($request->boolean('with_category')) {
            $query->with('category');
        }

        if ($request->boolean('with_author')) {
            $query->with('author:id,name,email');
        }

        $article = $query->find($id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found',
            ], 404);
        }

        // Increment view count if requested
        if ($request->boolean('increment_view')) {
            $article->incrementViewCount();
        }

        return response()->json([
            'success' => true,
            'data' => $article,
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
        $query = Article::query()->where('slug', $slug);

        // Include relationships
        if ($request->boolean('with_category')) {
            $query->with('category');
        }

        if ($request->boolean('with_author')) {
            $query->with('author:id,name,email');
        }

        $article = $query->first();

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found',
            ], 404);
        }

        // Increment view count if requested
        if ($request->boolean('increment_view')) {
            $article->incrementViewCount();
        }

        return response()->json([
            'success' => true,
            'data' => $article,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param UpdateArticleRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateArticleRequest $request, int $id): JsonResponse
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found',
            ], 404);
        }

        try {
            $article->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Article updated successfully',
                'data' => $article->load(['category', 'author']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update article',
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
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found',
            ], 404);
        }

        try {
            $article->delete();

            return response()->json([
                'success' => true,
                'message' => 'Article deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete article',
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
        $article = Article::withTrashed()->find($id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found',
            ], 404);
        }

        if (!$article->trashed()) {
            return response()->json([
                'success' => false,
                'message' => 'Article is not deleted',
            ], 400);
        }

        try {
            $article->restore();

            return response()->json([
                'success' => true,
                'message' => 'Article restored successfully',
                'data' => $article,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore article',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change article status.
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function changeStatus(Request $request, int $id): JsonResponse
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found',
            ], 404);
        }

        $request->validate([
            'status' => 'required|in:draft,published,archived',
        ]);

        try {
            $article->status = $request->input('status');

            // Set published_at when publishing
            if ($request->input('status') === Article::STATUS_PUBLISHED && !$article->published_at) {
                $article->published_at = now();
            }

            $article->save();

            return response()->json([
                'success' => true,
                'message' => 'Article status updated successfully',
                'data' => $article,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update article status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get featured articles.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 5);

        $articles = Article::query()
            ->published()
            ->featured()
            ->with(['category', 'author:id,name,email'])
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $articles,
        ]);
    }

    /**
     * Get most viewed articles.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function mostViewed(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 5);

        $articles = Article::query()
            ->published()
            ->mostViewed()
            ->with(['category', 'author:id,name,email'])
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $articles,
        ]);
    }

    /**
     * Get recent articles.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function recent(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 5);

        $articles = Article::query()
            ->published()
            ->recent()
            ->with(['category', 'author:id,name,email'])
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $articles,
        ]);
    }

    /**
     * Get related articles based on category and tags.
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function related(Request $request, int $id): JsonResponse
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found',
            ], 404);
        }

        $limit = $request->input('limit', 5);

        $query = Article::query()
            ->published()
            ->where('id', '!=', $id);

        // Prioritize articles from the same category
        if ($article->category_id) {
            $query->where('category_id', $article->category_id);
        }

        $related = $query
            ->with(['category', 'author:id,name,email'])
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $related,
        ]);
    }
}
