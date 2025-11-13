@extends('layouts.app')

@section('title', 'Change Password')

@section('header', 'Change Password')

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Main Content -->
    <div class="lg:col-span-2">
        <div class="bg-white shadow sm:rounded-lg">
            <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Update Password</h3>
                <p class="mt-1 text-sm text-gray-500">Change your account password to keep your account secure.</p>
            </div>

            <form method="POST" action="{{ route('client.profile.password.update') }}" class="space-y-6 px-4 py-5 sm:px-6">
                @csrf

                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                    <input type="password" name="current_password" id="current_password" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('current_password') border-red-300 @else border-gray-300 @enderror"
                        placeholder="Enter your current password">
                    @error('current_password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" name="password" id="password" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('password') border-red-300 @else border-gray-300 @enderror"
                        placeholder="Enter your new password"
                        @input="checkPasswordStrength">
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <!-- Password Strength Indicator -->
                    <div class="mt-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium text-gray-600">Password Strength</span>
                            <span id="strength-text" class="text-xs font-medium text-gray-500">Very Weak</span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-gray-200 overflow-hidden">
                            <div id="strength-bar" class="h-full bg-red-500 transition-all duration-300" style="width: 0%;"></div>
                        </div>
                    </div>
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('password_confirmation') border-red-300 @else border-gray-300 @enderror"
                        placeholder="Confirm your new password">
                    @error('password_confirmation')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Requirements -->
                <div class="rounded-md bg-blue-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zM8 7a1 1 0 000 2h6a1 1 0 100-2H8zm0 4a1 1 0 000 2h6a1 1 0 100-2H8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Password Requirements</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc space-y-1 pl-5">
                                    <li id="req-length" class="opacity-50">At least 8 characters</li>
                                    <li id="req-uppercase" class="opacity-50">At least one uppercase letter</li>
                                    <li id="req-lowercase" class="opacity-50">At least one lowercase letter</li>
                                    <li id="req-number" class="opacity-50">At least one number</li>
                                    <li id="req-special" class="opacity-50">At least one special character</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between">
                    <a href="{{ route('client.profile.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        Back to Profile
                    </a>
                    <button type="submit" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Security Tips Sidebar -->
    <div class="space-y-6">
        <div class="rounded-lg bg-yellow-50 p-4 border border-yellow-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Security Tips</h3>
                    <div class="mt-2 text-sm text-yellow-700 space-y-2">
                        <p>• Use a unique password not used elsewhere</p>
                        <p>• Avoid personal information like names or dates</p>
                        <p>• Use a combination of uppercase, lowercase, numbers, and symbols</p>
                        <p>• Consider using a password manager</p>
                        <p>• Change your password regularly</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-lg bg-indigo-50 p-4 border border-indigo-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000-2H3a1 1 0 00-1 1v12a1 1 0 001 1h14a1 1 0 001-1V4a1 1 0 00-1-1h-2a1 1 0 000 2 2 2 0 012 2v7a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm2-1a1 1 0 000 2h6a1 1 0 100-2H6z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-indigo-800">Two-Factor Authentication</h3>
                    <p class="mt-2 text-sm text-indigo-700">Protect your account further by enabling two-factor authentication in your security settings.</p>
                    <a href="{{ route('client.security.index') }}" class="mt-3 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        View Security Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    let strength = 0;

    // Check requirements
    const hasLength = password.length >= 8;
    const hasUppercase = /[A-Z]/.test(password);
    const hasLowercase = /[a-z]/.test(password);
    const hasNumber = /\d/.test(password);
    const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);

    if (hasLength) strength++;
    if (hasUppercase) strength++;
    if (hasLowercase) strength++;
    if (hasNumber) strength++;
    if (hasSpecial) strength++;

    // Update visual indicators
    updateRequirement('req-length', hasLength);
    updateRequirement('req-uppercase', hasUppercase);
    updateRequirement('req-lowercase', hasLowercase);
    updateRequirement('req-number', hasNumber);
    updateRequirement('req-special', hasSpecial);

    // Update strength bar
    const bar = document.getElementById('strength-bar');
    const text = document.getElementById('strength-text');
    const percentage = (strength / 5) * 100;

    bar.style.width = percentage + '%';

    if (strength <= 1) {
        bar.className = 'h-full bg-red-500 transition-all duration-300';
        text.textContent = 'Very Weak';
        text.className = 'text-xs font-medium text-red-600';
    } else if (strength === 2) {
        bar.className = 'h-full bg-orange-500 transition-all duration-300';
        text.textContent = 'Weak';
        text.className = 'text-xs font-medium text-orange-600';
    } else if (strength === 3) {
        bar.className = 'h-full bg-yellow-500 transition-all duration-300';
        text.textContent = 'Fair';
        text.className = 'text-xs font-medium text-yellow-600';
    } else if (strength === 4) {
        bar.className = 'h-full bg-lime-500 transition-all duration-300';
        text.textContent = 'Good';
        text.className = 'text-xs font-medium text-lime-600';
    } else {
        bar.className = 'h-full bg-green-500 transition-all duration-300';
        text.textContent = 'Strong';
        text.className = 'text-xs font-medium text-green-600';
    }
}

function updateRequirement(id, met) {
    const element = document.getElementById(id);
    if (met) {
        element.className = 'opacity-100 text-green-700';
    } else {
        element.className = 'opacity-50';
    }
}

// Check password strength on page load if password exists
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    if (passwordInput.value) {
        checkPasswordStrength();
    }
});
</script>
@endsection
