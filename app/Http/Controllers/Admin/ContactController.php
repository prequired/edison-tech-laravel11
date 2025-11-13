<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Contact Controller
 *
 * Manages contact form submissions in the admin panel.
 */
class ContactController extends Controller
{
    /**
     * Display a listing of contact submissions.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ContactSubmission::class);

        $submissions = ContactSubmission::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('subject', 'like', "%{$request->search}%")
                    ->orWhere('message', 'like', "%{$request->search}%");
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totalSubmissions = ContactSubmission::count();
        $unreadSubmissions = ContactSubmission::where('status', 'unread')->count();
        $repliedSubmissions = ContactSubmission::where('status', 'replied')->count();

        return view('admin.contacts.index', compact(
            'submissions',
            'totalSubmissions',
            'unreadSubmissions',
            'repliedSubmissions'
        ));
    }

    /**
     * Display the specified contact submission.
     *
     * @param ContactSubmission $contact
     * @return View
     */
    public function show(ContactSubmission $contact): View
    {
        $this->authorize('view', $contact);

        // Mark as read if currently unread
        if ($contact->status === 'unread') {
            $contact->update(['status' => 'read']);
        }

        return view('admin.contacts.show', compact('contact'));
    }

    /**
     * Update the status of the contact submission.
     *
     * @param Request $request
     * @param ContactSubmission $contact
     * @return RedirectResponse
     */
    public function updateStatus(Request $request, ContactSubmission $contact): RedirectResponse
    {
        $this->authorize('update', $contact);

        $validated = $request->validate([
            'status' => ['required', 'in:unread,read,replied,archived'],
            'notes' => ['nullable', 'string'],
        ]);

        $contact->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Contact submission status updated successfully.');
    }

    /**
     * Mark the contact submission as replied.
     *
     * @param ContactSubmission $contact
     * @return RedirectResponse
     */
    public function markAsReplied(ContactSubmission $contact): RedirectResponse
    {
        $this->authorize('update', $contact);

        $contact->update(['status' => 'replied']);

        return redirect()
            ->back()
            ->with('success', 'Contact marked as replied.');
    }

    /**
     * Archive the contact submission.
     *
     * @param ContactSubmission $contact
     * @return RedirectResponse
     */
    public function archive(ContactSubmission $contact): RedirectResponse
    {
        $this->authorize('update', $contact);

        $contact->update(['status' => 'archived']);

        return redirect()
            ->back()
            ->with('success', 'Contact archived successfully.');
    }

    /**
     * Remove the specified contact submission from storage.
     *
     * @param ContactSubmission $contact
     * @return RedirectResponse
     */
    public function destroy(ContactSubmission $contact): RedirectResponse
    {
        $this->authorize('delete', $contact);

        $contact->delete();

        return redirect()
            ->route('admin.contacts.index')
            ->with('success', 'Contact submission deleted successfully.');
    }
}
