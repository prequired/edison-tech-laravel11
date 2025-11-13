<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controller for handling user authentication (login/logout).
 *
 * @package App\Http\Controllers\Auth
 */
class LoginController extends Controller
{
    /**
     * Create a new LoginController instance.
     *
     * @param UserService $userService
     */
    public function __construct(
        private readonly UserService $userService,
    ) {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Show the login form.
     *
     * @return View
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * Rate limit: 5 attempts per minute
     *
     * @param LoginRequest $request
     * @return RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Attempt to authenticate
        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'These credentials do not match our records.',
                ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Check if user is active
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact support.',
            ]);
        }

        // Check if company is active
        if ($user->company && !$user->company->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Your company account has been deactivated. Please contact support.',
            ]);
        }

        // Update last login timestamp
        $this->userService->updateLastLogin($user);

        // Check if 2FA is enabled
        if ($user->twoFactorAuthentication?->enabled) {
            $request->session()->put('2fa:user:id', $user->id);
            Auth::logout();

            return redirect()->route('2fa.verify')
                ->with('message', 'Please enter your two-factor authentication code.');
        }

        return $this->redirectBasedOnRole($user);
    }

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return RedirectResponse
     */
    public function logout(\Illuminate\Http\Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('message', 'You have been logged out successfully.');
    }

    /**
     * Redirect user based on their role.
     *
     * @param \App\Models\User $user
     * @return RedirectResponse
     */
    private function redirectBasedOnRole(\App\Models\User $user): RedirectResponse
    {
        if ($user->isAdmin() || $user->isEmployee()) {
            return redirect()->route('admin.dashboard')
                ->with('message', 'Welcome back, ' . $user->name . '!');
        }

        if ($user->isClient()) {
            return redirect()->route('client.dashboard')
                ->with('message', 'Welcome back, ' . $user->name . '!');
        }

        // Default fallback
        return redirect()->route('home')
            ->with('message', 'Welcome back, ' . $user->name . '!');
    }
}
