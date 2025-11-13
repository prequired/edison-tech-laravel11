<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check if two-factor authentication verification is required.
 *
 * @package App\Http\Middleware
 */
class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user has 2FA enabled
        if ($user->twoFactorAuthentication?->enabled) {
            // Check if the session has the 2FA verification flag
            if (!$request->session()->has('2fa:verified:' . $user->id)) {
                // User has 2FA enabled but hasn't verified yet
                Auth::logout();
                $request->session()->put('2fa:user:id', $user->id);

                return redirect()->route('2fa.verify')
                    ->with('message', 'Please enter your two-factor authentication code.');
            }
        }

        return $next($request);
    }
}
