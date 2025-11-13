<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Blog Controller
 *
 * Manages blog posts in the admin panel.
 */
class BlogController extends Controller
{
    /**
     * Display a listing of blog posts.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', BlogPost::class);

        $posts = BlogPost::with(['category', 'author'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('title', 'like', "%{$request->search}%")
                    ->orWhere('content', 'like', "%{$request->search}%");
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('blog_category_id', $request->category_id);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('is_featured'), function ($query) use ($request) {
                $query->where('is_featured', $request->boolean('is_featured'));
            })
            ->latest('published_at')
            ->paginate(15)
            ->withQueryString();

        $categories = BlogCategory::orderBy('name')->get(['id', 'name']);

        return view('admin.blog.index', compact('posts', 'categories'));
    }

    /**
     * Show the form for creating a new blog post.
     *
     * @return View
     */
    public function create(): View
    {
        $this->authorize('create', BlogPost::class);

        $categories = BlogCategory::orderBy('name')->get(['id', 'name']);

        return view('admin.blog.create', compact('categories'));
    }

    /**
     * Store a newly created blog post in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', BlogPost::class);

        $validated = $request->validate([
            'blog_category_id' => ['required', 'exists:blog_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:blog_posts,slug'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'in:draft,published,scheduled'],
            'is_featured' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['author_id'] = Auth::id();

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        // Set published_at to now if status is published and no date is set
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $post = BlogPost::create($validated);

        return redirect()
            ->route('admin.blog.show', $post)
            ->with('success', 'Blog post created successfully.');
    }

    /**
     * Display the specified blog post.
     *
     * @param BlogPost $blog
     * @return View
     */
    public function show(BlogPost $blog): View
    {
        $this->authorize('view', $blog);

        $blog->load(['category', 'author']);

        return view('admin.blog.show', compact('blog'));
    }

    /**
     * Show the form for editing the specified blog post.
     *
     * @param BlogPost $blog
     * @return View
     */
    public function edit(BlogPost $blog): View
    {
        $this->authorize('update', $blog);

        $categories = BlogCategory::orderBy('name')->get(['id', 'name']);

        return view('admin.blog.edit', compact('blog', 'categories'));
    }

    /**
     * Update the specified blog post in storage.
     *
     * @param Request $request
     * @param BlogPost $blog
     * @return RedirectResponse
     */
    public function update(Request $request, BlogPost $blog): RedirectResponse
    {
        $this->authorize('update', $blog);

        $validated = $request->validate([
            'blog_category_id' => ['required', 'exists:blog_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:blog_posts,slug,' . $blog->id],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'in:draft,published,scheduled'],
            'is_featured' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($blog->featured_image && \Storage::disk('public')->exists($blog->featured_image)) {
                \Storage::disk('public')->delete($blog->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        // Set published_at to now if status changed to published and no date is set
        if ($validated['status'] === 'published' && empty($validated['published_at']) && $blog->status !== 'published') {
            $validated['published_at'] = now();
        }

        $blog->update($validated);

        return redirect()
            ->route('admin.blog.show', $blog)
            ->with('success', 'Blog post updated successfully.');
    }

    /**
     * Remove the specified blog post from storage.
     *
     * @param BlogPost $blog
     * @return RedirectResponse
     */
    public function destroy(BlogPost $blog): RedirectResponse
    {
        $this->authorize('delete', $blog);

        // Delete associated image
        if ($blog->featured_image && \Storage::disk('public')->exists($blog->featured_image)) {
            \Storage::disk('public')->delete($blog->featured_image);
        }

        $blog->delete();

        return redirect()
            ->route('admin.blog.index')
            ->with('success', 'Blog post deleted successfully.');
    }
}
