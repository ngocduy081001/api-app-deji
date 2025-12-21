<?php

namespace Vendor\News\Http\Controllers\Web;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Vendor\News\Models\Article;
use Vendor\News\Models\NewsCategory;
use Vendor\News\Http\Requests\StoreArticleRequest;
use Vendor\News\Http\Requests\UpdateArticleRequest;

class ArticleController extends Controller
{
    /**
     * Display a listing of articles (admin view).
     */
    public function index(Request $request)
    {
        $query = Article::with(['category', 'author']);

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->search($search);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $articles = $query->orderBy('published_at', 'desc')->paginate(20);
        $categories = NewsCategory::orderBy('name')->get();

        return view('news::admin.articles.index', compact('articles', 'categories'));
    }

    /**
     * Show the form for creating a new article.
     */
    public function create()
    {
        $categories = NewsCategory::orderBy('name')->get();
        return view('news::admin.articles.create', compact('categories'));
    }

    /**
     * Store a newly created article.
     */
    public function store(StoreArticleRequest $request)
    {
        $data = $request->validated();
        $data['author_id'] = auth()->id();

        // Handle tags - if empty array, set to null
        if (isset($data['tags']) && is_array($data['tags']) && empty($data['tags'])) {
            $data['tags'] = null;
        }

        // Handle SEO fields - store in meta_data
        $metaData = $data['meta_data'] ?? [];
        if (isset($data['meta_title'])) {
            $metaData['meta_title'] = $data['meta_title'];
        }
        if (isset($data['meta_description'])) {
            $metaData['meta_description'] = $data['meta_description'];
        }
        if (isset($data['meta_keywords'])) {
            $metaData['meta_keywords'] = $data['meta_keywords'];
        }
        $data['meta_data'] = !empty($metaData) ? $metaData : null;

        // Remove SEO fields from data as they're stored in meta_data
        unset($data['meta_title'], $data['meta_description'], $data['meta_keywords']);

        $article = Article::create($data);

        return redirect()
            ->route('admin.articles.edit', ['article' => $article->id])
            ->with('success', 'Bài viết đã được tạo thành công.');
    }

    /**
     * Display the specified article.
     */
    public function show(Article $article)
    {
        $article->load(['category', 'author']);
        return view('news::admin.articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified article.
     */
    public function edit(Article $article)
    {
        $categories = NewsCategory::orderBy('name')->get();

        // Extract SEO data from meta_data for the form
        $metaData = $article->meta_data ?? [];
        $article->meta_title = $metaData['meta_title'] ?? null;
        $article->meta_description = $metaData['meta_description'] ?? null;
        $article->meta_keywords = $metaData['meta_keywords'] ?? null;

        return view('news::admin.articles.edit', compact('article', 'categories'));
    }

    /**
     * Update the specified article.
     */
    public function update(UpdateArticleRequest $request, Article $article)
    {
        $data = $request->validated();

        // Handle tags - if empty array, set to null
        if (isset($data['tags']) && is_array($data['tags']) && empty($data['tags'])) {
            $data['tags'] = null;
        }

        // Handle SEO fields - store in meta_data
        $metaData = $article->meta_data ?? [];
        if (isset($data['meta_title'])) {
            $metaData['meta_title'] = $data['meta_title'];
        }
        if (isset($data['meta_description'])) {
            $metaData['meta_description'] = $data['meta_description'];
        }
        if (isset($data['meta_keywords'])) {
            $metaData['meta_keywords'] = $data['meta_keywords'];
        }
        $data['meta_data'] = !empty($metaData) ? $metaData : null;

        // Remove SEO fields from data as they're stored in meta_data
        unset($data['meta_title'], $data['meta_description'], $data['meta_keywords']);

        $article->update($data);

        return redirect()
            ->route('admin.articles.edit', $article)
            ->with('success', 'Bài viết đã được cập nhật thành công.');
    }

    /**
     * Remove the specified article (soft delete).
     */
    public function destroy(Article $article)
    {
        $article->delete();

        return redirect()
            ->route('admin.articles.index')
                ->with('success', 'Bài viết đã được xóa thành công.');
    }
}
