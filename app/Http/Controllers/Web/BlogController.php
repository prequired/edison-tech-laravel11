<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Blog Controller
 *
 * Displays public blog pages.
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
        $posts = BlogPost::where('status', 'published')
            ->with(['category', 'author'])
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('blog_category_id', $request->category);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('title', 'like', "%{$request->search}%")
                    ->orWhere('excerpt', 'like', "%{$request->search}%")
                    ->orWhere('content', 'like', "%{$request->search}%");
            })
            ->when($request->filled('tag'), function ($query) use ($request) {
                $query->whereJsonContains('tags', $request->tag);
            })
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        // Get categories for sidebar
        $categories = BlogCategory::withCount(['posts' => function ($query) {
            $query->where('status', 'published');
        }])
            ->orderBy('name')
            ->get();

        // Get recent posts for sidebar
        $recentPosts = BlogPost::where('status', 'published')
            ->latest('published_at')
            ->take(5)
            ->get(['id', 'title', 'slug', 'published_at']);

        // Get all tags for sidebar
        $allTags = BlogPost::where('status', 'published')
            ->whereNotNull('tags')
            ->get()
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        return view('web.blog.index', compact('posts', 'categories', 'recentPosts', 'allTags'));
    }

    /**
     * Display the specified blog post.
     *
     * @param string $slug
     * @return View
     */
    public function show(string $slug): View
    {
        $post = BlogPost::where('slug', $slug)
            ->where('status', 'published')
            ->with(['category', 'author'])
            ->firstOrFail();

        // Get related posts from the same category
        $relatedPosts = BlogPost::where('status', 'published')
            ->where('blog_category_id', $post->blog_category_id)
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        // Get previous and next posts
        $previousPost = BlogPost::where('status', 'published')
            ->where('published_at', '<', $post->published_at)
            ->latest('published_at')
            ->first(['id', 'title', 'slug']);

        $nextPost = BlogPost::where('status', 'published')
            ->where('published_at', '>', $post->published_at)
            ->oldest('published_at')
            ->first(['id', 'title', 'slug']);

        return view('web.blog.show', compact('post', 'relatedPosts', 'previousPost', 'nextPost'));
    }

    /**
     * Display posts by category.
     *
     * @param string $slug
     * @return View
     */
    public function category(string $slug): View
    {
        $category = BlogCategory::where('slug', $slug)->firstOrFail();

        $posts = BlogPost::where('status', 'published')
            ->where('blog_category_id', $category->id)
            ->with(['category', 'author'])
            ->latest('published_at')
            ->paginate(12);

        // Get categories for sidebar
        $categories = BlogCategory::withCount(['posts' => function ($query) {
            $query->where('status', 'published');
        }])
            ->orderBy('name')
            ->get();

        return view('web.blog.category', compact('category', 'posts', 'categories'));
    }
}
