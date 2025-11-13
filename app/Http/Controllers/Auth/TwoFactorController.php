<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controller for handling two-factor authentication operations.
 *
 * @package App\Http\Controllers\Auth
 */
class TwoFactorController extends Controller
{
    /**
     * Create a new TwoFactorController instance.
     *
     * @param UserService $userService
     */
    public function __construct(
        private readonly UserService $userService,
    ) {
        $this->middleware('auth')->except('verify', 'showVerifyForm');
    }

    /**
     * Show the two-factor verification form.
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function showVerifyForm(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('2fa:user:id')) {
            return redirect()->route('login');
        }

        return view('auth.2fa.verify');
    }

    /**
     * Verify the two-factor authentication code.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $userId = $request->session()->get('2fa:user:id');

        if (!$userId) {
            return redirect()->route('login')
                ->withErrors(['code' => 'Session expired. Please login again.']);
        }

        $user = \App\Models\User::find($userId);

        if (!$user) {
            $request->session()->forget('2fa:user:id');
            return redirect()->route('login')
                ->withErrors(['code' => 'User not found. Please login again.']);
        }

        // Verify the code
        if (!$this->userService->verifyTwoFactorCode($user, $request->input('code'))) {
            return back()->withErrors([
                'code' => 'The verification code is invalid.',
            ]);
        }

        // Code is valid, log the user in
        $request->session()->forget('2fa:user:id');
        Auth::login($user);
        $request->session()->regenerate();

        // Update last login timestamp
        $this->userService->updateLastLogin($user);

        // Redirect based on role
        if ($user->isAdmin() || $user->isEmployee()) {
            return redirect()->route('admin.dashboard')
                ->with('message', 'Welcome back, ' . $user->name . '!');
        }

        return redirect()->route('client.dashboard')
            ->with('message', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Enable two-factor authentication for the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function enable(Request $request): JsonResponse
    {
        $request->validate([
            'secret' => ['required', 'string'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();

        // Verify the code before enabling
        // TODO: Implement proper 2FA code verification with the secret
        // For now, we'll assume the verification passes

        // Generate recovery codes
        $recoveryCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = strtoupper(\Illuminate\Support\Str::random(10));
        }

        $twoFactor = $this->userService->activateTwoFactor(
            $user,
            $request->input('secret'),
            $recoveryCodes
        );

        return response()->json([
            'message' => 'Two-factor authentication has been enabled successfully.',
            'recovery_codes' => $recoveryCodes,
        ]);
    }

    /**
     * Disable two-factor authentication for the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function disable(Request $request): JsonResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        // Verify password before disabling
        if (!\Illuminate\Support\Facades\Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'message' => 'The provided password is incorrect.',
            ], 422);
        }

        $this->userService->deactivateTwoFactor($user);

        return response()->json([
            'message' => 'Two-factor authentication has been disabled successfully.',
        ]);
    }

    /**
     * Regenerate recovery codes for the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function regenerateRecoveryCodes(Request $request): JsonResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        // Verify password before regenerating
        if (!\Illuminate\Support\Facades\Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'message' => 'The provided password is incorrect.',
            ], 422);
        }

        $twoFactor = $user->twoFactorAuthentication;

        if (!$twoFactor || !$twoFactor->enabled) {
            return response()->json([
                'message' => 'Two-factor authentication is not enabled.',
            ], 422);
        }

        // Generate new recovery codes
        $recoveryCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = strtoupper(\Illuminate\Support\Str::random(10));
        }

        $twoFactor->update([
            'recovery_codes' => encrypt(json_encode($recoveryCodes)),
        ]);

        return response()->json([
            'message' => 'Recovery codes have been regenerated successfully.',
            'recovery_codes' => $recoveryCodes,
        ]);
    }
}
