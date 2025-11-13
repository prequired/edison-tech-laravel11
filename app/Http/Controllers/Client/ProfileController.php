<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Client Profile Controller
 *
 * Allows clients to view and edit their own profile.
 */
class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param UserService $userService
     */
    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     * Display the user's profile.
     *
     * @return View
     */
    public function show(): View
    {
        $user = Auth::user();
        $user->load(['company', 'projects', 'tasks']);

        return view('client.profile.show', compact('user'));
    }

    /**
     * Show the form for editing the user's profile.
     *
     * @return View
     */
    public function edit(): View
    {
        $user = Auth::user();

        return view('client.profile.edit', compact('user'));
    }

    /**
     * Update the user's profile in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'preferences' => ['nullable', 'array'],
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $this->userService->updateUser($user, $validated);

        return redirect()
            ->route('client.profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Show the form for changing password.
     *
     * @return View
     */
    public function editPassword(): View
    {
        return view('client.profile.password');
    }

    /**
     * Update the user's password.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()
                ->back()
                ->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('client.profile.show')
            ->with('success', 'Password updated successfully.');
    }

    /**
     * Delete the user's account.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'password' => ['required', 'string'],
        ]);

        // Verify password
        if (!Hash::check($validated['password'], $user->password)) {
            return redirect()
                ->back()
                ->withErrors(['password' => 'The password is incorrect.']);
        }

        // Delete avatar if exists
        if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
            \Storage::disk('public')->delete($user->avatar);
        }

        // Logout
        Auth::logout();

        // Soft delete the user
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Your account has been deleted.');
    }
}
