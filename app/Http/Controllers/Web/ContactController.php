<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Contact Controller
 *
 * Handles public contact form submissions.
 */
class ContactController extends Controller
{
    /**
     * Display the contact page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('web.contact');
    }

    /**
     * Store a contact form submission.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'preferred_contact_method' => ['nullable', 'in:email,phone'],
        ]);

        $validated['status'] = 'unread';
        $validated['ip_address'] = $request->ip();
        $validated['user_agent'] = $request->userAgent();

        ContactSubmission::create($validated);

        // In a real application, you might want to send an email notification
        // to the admin team here, or dispatch a job to handle it

        return redirect()
            ->back()
            ->with('success', 'Thank you for contacting us! We will get back to you soon.');
    }
}
