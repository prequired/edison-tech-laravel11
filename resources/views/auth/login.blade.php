@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="flex min-h-screen items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8">
        <!-- Header -->
        <div class="text-center">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Welcome back</h1>
            <p class="mt-2 text-sm text-gray-600">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Sign up
                </a>
            </p>
        </div>

        <!-- Login Form -->
        <form class="space-y-6" method="POST" action="{{ route('login') }}">
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

            <!-- Password Input -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="mt-1 block w-full rounded-lg border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3"
                    placeholder="••••••••"
                />
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input
                        id="remember"
                        name="remember"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                    />
                    <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                </div>
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    Forgot password?
                </a>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full rounded-lg bg-indigo-600 py-2.5 px-4 font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 transition-colors"
            >
                Sign in
            </button>
        </form>

        <!-- Divider -->
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="bg-gray-100 px-2 text-gray-500">Or</span>
            </div>
        </div>

        <!-- Social Login Options -->
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
    </div>
</div>
@endsection
