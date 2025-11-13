@extends('layouts.app')

@section('title', 'Security Settings')

@section('header', 'Security Settings')

@section('content')
<div class="space-y-6">
    <!-- Two-Factor Authentication Card -->
    <div class="rounded-lg bg-white shadow">
        <div class="px-4 py-5 sm:px-6">
            <div class="flex items-start justify-between">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-md bg-indigo-100">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                    </div>
                    <div class="pt-0.5">
                        <h3 class="text-lg font-medium text-gray-900">Two-Factor Authentication</h3>
                        <p class="mt-1 text-sm text-gray-500">Add an extra layer of security to your account by requiring a code when you log in.</p>
                        @if($twoFactor && $twoFactor->enabled_at)
                            <div class="mt-3 flex items-center space-x-2">
                                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-0.5 text-sm font-medium text-green-800">
                                    <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Enabled
                                </span>
                                <span class="text-sm text-gray-600">Enabled on {{ $twoFactor->enabled_at->format('M d, Y') }}</span>
                            </div>
                        @else
                            <div class="mt-3">
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-0.5 text-sm font-medium text-gray-800">
                                    <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                    Not Enabled
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
                <div>
                    @if($twoFactor && $twoFactor->enabled_at)
                        <form method="POST" action="{{ route('client.security.2fa.disable') }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure you want to disable two-factor authentication? This will reduce the security of your account.')" class="rounded-md border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                Disable 2FA
                            </button>
                        </form>
                    @else
                        <a href="{{ route('client.security.2fa.enable') }}" class="rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Enable 2FA
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Password Card -->
    <div class="rounded-lg bg-white shadow">
        <div class="px-4 py-5 sm:px-6">
            <div class="flex items-start justify-between">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-md bg-indigo-100">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                    </div>
                    <div class="pt-0.5">
                        <h3 class="text-lg font-medium text-gray-900">Password</h3>
                        <p class="mt-1 text-sm text-gray-500">Manage your password and keep your account secure.</p>
                        @if($user->password_changed_at)
                            <div class="mt-3 text-sm text-gray-600">
                                Password last changed on <span class="font-medium">{{ $user->password_changed_at->format('M d, Y') }}</span>
                            </div>
                        @else
                            <div class="mt-3 text-sm text-gray-600">
                                Password last changed <span class="font-medium">when account was created</span>
                            </div>
                        @endif
                    </div>
                </div>
                <div>
                    <a href="{{ route('client.profile.password.edit') }}" class="rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Change Password
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Sessions Card -->
    <div class="rounded-lg bg-white shadow">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-10 w-10 rounded-md bg-indigo-100">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20m0 0l-.75 3M9 20H5m4 0h10m0-4l.75-3M15 20l.75 3M15 20h4m0-4V5a2 2 0 00-2-2H5a2 2 0 00-2 2v11m0-7l.75-3M3 13h18" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Active Sessions</h3>
                    <p class="mt-1 text-sm text-gray-500">Manage your active sessions across different devices.</p>
                </div>
            </div>
        </div>

        <div class="divide-y divide-gray-200">
            <!-- Current Session -->
            <div class="px-4 py-5 sm:px-6">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Current Session</p>
                                <p class="text-sm text-gray-500">This device</p>
                            </div>
                        </div>
                        <div class="mt-3 text-sm text-gray-600">
                            <p>Last active: <span class="font-medium">just now</span></p>
                            <p class="mt-1">IP Address: <span class="font-medium">{{ request()->ip() }}</span></p>
                        </div>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-0.5 text-sm font-medium text-green-800">
                        <svg class="mr-1.5 h-3 w-3" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3" />
                        </svg>
                        Active
                    </span>
                </div>
            </div>

            <!-- Other Sessions Message -->
            <div class="px-4 py-5 sm:px-6">
                <p class="text-sm text-gray-600">Other active sessions will appear here. Log out from other sessions if needed.</p>
            </div>
        </div>
    </div>

    <!-- Recent Activity Log -->
    <div class="rounded-lg bg-white shadow">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-10 w-10 rounded-md bg-indigo-100">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
                    <p class="mt-1 text-sm text-gray-500">Monitor your account activity and security events.</p>
                </div>
            </div>
        </div>

        <div class="divide-y divide-gray-200">
            <div class="px-4 py-5 sm:px-6">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Successful Login</p>
                                <p class="text-sm text-gray-500">You signed in to your account</p>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-600">{{ now()->subHours(2)->format('M d, Y g:i A') }}</p>
                    </div>
                </div>
            </div>

            <div class="px-4 py-5 sm:px-6">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zM8 7a1 1 0 000 2h6a1 1 0 100-2H8zm0 4a1 1 0 000 2h6a1 1 0 100-2H8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Account Updated</p>
                                <p class="text-sm text-gray-500">Your profile information was changed</p>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-600">{{ now()->subDays(1)->format('M d, Y g:i A') }}</p>
                    </div>
                </div>
            </div>

            <div class="px-4 py-5 sm:px-6">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Login from New Device</p>
                                <p class="text-sm text-gray-500">First login from this device</p>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-600">{{ now()->subDays(5)->format('M d, Y g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Recommendations -->
    <div class="rounded-lg bg-indigo-50 border border-indigo-200 p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-indigo-900">Security Recommendations</h3>
                <div class="mt-3 space-y-3 text-sm text-indigo-800">
                    <div class="flex items-start">
                        <svg class="h-4 w-4 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>Enable two-factor authentication for enhanced account security</span>
                    </div>
                    <div class="flex items-start">
                        <svg class="h-4 w-4 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>Use a strong, unique password that you don't use elsewhere</span>
                    </div>
                    <div class="flex items-start">
                        <svg class="h-4 w-4 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>Review your active sessions regularly and log out from unknown devices</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
