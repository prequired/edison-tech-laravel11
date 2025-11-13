<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Edison Tech') }} - @yield('title', 'Project Management')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    <div class="min-h-full">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex">
                        <div class="flex flex-shrink-0 items-center">
                            <a href="{{ route('home') }}" class="text-2xl font-bold text-indigo-600">
                                {{ config('app.name', 'Edison Tech') }}
                            </a>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ route('home') }}" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-900 hover:border-gray-300">
                                Home
                            </a>
                            <a href="{{ route('services.index') }}" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                Services
                            </a>
                            <a href="{{ route('portfolio.index') }}" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                Portfolio
                            </a>
                            <a href="{{ route('blog.index') }}" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                Blog
                            </a>
                            <a href="{{ route('about') }}" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                About
                            </a>
                            <a href="{{ route('contact') }}" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                Contact
                            </a>
                        </div>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-4">
                        @guest
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                Get Started
                            </a>
                        @else
                            <a href="{{ auth()->user()->isClient() ? route('client.dashboard') : route('admin.dashboard') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                Dashboard
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            <!-- Alert Messages -->
            @if (session('success'))
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="rounded-md bg-green-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-50 mt-12">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="col-span-1 md:col-span-2">
                        <h3 class="text-2xl font-bold text-indigo-600">{{ config('app.name', 'Edison Tech') }}</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Professional web development and digital solutions for your business.
                        </p>
                        <div class="mt-4">
                            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="sm:flex sm:max-w-md">
                                @csrf
                                <label for="email-address" class="sr-only">Email address</label>
                                <input type="email" name="email" id="email-address" required class="w-full min-w-0 rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Enter your email">
                                <div class="mt-3 sm:ml-3 sm:mt-0 sm:flex-shrink-0">
                                    <button type="submit" class="flex w-full items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Subscribe</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Company</h3>
                        <ul class="mt-4 space-y-2">
                            <li><a href="{{ route('about') }}" class="text-sm text-gray-500 hover:text-gray-900">About</a></li>
                            <li><a href="{{ route('team.index') }}" class="text-sm text-gray-500 hover:text-gray-900">Team</a></li>
                            <li><a href="{{ route('blog.index') }}" class="text-sm text-gray-500 hover:text-gray-900">Blog</a></li>
                            <li><a href="{{ route('contact') }}" class="text-sm text-gray-500 hover:text-gray-900">Contact</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Services</h3>
                        <ul class="mt-4 space-y-2">
                            <li><a href="{{ route('services.index') }}" class="text-sm text-gray-500 hover:text-gray-900">All Services</a></li>
                            <li><a href="{{ route('portfolio.index') }}" class="text-sm text-gray-500 hover:text-gray-900">Portfolio</a></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 border-t border-gray-200 pt-8">
                    <p class="text-sm text-gray-500 text-center">
                        &copy; {{ date('Y') }} {{ config('app.name', 'Edison Tech') }}. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
