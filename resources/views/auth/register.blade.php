@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<div class="flex min-h-screen items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8">
        <!-- Header -->
        <div class="text-center">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Create your account</h1>
            <p class="mt-2 text-sm text-gray-600">
                Already have an account?
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Sign in
                </a>
            </p>
        </div>

        <!-- Registration Form -->
        <form class="space-y-6" method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name Input -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Full name</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    required
                    autocomplete="name"
                    value="{{ old('name') }}"
                    class="mt-1 block w-full rounded-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3"
                    placeholder="John Doe"
                />
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

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

            <!-- Password Input -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="mt-1 block w-full rounded-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3"
                    placeholder="••••••••"
                />
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">At least 8 characters with uppercase, lowercase, numbers and symbols</p>
            </div>

            <!-- Confirm Password Input -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm password</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="mt-1 block w-full rounded-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3"
                    placeholder="••••••••"
                />
                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Terms Agreement -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input
                        id="terms"
                        name="terms"
                        type="checkbox"
                        required
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 h-4 w-4"
                    />
                </div>
                <label for="terms" class="ml-3 text-sm text-gray-700">
                    I agree to the
                    <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">Terms of Service</a>
                    and
                    <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">Privacy Policy</a>
                </label>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full rounded-lg bg-indigo-600 py-2.5 px-4 font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 transition-colors"
            >
                Create account
            </button>
        </form>

        <!-- Divider -->
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="bg-gray-100 px-2 text-gray-500">Or continue with</span>
            </div>
        </div>

        <!-- Social Registration Options -->
        <div class="space-y-3">
            <button
                type="button"
                class="w-full inline-flex justify-center items-center rounded-lg border border-gray-300 bg-white py-2.5 px-4 font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors"
            >
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M15.545 6.558a9.42 9.42 0 011.674 2.884c.25.569.495 1.16.736 1.772C19.892 10.195 20 11.588 20 12.792c0 1.289-.213 2.467-.064 4.038h.031c2.583 0 5.194 1.592 6.498-2.169.5-1.461.779-3.707.779-5.946 0-2.456-.235-4.735-.905-6.769-1.865.592-4.823-.648-8.02-.133-.832.148-1.714.738-2.554 1.217z"/>
                </svg>
                <span class="ml-2">Google</span>
            </button>
            <button
                type="button"
                class="w-full inline-flex justify-center items-center rounded-lg border border-gray-300 bg-white py-2.5 px-4 font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors"
            >
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 0C4.477 0 0 4.584 0 10.253c0 4.537 2.857 8.449 6.844 9.732.5.087.685-.217.685-.48 0-.237-.01-1.023-.01-1.852-2.76.603-3.343-1.44-3.343-1.44-.454-1.156-1.11-1.463-1.11-1.463-.908-.62.07-.62.07-.62 1.005.07 1.532 1.032 1.532 1.032.89 1.524 2.336 1.084 2.902.83.088-.646.35-1.086.636-1.336-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.03-2.683-.103-.253-.447-1.28.098-2.67 0 0 .84-.269 2.75 1.025A9.578 9.578 0 0110 4.972c.85.004 1.705.114 2.504.336 1.909-1.294 2.747-1.025 2.747-1.025.546 1.39.202 2.417.1 2.67.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.183.578.688.48C17.137 18.704 20 14.792 20 10.253 20 4.584 15.523 0 10 0z"/>
                </svg>
                <span class="ml-2">GitHub</span>
            </button>
        </div>

        <!-- Privacy Notice -->
        <p class="text-xs text-gray-500 text-center">
            We take your privacy seriously. Your data will never be shared with third parties.
        </p>
    </div>
</div>
@endsection
