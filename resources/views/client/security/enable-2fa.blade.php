@extends('layouts.app')

@section('title', 'Enable Two-Factor Authentication')

@section('header', 'Enable Two-Factor Authentication')

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Main Setup Card -->
    <div class="lg:col-span-2">
        <div class="bg-white shadow sm:rounded-lg">
            <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Setup Two-Factor Authentication</h3>
                <p class="mt-1 text-sm text-gray-500">Follow these steps to enable two-factor authentication on your account.</p>
            </div>

            <div class="space-y-6 px-4 py-5 sm:px-6">
                <!-- Step 1: Install Authenticator -->
                <div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-md bg-indigo-100 text-indigo-600 text-sm font-semibold">1</div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-base font-medium text-gray-900">Install an Authenticator App</h4>
                            <p class="mt-2 text-sm text-gray-600">Download and install an authenticator application on your mobile device. We recommend:</p>
                            <ul class="mt-3 space-y-2 text-sm text-gray-600">
                                <li class="flex items-center">
                                    <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Google Authenticator
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Microsoft Authenticator
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Authy
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    1Password
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-200"></div>

                <!-- Step 2: Scan QR Code -->
                <div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-md bg-indigo-100 text-indigo-600 text-sm font-semibold">2</div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-base font-medium text-gray-900">Scan QR Code</h4>
                            <p class="mt-2 text-sm text-gray-600">Open your authenticator app and scan this QR code to add your account:</p>

                            <!-- QR Code Display -->
                            <div class="mt-4 flex justify-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                                @if(isset($qrCodeUrl))
                                    <img src="{{ $qrCodeUrl }}" alt="Two-Factor Authentication QR Code" class="h-64 w-64">
                                @else
                                    <div class="h-64 w-64 bg-gray-200 rounded flex items-center justify-center">
                                        <span class="text-gray-500">QR Code will appear here</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-200"></div>

                <!-- Step 3: Manual Entry -->
                <div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-md bg-indigo-100 text-indigo-600 text-sm font-semibold">3</div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-base font-medium text-gray-900">Or Enter Manually</h4>
                            <p class="mt-2 text-sm text-gray-600">If you cannot scan the QR code, enter this secret key manually:</p>

                            <!-- Secret Key Display -->
                            <div class="mt-4 flex items-center space-x-2">
                                <div class="flex-1 px-4 py-3 bg-gray-50 rounded-lg border border-gray-200 font-mono text-sm">
                                    @if(isset($secretKey))
                                        <span id="secret-key">{{ chunk_split($secretKey, 4, ' ') }}</span>
                                    @else
                                        <span>Your secret key will appear here</span>
                                    @endif
                                </div>
                                <button type="button" onclick="copySecretKey()" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    Copy
                                </button>
                            </div>

                            <p class="mt-2 text-xs text-gray-500">Store this key in a safe place. You'll need it to recover your account if you lose access to your authenticator app.</p>
                        </div>
                    </div>
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-200"></div>

                <!-- Step 4: Verify -->
                <div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-md bg-indigo-100 text-indigo-600 text-sm font-semibold">4</div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-base font-medium text-gray-900">Verify Your Setup</h4>
                            <p class="mt-2 text-sm text-gray-600">Enter the 6-digit code from your authenticator app to verify it's working correctly:</p>

                            <!-- Verification Form -->
                            <form method="POST" action="{{ route('client.security.2fa.store') }}" class="mt-4 space-y-4">
                                @csrf

                                <div>
                                    <label for="code" class="block text-sm font-medium text-gray-700">Verification Code</label>
                                    <input type="text" name="code" id="code" required maxlength="6" inputmode="numeric"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('code') border-red-300 @else border-gray-300 @enderror"
                                        placeholder="000000"
                                        pattern="[0-9]{6}">
                                    @error('code')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex items-center justify-between">
                                    <a href="{{ route('client.security.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                        Cancel
                                    </a>
                                    <button type="submit" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Verify and Enable
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Help and Information Sidebar -->
    <div class="space-y-6">
        <!-- Important Information -->
        <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Important</h3>
                    <div class="mt-2 text-sm text-yellow-700 space-y-2">
                        <p>Save your secret key in a secure location. You'll need it if you lose access to your authenticator app.</p>
                        <p>After enabling 2FA, you'll receive recovery codes that can be used to access your account if you lose your phone.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- What is 2FA -->
        <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zM8 7a1 1 0 000 2h6a1 1 0 100-2H8zm0 4a1 1 0 000 2h6a1 1 0 100-2H8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">What is 2FA?</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Two-factor authentication adds a second layer of security to your account. In addition to your password, you'll need to enter a code from your authenticator app when signing in.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Benefits -->
        <div class="rounded-lg bg-indigo-50 border border-indigo-200 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000-2H3a1 1 0 00-1 1v12a1 1 0 001 1h14a1 1 0 001-1V4a1 1 0 00-1-1h-2a1 1 0 000 2 2 2 0 012 2v7a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm2-1a1 1 0 000 2h6a1 1 0 100-2H6z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-indigo-800">Security Benefits</h3>
                    <ul class="mt-2 text-sm text-indigo-700 space-y-1">
                        <li class="flex items-start">
                            <svg class="h-4 w-4 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Prevent unauthorized access
                        </li>
                        <li class="flex items-start">
                            <svg class="h-4 w-4 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Protect against password theft
                        </li>
                        <li class="flex items-start">
                            <svg class="h-4 w-4 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Works offline
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copySecretKey() {
    const secretKey = document.getElementById('secret-key').textContent;
    navigator.clipboard.writeText(secretKey.replace(/\s/g, '')).then(() => {
        alert('Secret key copied to clipboard');
    }).catch(() => {
        alert('Failed to copy secret key');
    });
}

// Format verification code input
document.getElementById('code').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
});
</script>
@endsection
