<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PortfolioItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portfolio Controller
 *
 * Displays public portfolio pages.
 */
class PortfolioController extends Controller
{
    /**
     * Display a listing of portfolio items.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $portfolioItems = PortfolioItem::where('is_published', true)
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category', $request->category);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('title', 'like', "%{$request->search}%")
                    ->orWhere('short_description', 'like', "%{$request->search}%");
            })
            ->orderBy('display_order')
            ->paginate(12)
            ->withQueryString();

        // Get all unique categories for filter
        $categories = PortfolioItem::where('is_published', true)
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        return view('web.portfolio.index', compact('portfolioItems', 'categories'));
    }

    /**
     * Display the specified portfolio item.
     *
     * @param string $slug
     * @return View
     */
    public function show(string $slug): View
    {
        $portfolioItem = PortfolioItem::where('slug', $slug)
            ->where('is_published', true)
            ->with('project')
            ->firstOrFail();

        // Get related portfolio items from the same category
        $relatedItems = PortfolioItem::where('is_published', true)
            ->where('category', $portfolioItem->category)
            ->where('id', '!=', $portfolioItem->id)
            ->inRandomOrder()
            ->take(3)
            ->get();

        return view('web.portfolio.show', compact('portfolioItem', 'relatedItems'));
    }
}
