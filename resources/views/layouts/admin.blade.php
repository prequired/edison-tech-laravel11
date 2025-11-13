<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Edison Tech') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    <div class="min-h-full">
        <!-- Navigation -->
        <nav class="bg-indigo-600">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <h1 class="text-white text-xl font-bold">{{ config('app.name') }} Admin</h1>
                        </div>
                        <div class="hidden md:block">
                            <div class="ml-10 flex items-baseline space-x-4">
                                <a href="{{ route('admin.dashboard') }}" class="rounded-md px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-700' : '' }}">
                                    Dashboard
                                </a>
                                <a href="{{ route('admin.projects.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500 {{ request()->routeIs('admin.projects.*') ? 'bg-indigo-700' : '' }}">
                                    Projects
                                </a>
                                <a href="{{ route('admin.companies.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500 {{ request()->routeIs('admin.companies.*') ? 'bg-indigo-700' : '' }}">
                                    Companies
                                </a>
                                <a href="{{ route('admin.invoices.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500 {{ request()->routeIs('admin.invoices.*') ? 'bg-indigo-700' : '' }}">
                                    Invoices
                                </a>
                                <a href="{{ route('admin.analytics.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500 {{ request()->routeIs('admin.analytics.*') ? 'bg-indigo-700' : '' }}">
                                    Analytics
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-4 flex items-center md:ml-6">
                            <!-- Profile dropdown -->
                            <div class="relative ml-3">
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm font-medium text-white">{{ auth()->user()->name }}</span>
                                    <form method="POST" action="{{ route('logout') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="rounded-md bg-indigo-700 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Secondary Navigation -->
        <div class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-12 space-x-4 overflow-x-auto">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center border-b-2 border-transparent px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 {{ request()->routeIs('admin.users.*') ? 'border-indigo-500 text-gray-900' : '' }}">
                        Users
                    </a>
                    <a href="{{ route('admin.team.index') }}" class="inline-flex items-center border-b-2 border-transparent px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 {{ request()->routeIs('admin.team.*') ? 'border-indigo-500 text-gray-900' : '' }}">
                        Team
                    </a>
                    <a href="{{ route('admin.tasks.index') }}" class="inline-flex items-center border-b-2 border-transparent px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 {{ request()->routeIs('admin.tasks.*') ? 'border-indigo-500 text-gray-900' : '' }}">
                        Tasks
                    </a>
                    <a href="{{ route('admin.services.index') }}" class="inline-flex items-center border-b-2 border-transparent px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 {{ request()->routeIs('admin.services.*') ? 'border-indigo-500 text-gray-900' : '' }}">
                        Services
                    </a>
                    <a href="{{ route('admin.portfolio.index') }}" class="inline-flex items-center border-b-2 border-transparent px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 {{ request()->routeIs('admin.portfolio.*') ? 'border-indigo-500 text-gray-900' : '' }}">
                        Portfolio
                    </a>
                    <a href="{{ route('admin.blog.index') }}" class="inline-flex items-center border-b-2 border-transparent px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 {{ request()->routeIs('admin.blog.*') ? 'border-indigo-500 text-gray-900' : '' }}">
                        Blog
                    </a>
                    <a href="{{ route('admin.testimonials.index') }}" class="inline-flex items-center border-b-2 border-transparent px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 {{ request()->routeIs('admin.testimonials.*') ? 'border-indigo-500 text-gray-900' : '' }}">
                        Testimonials
                    </a>
                    <a href="{{ route('admin.documents.index') }}" class="inline-flex items-center border-b-2 border-transparent px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 {{ request()->routeIs('admin.documents.*') ? 'border-indigo-500 text-gray-900' : '' }}">
                        Documents
                    </a>
                    <a href="{{ route('admin.contacts.index') }}" class="inline-flex items-center border-b-2 border-transparent px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 {{ request()->routeIs('admin.contacts.*') ? 'border-indigo-500 text-gray-900' : '' }}">
                        Contacts
                    </a>
                    <a href="{{ route('admin.newsletter.index') }}" class="inline-flex items-center border-b-2 border-transparent px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 {{ request()->routeIs('admin.newsletter.*') ? 'border-indigo-500 text-gray-900' : '' }}">
                        Newsletter
                    </a>
                </div>
            </div>
        </div>

        <!-- Page Header -->
        @hasSection('header')
            <header class="bg-white shadow">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <div class="md:flex md:items-center md:justify-between">
                        <div class="min-w-0 flex-1">
                            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                                @yield('header')
                            </h2>
                        </div>
                        @hasSection('header-actions')
                            <div class="mt-4 flex md:ml-4 md:mt-0">
                                @yield('header-actions')
                            </div>
                        @endif
                    </div>
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Alert Messages -->
                @if (session('success'))
                    <div class="rounded-md bg-green-50 p-4 mb-6">
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
                @endif

                @if (session('error'))
                    <div class="rounded-md bg-red-50 p-4 mb-6">
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
                @endif

                @if ($errors->any())
                    <div class="rounded-md bg-red-50 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc space-y-1 pl-5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
