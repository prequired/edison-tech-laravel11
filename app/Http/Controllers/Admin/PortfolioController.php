<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioItem;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portfolio Controller
 *
 * Manages portfolio items displayed on the public website.
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
        $this->authorize('viewAny', PortfolioItem::class);

        $portfolioItems = PortfolioItem::with('project')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('title', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category', $request->category);
            })
            ->when($request->filled('is_featured'), function ($query) use ($request) {
                $query->where('is_featured', $request->boolean('is_featured'));
            })
            ->orderBy('display_order')
            ->paginate(15)
            ->withQueryString();

        return view('admin.portfolio.index', compact('portfolioItems'));
    }

    /**
     * Show the form for creating a new portfolio item.
     *
     * @return View
     */
    public function create(): View
    {
        $this->authorize('create', PortfolioItem::class);

        $projects = Project::orderBy('name')->get(['id', 'name']);

        return view('admin.portfolio.create', compact('projects'));
    }

    /**
     * Store a newly created portfolio item in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', PortfolioItem::class);

        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:portfolio_items,slug'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['required', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'project_url' => ['nullable', 'url', 'max:500'],
            'featured_image' => ['nullable', 'image', 'max:2048'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
            'technologies' => ['nullable', 'array'],
            'technologies.*' => ['string', 'max:100'],
            'completion_date' => ['nullable', 'date'],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'display_order' => ['integer', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('portfolio', 'public');
        }

        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('portfolio', 'public');
            }
            $validated['images'] = $imagePaths;
        }

        $portfolioItem = PortfolioItem::create($validated);

        return redirect()
            ->route('admin.portfolio.show', $portfolioItem)
            ->with('success', 'Portfolio item created successfully.');
    }

    /**
     * Display the specified portfolio item.
     *
     * @param PortfolioItem $portfolioItem
     * @return View
     */
    public function show(PortfolioItem $portfolioItem): View
    {
        $this->authorize('view', $portfolioItem);

        $portfolioItem->load('project');

        return view('admin.portfolio.show', compact('portfolioItem'));
    }

    /**
     * Show the form for editing the specified portfolio item.
     *
     * @param PortfolioItem $portfolioItem
     * @return View
     */
    public function edit(PortfolioItem $portfolioItem): View
    {
        $this->authorize('update', $portfolioItem);

        $projects = Project::orderBy('name')->get(['id', 'name']);

        return view('admin.portfolio.edit', compact('portfolioItem', 'projects'));
    }

    /**
     * Update the specified portfolio item in storage.
     *
     * @param Request $request
     * @param PortfolioItem $portfolioItem
     * @return RedirectResponse
     */
    public function update(Request $request, PortfolioItem $portfolioItem): RedirectResponse
    {
        $this->authorize('update', $portfolioItem);

        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:portfolio_items,slug,' . $portfolioItem->id],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['required', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'project_url' => ['nullable', 'url', 'max:500'],
            'featured_image' => ['nullable', 'image', 'max:2048'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
            'technologies' => ['nullable', 'array'],
            'technologies.*' => ['string', 'max:100'],
            'completion_date' => ['nullable', 'date'],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'display_order' => ['integer', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($portfolioItem->featured_image && \Storage::disk('public')->exists($portfolioItem->featured_image)) {
                \Storage::disk('public')->delete($portfolioItem->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('portfolio', 'public');
        }

        if ($request->hasFile('images')) {
            // Delete old images if exists
            if ($portfolioItem->images) {
                foreach ($portfolioItem->images as $oldImage) {
                    if (\Storage::disk('public')->exists($oldImage)) {
                        \Storage::disk('public')->delete($oldImage);
                    }
                }
            }

            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('portfolio', 'public');
            }
            $validated['images'] = $imagePaths;
        }

        $portfolioItem->update($validated);

        return redirect()
            ->route('admin.portfolio.show', $portfolioItem)
            ->with('success', 'Portfolio item updated successfully.');
    }

    /**
     * Remove the specified portfolio item from storage.
     *
     * @param PortfolioItem $portfolioItem
     * @return RedirectResponse
     */
    public function destroy(PortfolioItem $portfolioItem): RedirectResponse
    {
        $this->authorize('delete', $portfolioItem);

        // Delete associated images
        if ($portfolioItem->featured_image && \Storage::disk('public')->exists($portfolioItem->featured_image)) {
            \Storage::disk('public')->delete($portfolioItem->featured_image);
        }

        if ($portfolioItem->images) {
            foreach ($portfolioItem->images as $image) {
                if (\Storage::disk('public')->exists($image)) {
                    \Storage::disk('public')->delete($image);
                }
            }
        }

        $portfolioItem->delete();

        return redirect()
            ->route('admin.portfolio.index')
            ->with('success', 'Portfolio item deleted successfully.');
    }
}
