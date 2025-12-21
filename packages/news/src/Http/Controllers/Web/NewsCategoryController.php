<?php

namespace Vendor\News\Http\Controllers\Web;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Vendor\News\Models\NewsCategory;
use Vendor\News\Http\Requests\StoreNewsCategoryRequest;
use Vendor\News\Http\Requests\UpdateNewsCategoryRequest;

class NewsCategoryController extends Controller
{
    /**
     * Display a listing of categories (admin view).
     */
    public function index(Request $request)
    {
        $query = NewsCategory::query()
            ->with('parent')
            ->withCount('articles');

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->orderBy('sort_order')->orderBy('name')->paginate(20);

        return view('news::admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $parentCategories = NewsCategory::whereNull('parent_id')->orderBy('name')->get();
        return view('news::admin.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreNewsCategoryRequest $request)
    {
        $category = NewsCategory::create($request->validated());

        return redirect()
            ->route('admin.news-categories.show', $category)
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified category.
     */
    public function show(NewsCategory $category)
    {
        $category->load(['articles', 'parent', 'children']);
        return view('news::admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(NewsCategory $category)
    {
        $parentCategories = NewsCategory::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('news::admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified category.
     */
    public function update(UpdateNewsCategoryRequest $request, NewsCategory $category)
    {
        $category->update($request->validated());

        return redirect()
            ->route('admin.news-categories.show', $category)
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(NewsCategory $category)
    {
        $category->delete();

        return redirect()
            ->route('admin.news-categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
