@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
<div class="flex min-h-screen items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8">
        <!-- Header -->
        <div class="text-center">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Forgot password?</h1>
            <p class="mt-2 text-sm text-gray-600">
                Remember your password?
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Sign in
                </a>
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
                        Enter your email address and we'll send you a link to reset your password.
                    </p>
                </div>
            </div>
        </div>

        <!-- Forgot Password Form -->
        <form class="space-y-6" method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    required
                    autocomplete="email"
                    value="{{ old('email') }}"
                    class="mt-1 block w-full rounded-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3"
                    placeholder="you@example.com"
                />
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Success Message -->
            @if (session('status'))
                <div class="rounded-lg bg-green-50 p-4 border border-green-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('status') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full rounded-lg bg-indigo-600 py-2.5 px-4 font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 transition-colors"
            >
                Send password reset link
            </button>
        </form>

        <!-- Back to Login -->
        <div class="text-center">
            <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                Back to sign in
            </a>
        </div>

        <!-- Additional Help -->
        <div class="rounded-lg bg-gray-50 p-4 border border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900 mb-2">Need help?</h3>
            <p class="text-sm text-gray-600 mb-3">
                If you don't receive the password reset email within a few minutes, please check your spam folder or try again.
            </p>
            <a href="{{ route('contact') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                Contact support
            </a>
        </div>
    </div>
</div>
@endsection
