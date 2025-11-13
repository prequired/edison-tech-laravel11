<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\TwoFactorAuthentication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Client Security Controller
 *
 * Manages security settings including two-factor authentication.
 */
class SecurityController extends Controller
{
    /**
     * Display the security settings page.
     *
     * @return View
     */
    public function index(): View
    {
        $user = Auth::user();

        $twoFactor = TwoFactorAuthentication::where('user_id', $user->id)->first();

        return view('client.security.index', compact('user', 'twoFactor'));
    }

    /**
     * Enable two-factor authentication.
     *
     * @return View
     */
    public function enableTwoFactor(): View
    {
        $user = Auth::user();

        // Check if 2FA is already enabled
        $existing = TwoFactorAuthentication::where('user_id', $user->id)->first();

        if ($existing && $existing->is_enabled) {
            return redirect()
                ->route('client.security.index')
                ->with('info', 'Two-factor authentication is already enabled.');
        }

        // Generate a new secret key
        // In a real implementation, you would use a package like pragmarx/google2fa
        $secretKey = $this->generateSecretKey();

        // Store or update the 2FA record
        $twoFactor = TwoFactorAuthentication::updateOrCreate(
            ['user_id' => $user->id],
            [
                'secret_key' => $secretKey,
                'is_enabled' => false, // Will be enabled after verification
            ]
        );

        // Generate QR code data
        $qrCodeUrl = $this->getQRCodeUrl($user->email, $secretKey);

        return view('client.security.enable-2fa', compact('secretKey', 'qrCodeUrl', 'twoFactor'));
    }

    /**
     * Verify and activate two-factor authentication.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function verifyTwoFactor(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $twoFactor = TwoFactorAuthentication::where('user_id', $user->id)->first();

        if (!$twoFactor) {
            return redirect()
                ->route('client.security.index')
                ->with('error', 'Two-factor authentication setup not found.');
        }

        // Verify the code
        // In a real implementation, you would use Google2FA to verify
        $isValid = $this->verifyCode($twoFactor->secret_key, $validated['code']);

        if (!$isValid) {
            return redirect()
                ->back()
                ->withErrors(['code' => 'The verification code is invalid.']);
        }

        // Enable 2FA
        $twoFactor->update([
            'is_enabled' => true,
            'verified_at' => now(),
        ]);

        return redirect()
            ->route('client.security.index')
            ->with('success', 'Two-factor authentication has been enabled successfully.');
    }

    /**
     * Disable two-factor authentication.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function disableTwoFactor(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'password' => ['required', 'string'],
        ]);

        // Verify password
        if (!\Hash::check($validated['password'], $user->password)) {
            return redirect()
                ->back()
                ->withErrors(['password' => 'The password is incorrect.']);
        }

        $twoFactor = TwoFactorAuthentication::where('user_id', $user->id)->first();

        if ($twoFactor) {
            $twoFactor->delete();
        }

        return redirect()
            ->route('client.security.index')
            ->with('success', 'Two-factor authentication has been disabled.');
    }

    /**
     * Show recovery codes.
     *
     * @return View
     */
    public function recoveryCodes(): View
    {
        $user = Auth::user();

        $twoFactor = TwoFactorAuthentication::where('user_id', $user->id)->first();

        if (!$twoFactor || !$twoFactor->is_enabled) {
            return redirect()
                ->route('client.security.index')
                ->with('error', 'Two-factor authentication is not enabled.');
        }

        // Generate recovery codes if not already generated
        if (empty($twoFactor->recovery_codes)) {
            $recoveryCodes = $this->generateRecoveryCodes();
            $twoFactor->update([
                'recovery_codes' => $recoveryCodes,
            ]);
        }

        return view('client.security.recovery-codes', compact('twoFactor'));
    }

    /**
     * Regenerate recovery codes.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function regenerateRecoveryCodes(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'password' => ['required', 'string'],
        ]);

        // Verify password
        if (!\Hash::check($validated['password'], $user->password)) {
            return redirect()
                ->back()
                ->withErrors(['password' => 'The password is incorrect.']);
        }

        $twoFactor = TwoFactorAuthentication::where('user_id', $user->id)->first();

        if (!$twoFactor || !$twoFactor->is_enabled) {
            return redirect()
                ->route('client.security.index')
                ->with('error', 'Two-factor authentication is not enabled.');
        }

        $recoveryCodes = $this->generateRecoveryCodes();
        $twoFactor->update([
            'recovery_codes' => $recoveryCodes,
        ]);

        return redirect()
            ->route('client.security.recovery-codes')
            ->with('success', 'Recovery codes have been regenerated.');
    }

    /**
     * Generate a random secret key for 2FA.
     *
     * @return string
     */
    private function generateSecretKey(): string
    {
        // In a real implementation, use Google2FA
        // return app('pragmarx.google2fa')->generateSecretKey();

        // Simple placeholder
        return strtoupper(bin2hex(random_bytes(16)));
    }

    /**
     * Generate QR code URL.
     *
     * @param string $email
     * @param string $secret
     * @return string
     */
    private function getQRCodeUrl(string $email, string $secret): string
    {
        // In a real implementation, use Google2FA
        $appName = config('app.name', 'Edison Tech');

        return "otpauth://totp/{$appName}:{$email}?secret={$secret}&issuer={$appName}";
    }

    /**
     * Verify the 2FA code.
     *
     * @param string $secret
     * @param string $code
     * @return bool
     */
    private function verifyCode(string $secret, string $code): bool
    {
        // In a real implementation, use Google2FA
        // return app('pragmarx.google2fa')->verifyKey($secret, $code);

        // Placeholder - always return true for demonstration
        // In production, this must be properly implemented
        return strlen($code) === 6 && is_numeric($code);
    }

    /**
     * Generate recovery codes.
     *
     * @return array
     */
    private function generateRecoveryCodes(): array
    {
        $codes = [];

        for ($i = 0; $i < 10; $i++) {
            $codes[] = strtoupper(substr(bin2hex(random_bytes(5)), 0, 10));
        }

        return $codes;
    }
}
