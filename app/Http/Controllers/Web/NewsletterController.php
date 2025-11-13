<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Newsletter Controller
 *
 * Handles public newsletter subscriptions and unsubscriptions.
 */
class NewsletterController extends Controller
{
    /**
     * Subscribe to the newsletter.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function subscribe(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:newsletter_subscribers,email'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['status'] = 'subscribed';
        $validated['verification_token'] = Str::random(60);
        $validated['ip_address'] = $request->ip();

        $subscriber = NewsletterSubscriber::create($validated);

        // In a real application, you would send a verification email here
        // For now, we'll just mark it as verified
        $subscriber->update([
            'verified_at' => now(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Thank you for subscribing to our newsletter!');
    }

    /**
     * Verify email subscription.
     *
     * @param string $token
     * @return RedirectResponse
     */
    public function verify(string $token): RedirectResponse
    {
        $subscriber = NewsletterSubscriber::where('verification_token', $token)->first();

        if (!$subscriber) {
            return redirect()
                ->route('home')
                ->with('error', 'Invalid verification token.');
        }

        if ($subscriber->verified_at) {
            return redirect()
                ->route('home')
                ->with('info', 'Your email is already verified.');
        }

        $subscriber->update([
            'verified_at' => now(),
            'verification_token' => null,
        ]);

        return redirect()
            ->route('home')
            ->with('success', 'Your email has been verified successfully!');
    }

    /**
     * Unsubscribe from the newsletter.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function unsubscribe(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $subscriber = NewsletterSubscriber::where('email', $validated['email'])->first();

        if (!$subscriber) {
            return redirect()
                ->back()
                ->with('error', 'Email address not found in our subscriber list.');
        }

        if ($subscriber->status === 'unsubscribed') {
            return redirect()
                ->back()
                ->with('info', 'You are already unsubscribed from our newsletter.');
        }

        $subscriber->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);

        return redirect()
            ->route('home')
            ->with('success', 'You have been unsubscribed from our newsletter.');
    }

    /**
     * Display the unsubscribe page.
     *
     * @return \Illuminate\View\View
     */
    public function unsubscribePage(): \Illuminate\View\View
    {
        return view('web.newsletter.unsubscribe');
    }
}
