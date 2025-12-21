<?php

namespace Vendor\News\Http\Controllers\Web;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Vendor\News\Models\Article;
use Vendor\News\Models\NewsCategory;

class BlogController extends Controller
{
    /**
     * Display blog listing (public view).
     */
    public function index(Request $request)
    {
        $query = Article::published()->with(['category', 'author']);

        // Filter by category
        if ($request->has('category')) {
            $category = NewsCategory::where('slug', $request->input('category'))->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->search($search);
        }

        // Filter by tag
        if ($request->has('tag')) {
            $query->byTag($request->input('tag'));
        }

        // Sort
        $sortBy = $request->input('sort', 'published_at');

        if ($sortBy === 'most_viewed') {
            $query->mostViewed();
        } elseif ($sortBy === 'recent') {
            $query->recent();
        } else {
            $query->orderBy('published_at', 'desc');
        }

        $articles = $query->paginate(12);
        $categories = NewsCategory::active()->orderBy('sort_order')->orderBy('name')->get();
        $featuredArticles = Article::published()->featured()->take(3)->get();

        return view('news::blog.index', compact('articles', 'categories', 'featuredArticles'));
    }

    /**
     * Display article detail page.
     */
    public function show(string $slug)
    {
        $article = Article::published()
            ->where('slug', $slug)
            ->with(['category', 'author'])
            ->firstOrFail();

        // Increment view count
        $article->incrementViewCount();

        // Get related articles
        $relatedArticles = Article::published()
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->take(4)
            ->get();

        return view('news::blog.show', compact('article', 'relatedArticles'));
    }

    /**
     * Display articles by category.
     */
    public function category(string $slug)
    {
        $category = NewsCategory::active()
            ->where('slug', $slug)
            ->with('children')
            ->firstOrFail();

        $query = Article::published()->where('category_id', $category->id);

        $articles = $query->orderBy('published_at', 'desc')->paginate(12);

        return view('news::blog.category', compact('category', 'articles'));
    }

    /**
     * Display featured articles.
     */
    public function featured()
    {
        $articles = Article::published()->featured()->paginate(12);
        $categories = NewsCategory::active()->orderBy('sort_order')->get();

        return view('news::blog.featured', compact('articles', 'categories'));
    }

    /**
     * Display most viewed articles.
     */
    public function mostViewed()
    {
        $articles = Article::published()->mostViewed()->paginate(12);
        $categories = NewsCategory::active()->orderBy('sort_order')->get();

        return view('news::blog.most-viewed', compact('articles', 'categories'));
    }
}
