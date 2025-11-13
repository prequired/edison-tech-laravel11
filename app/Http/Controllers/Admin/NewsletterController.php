<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Newsletter Controller
 *
 * Manages newsletter subscribers in the admin panel.
 */
class NewsletterController extends Controller
{
    /**
     * Display a listing of newsletter subscribers.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', NewsletterSubscriber::class);

        $subscribers = NewsletterSubscriber::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('email', 'like', "%{$request->search}%")
                    ->orWhere('name', 'like', "%{$request->search}%");
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totalSubscribers = NewsletterSubscriber::count();
        $activeSubscribers = NewsletterSubscriber::where('status', 'subscribed')->count();
        $unsubscribedCount = NewsletterSubscriber::where('status', 'unsubscribed')->count();

        return view('admin.newsletter.index', compact(
            'subscribers',
            'totalSubscribers',
            'activeSubscribers',
            'unsubscribedCount'
        ));
    }

    /**
     * Display the specified newsletter subscriber.
     *
     * @param NewsletterSubscriber $newsletter
     * @return View
     */
    public function show(NewsletterSubscriber $newsletter): View
    {
        $this->authorize('view', $newsletter);

        return view('admin.newsletter.show', compact('newsletter'));
    }

    /**
     * Store a newly created newsletter subscriber in storage (manual add).
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', NewsletterSubscriber::class);

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:newsletter_subscribers,email'],
            'name' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:subscribed,unsubscribed'],
        ]);

        $validated['verified_at'] = now();
        $validated['ip_address'] = $request->ip();

        $subscriber = NewsletterSubscriber::create($validated);

        return redirect()
            ->route('admin.newsletter.show', $subscriber)
            ->with('success', 'Newsletter subscriber added successfully.');
    }

    /**
     * Update the specified newsletter subscriber in storage.
     *
     * @param Request $request
     * @param NewsletterSubscriber $newsletter
     * @return RedirectResponse
     */
    public function update(Request $request, NewsletterSubscriber $newsletter): RedirectResponse
    {
        $this->authorize('update', $newsletter);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:subscribed,unsubscribed'],
        ]);

        if ($validated['status'] === 'unsubscribed' && $newsletter->status !== 'unsubscribed') {
            $validated['unsubscribed_at'] = now();
        } elseif ($validated['status'] === 'subscribed' && $newsletter->status === 'unsubscribed') {
            $validated['unsubscribed_at'] = null;
        }

        $newsletter->update($validated);

        return redirect()
            ->route('admin.newsletter.show', $newsletter)
            ->with('success', 'Newsletter subscriber updated successfully.');
    }

    /**
     * Remove the specified newsletter subscriber from storage.
     *
     * @param NewsletterSubscriber $newsletter
     * @return RedirectResponse
     */
    public function destroy(NewsletterSubscriber $newsletter): RedirectResponse
    {
        $this->authorize('delete', $newsletter);

        $newsletter->delete();

        return redirect()
            ->route('admin.newsletter.index')
            ->with('success', 'Newsletter subscriber deleted successfully.');
    }

    /**
     * Export all active subscribers to CSV.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->authorize('viewAny', NewsletterSubscriber::class);

        $subscribers = NewsletterSubscriber::where('status', 'subscribed')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="newsletter-subscribers-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($subscribers) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, ['Email', 'Name', 'Subscribed Date', 'Verified']);

            // Add data rows
            foreach ($subscribers as $subscriber) {
                fputcsv($file, [
                    $subscriber->email,
                    $subscriber->name,
                    $subscriber->created_at->format('Y-m-d H:i:s'),
                    $subscriber->verified_at ? 'Yes' : 'No',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
