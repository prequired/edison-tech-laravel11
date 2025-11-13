<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Models\Project;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Testimonial Controller
 *
 * Manages client testimonials displayed on the public website.
 */
class TestimonialController extends Controller
{
    /**
     * Display a listing of testimonials.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Testimonial::class);

        $testimonials = Testimonial::with(['company', 'project'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('client_name', 'like', "%{$request->search}%")
                    ->orWhere('content', 'like', "%{$request->search}%");
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('is_featured'), function ($query) use ($request) {
                $query->where('is_featured', $request->boolean('is_featured'));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.testimonials.index', compact('testimonials'));
    }

    /**
     * Show the form for creating a new testimonial.
     *
     * @return View
     */
    public function create(): View
    {
        $this->authorize('create', Testimonial::class);

        $companies = Company::orderBy('name')->get(['id', 'name']);
        $projects = Project::orderBy('name')->get(['id', 'name']);

        return view('admin.testimonials.create', compact('companies', 'projects'));
    }

    /**
     * Store a newly created testimonial in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Testimonial::class);

        $validated = $request->validate([
            'company_id' => ['nullable', 'exists:companies,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'client_name' => ['required', 'string', 'max:255'],
            'client_position' => ['nullable', 'string', 'max:255'],
            'client_company' => ['nullable', 'string', 'max:255'],
            'client_image' => ['nullable', 'image', 'max:2048'],
            'content' => ['required', 'string'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'status' => ['required', 'in:pending,approved,rejected'],
            'is_featured' => ['boolean'],
            'display_order' => ['integer', 'min:0'],
        ]);

        if ($request->hasFile('client_image')) {
            $validated['client_image'] = $request->file('client_image')->store('testimonials', 'public');
        }

        $testimonial = Testimonial::create($validated);

        return redirect()
            ->route('admin.testimonials.show', $testimonial)
            ->with('success', 'Testimonial created successfully.');
    }

    /**
     * Display the specified testimonial.
     *
     * @param Testimonial $testimonial
     * @return View
     */
    public function show(Testimonial $testimonial): View
    {
        $this->authorize('view', $testimonial);

        $testimonial->load(['company', 'project']);

        return view('admin.testimonials.show', compact('testimonial'));
    }

    /**
     * Show the form for editing the specified testimonial.
     *
     * @param Testimonial $testimonial
     * @return View
     */
    public function edit(Testimonial $testimonial): View
    {
        $this->authorize('update', $testimonial);

        $companies = Company::orderBy('name')->get(['id', 'name']);
        $projects = Project::orderBy('name')->get(['id', 'name']);

        return view('admin.testimonials.edit', compact('testimonial', 'companies', 'projects'));
    }

    /**
     * Update the specified testimonial in storage.
     *
     * @param Request $request
     * @param Testimonial $testimonial
     * @return RedirectResponse
     */
    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $this->authorize('update', $testimonial);

        $validated = $request->validate([
            'company_id' => ['nullable', 'exists:companies,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'client_name' => ['required', 'string', 'max:255'],
            'client_position' => ['nullable', 'string', 'max:255'],
            'client_company' => ['nullable', 'string', 'max:255'],
            'client_image' => ['nullable', 'image', 'max:2048'],
            'content' => ['required', 'string'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'status' => ['required', 'in:pending,approved,rejected'],
            'is_featured' => ['boolean'],
            'display_order' => ['integer', 'min:0'],
        ]);

        if ($request->hasFile('client_image')) {
            // Delete old image if exists
            if ($testimonial->client_image && \Storage::disk('public')->exists($testimonial->client_image)) {
                \Storage::disk('public')->delete($testimonial->client_image);
            }
            $validated['client_image'] = $request->file('client_image')->store('testimonials', 'public');
        }

        $testimonial->update($validated);

        return redirect()
            ->route('admin.testimonials.show', $testimonial)
            ->with('success', 'Testimonial updated successfully.');
    }

    /**
     * Remove the specified testimonial from storage.
     *
     * @param Testimonial $testimonial
     * @return RedirectResponse
     */
    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $this->authorize('delete', $testimonial);

        // Delete associated image
        if ($testimonial->client_image && \Storage::disk('public')->exists($testimonial->client_image)) {
            \Storage::disk('public')->delete($testimonial->client_image);
        }

        $testimonial->delete();

        return redirect()
            ->route('admin.testimonials.index')
            ->with('success', 'Testimonial deleted successfully.');
    }

    /**
     * Approve the specified testimonial.
     *
     * @param Testimonial $testimonial
     * @return RedirectResponse
     */
    public function approve(Testimonial $testimonial): RedirectResponse
    {
        $this->authorize('update', $testimonial);

        $testimonial->update(['status' => 'approved']);

        return redirect()
            ->back()
            ->with('success', 'Testimonial approved successfully.');
    }

    /**
     * Reject the specified testimonial.
     *
     * @param Testimonial $testimonial
     * @return RedirectResponse
     */
    public function reject(Testimonial $testimonial): RedirectResponse
    {
        $this->authorize('update', $testimonial);

        $testimonial->update(['status' => 'rejected']);

        return redirect()
            ->back()
            ->with('success', 'Testimonial rejected.');
    }
}
