<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check if the authenticated user's company is active.
 *
 * @package App\Http\Middleware
 */
class CompanyActiveMiddleware
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
        if (!$request->user()) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Please log in to continue.']);
        }

        $user = $request->user();

        // Check if user has a company
        if (!$user->company) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['error' => 'Your account is not associated with a company. Please contact support.']);
        }

        // Check if company is active
        if (!$user->company->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['error' => 'Your company account has been deactivated. Please contact support.']);
        }

        return $next($request);
    }
}
