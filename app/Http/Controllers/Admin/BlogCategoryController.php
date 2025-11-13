<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Blog Category Controller
 *
 * Manages blog categories in the admin panel.
 */
class BlogCategoryController extends Controller
{
    /**
     * Display a listing of blog categories.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', BlogCategory::class);

        $categories = BlogCategory::withCount('posts')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.blog-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new blog category.
     *
     * @return View
     */
    public function create(): View
    {
        $this->authorize('create', BlogCategory::class);

        return view('admin.blog-categories.create');
    }

    /**
     * Store a newly created blog category in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', BlogCategory::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:blog_categories,slug'],
            'description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        $category = BlogCategory::create($validated);

        return redirect()
            ->route('admin.blog-categories.show', $category)
            ->with('success', 'Blog category created successfully.');
    }

    /**
     * Display the specified blog category.
     *
     * @param BlogCategory $blogCategory
     * @return View
     */
    public function show(BlogCategory $blogCategory): View
    {
        $this->authorize('view', $blogCategory);

        $blogCategory->loadCount('posts');

        // Get recent posts in this category
        $recentPosts = $blogCategory->posts()
            ->with('author')
            ->latest('published_at')
            ->take(10)
            ->get();

        return view('admin.blog-categories.show', compact('blogCategory', 'recentPosts'));
    }

    /**
     * Show the form for editing the specified blog category.
     *
     * @param BlogCategory $blogCategory
     * @return View
     */
    public function edit(BlogCategory $blogCategory): View
    {
        $this->authorize('update', $blogCategory);

        return view('admin.blog-categories.edit', compact('blogCategory'));
    }

    /**
     * Update the specified blog category in storage.
     *
     * @param Request $request
     * @param BlogCategory $blogCategory
     * @return RedirectResponse
     */
    public function update(Request $request, BlogCategory $blogCategory): RedirectResponse
    {
        $this->authorize('update', $blogCategory);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:blog_categories,slug,' . $blogCategory->id],
            'description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        $blogCategory->update($validated);

        return redirect()
            ->route('admin.blog-categories.show', $blogCategory)
            ->with('success', 'Blog category updated successfully.');
    }

    /**
     * Remove the specified blog category from storage.
     *
     * @param BlogCategory $blogCategory
     * @return RedirectResponse
     */
    public function destroy(BlogCategory $blogCategory): RedirectResponse
    {
        $this->authorize('delete', $blogCategory);

        // Check if category has posts
        if ($blogCategory->posts()->count() > 0) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete category with existing blog posts. Please reassign or delete posts first.');
        }

        $blogCategory->delete();

        return redirect()
            ->route('admin.blog-categories.index')
            ->with('success', 'Blog category deleted successfully.');
    }
}
