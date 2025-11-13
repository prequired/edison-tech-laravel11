@extends('layouts.guest')

@section('title', 'Two-Factor Authentication')

@section('content')
<div class="flex min-h-screen items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center mb-4">
                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Verify your identity</h1>
            <p class="mt-2 text-sm text-gray-600">
                Enter the 6-digit code from your authenticator app
            </p>
        </div>

        <!-- 2FA Form -->
        <form class="space-y-6" method="POST" action="{{ route('two-factor.verify') }}">
            @csrf

            <!-- 2FA Code Input -->
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700">Authentication code</label>
                <div class="mt-2">
                    <input
                        id="code"
                        name="code"
                        type="text"
                        inputmode="numeric"
                        maxlength="6"
                        required
                        autocomplete="one-time-code"
                        class="block w-full rounded-lg border-0 py-3 text-center text-2xl font-mono font-semibold tracking-widest text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3"
                        placeholder="000000"
                    />
                </div>
                @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-gray-500 text-center">
                    This code expires in <span id="timer" class="font-semibold">30</span> seconds
                </p>
            </div>

            <!-- Info Box -->
            <div class="rounded-lg bg-blue-50 p-4 border border-blue-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Open your authenticator app and enter the 6-digit code shown for your account.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full rounded-lg bg-indigo-600 py-2.5 px-4 font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 transition-colors"
            >
                Verify
            </button>
        </form>

        <!-- Recovery Code Option -->
        <div class="space-y-4">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="bg-gray-100 px-2 text-gray-500">Having trouble?</span>
                </div>
            </div>

            <button
                type="button"
                onclick="document.getElementById('recovery-form').classList.toggle('hidden')"
                class="w-full inline-flex justify-center items-center rounded-lg border border-gray-300 bg-white py-2.5 px-4 font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors"
            >
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Use recovery code instead
            </button>
        </div>

        <!-- Recovery Code Form (Hidden) -->
        <form id="recovery-form" class="hidden space-y-6" method="POST" action="{{ route('two-factor.verify-recovery') }}">
            @csrf

            <div>
                <label for="recovery_code" class="block text-sm font-medium text-gray-700">Recovery code</label>
                <input
                    id="recovery_code"
                    name="recovery_code"
                    type="text"
                    required
                    autocomplete="off"
                    class="mt-1 block w-full rounded-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3"
                    placeholder="XXXX-XXXX-XXXX"
                />
                @error('recovery_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Enter one of your backup recovery codes</p>
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-indigo-600 py-2.5 px-4 font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 transition-colors"
            >
                Verify recovery code
            </button>
        </form>

        <!-- Help Section -->
        <div class="rounded-lg bg-gray-50 p-4 border border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900 mb-2">Lost your authenticator?</h3>
            <p class="text-sm text-gray-600 mb-3">
                If you've lost access to your authenticator app, use one of your recovery codes to sign in. You can then set up a new authenticator app.
            </p>
            <a href="{{ route('contact') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                Contact support
            </a>
        </div>

        <!-- Countdown Timer Script -->
        <script>
            let timeLeft = 30;
            const timerElement = document.getElementById('timer');

            const countdown = setInterval(() => {
                timeLeft--;
                timerElement.textContent = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    timerElement.parentElement.innerHTML = '<span class="text-red-600 font-semibold">Code expired</span>';
                }
            }, 1000);

            // Auto-focus and format code input
            const codeInput = document.getElementById('code');
            codeInput.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/\D/g, '').slice(0, 6);
            });

            // Auto-submit when 6 digits are entered
            codeInput.addEventListener('input', (e) => {
                if (e.target.value.length === 6) {
                    // Optional: Auto-submit the form
                    // e.target.form.submit();
                }
            });

            // Focus on code input on page load
            codeInput.focus();
        </script>
    </div>
</div>
@endsection
