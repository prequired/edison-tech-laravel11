@extends('layouts.guest')

@section('title', 'Our Services')

@section('content')
    <!-- Hero Section -->
    <div class="relative isolate overflow-hidden bg-gradient-to-b from-indigo-50 to-white px-6 py-24 sm:py-32 lg:overflow-visible lg:px-0">
        <div class="absolute inset-0 -z-10 overflow-hidden">
            <svg class="absolute left-[max(50%,25rem)] top-0 h-[64rem] w-[128rem] -translate-x-1/2 stroke-gray-200 [mask-image:radial-gradient(64rem_64rem_at_50%_0%,white,transparent)]" aria-hidden="true">
                <defs>
                    <pattern id="e813992c-7d53-4232-a359-a0c5495960a3" x="50%" y="50%" patternUnits="userSpaceOnUse" patternTransform="translate(-64 0)" width="200" height="200">
                        <path d="M.5,200V.5H200" fill="none" />
                    </pattern>
                </defs>
                <svg x="50%" y="50%" class="overflow-visible fill-gray-50">
                    <path d="M-200 0h201v201h-201Z M600 0h201v201h-201Z M-400 600h201v201h-201Z M200 800h201v201h-201Z" stroke-width="0" />
                </svg>
                <rect width="100%" height="100%" stroke-width="0" fill="url(#e813992c-7d53-4232-a359-a0c5495960a3)" />
            </svg>
        </div>
        <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">
                Our <span class="text-indigo-600">Services</span>
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-gray-600">
                Comprehensive solutions tailored to your business needs. From web development to digital strategy, we deliver excellence at every step.
            </p>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="mx-auto max-w-7xl px-6 lg:px-8 py-24 sm:py-32">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
            @forelse($services as $service)
                <div class="group relative overflow-hidden rounded-lg border border-gray-200 bg-white p-8 shadow-sm hover:shadow-md transition-all duration-300">
                    <!-- Icon -->
                    <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-indigo-600 text-white mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>

                    <!-- Content -->
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ $service->title }}</h3>
                    <p class="text-gray-600 mb-6 line-clamp-3">{{ $service->description }}</p>

                    <!-- Link -->
                    <a href="{{ route('services.show', $service->id) }}" class="inline-flex items-center text-indigo-600 font-semibold hover:text-indigo-700 group/link">
                        Learn More
                        <svg class="w-4 h-4 ml-2 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">No services available at the moment.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-indigo-600 py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                Ready to get started?
            </h2>
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-indigo-100">
                Contact us today to discuss how we can help transform your business with our expert services.
            </p>
            <div class="mt-10 flex justify-center gap-x-6">
                <a href="{{ route('contact') }}" class="rounded-md bg-white px-6 py-3 text-sm font-semibold text-indigo-600 shadow-sm hover:bg-indigo-50 transition-colors">
                    Get in Touch
                </a>
                <a href="{{ route('portfolio.index') }}" class="inline-flex items-center text-sm font-semibold leading-6 text-white hover:text-indigo-100">
                    See Our Work
                    <span aria-hidden="true" class="ml-2">→</span>
                </a>
            </div>
        </div>
    </div>
@endsection
