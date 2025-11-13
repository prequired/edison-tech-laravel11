@extends('layouts.guest')

@section('title', $service->title)

@section('content')
    <!-- Hero Section with Icon -->
    <div class="relative bg-gradient-to-b from-indigo-50 to-white px-6 py-20 sm:py-24 lg:px-8">
        <div class="mx-auto max-w-4xl">
            <div class="flex flex-col items-start gap-8">
                <!-- Icon -->
                <div class="flex items-center justify-center w-16 h-16 rounded-lg bg-indigo-600 text-white">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>

                <!-- Title and Description -->
                <div>
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                        {{ $service->title }}
                    </h1>
                    <p class="mt-6 text-xl leading-8 text-gray-600">
                        {{ $service->description }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Details -->
    <div class="mx-auto max-w-4xl px-6 py-16 lg:px-8">
        <!-- Features Section -->
        @if($service->features && count($service->features) > 0)
            <div class="mb-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-8">Key Features</h2>
                <div class="flex flex-wrap gap-3">
                    @foreach($service->features as $feature)
                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-600/20">
                            {{ $feature }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Full Content -->
        <div class="prose prose-indigo max-w-4xl mb-16">
            <div class="text-lg leading-8 text-gray-600">
                @if($service->full_content)
                    {!! nl2br(e($service->full_content)) !!}
                @else
                    <p>{{ $service->description }}</p>
                @endif
            </div>
        </div>

        <!-- Pricing Section -->
        @if($service->pricing)
            <div class="mb-16 rounded-lg bg-gray-50 p-8 border border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Pricing</h3>
                <p class="text-gray-600 mb-6">{{ $service->pricing }}</p>
                <p class="text-sm text-gray-500">Contact us for a custom quote tailored to your specific needs.</p>
            </div>
        @endif

        <!-- CTA Section -->
        <div class="rounded-lg bg-indigo-600 px-8 py-12 text-center sm:px-12">
            <h3 class="text-2xl font-bold text-white mb-4">Ready to Get Started?</h3>
            <p class="text-indigo-100 mb-8 max-w-2xl mx-auto">
                Let's discuss how {{ $service->title }} can help achieve your business goals.
            </p>
            <a href="{{ route('contact') }}" class="inline-flex items-center rounded-md bg-white px-8 py-3 text-sm font-semibold text-indigo-600 shadow-sm hover:bg-indigo-50 transition-colors">
                Get Started
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>

    <!-- Related Services -->
    @if($relatedServices && count($relatedServices) > 0)
        <div class="bg-gray-50 px-6 py-16 lg:px-8">
            <div class="mx-auto max-w-4xl">
                <h2 class="text-2xl font-bold text-gray-900 mb-12">Related Services</h2>
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    @foreach($relatedServices as $relatedService)
                        <div class="rounded-lg border border-gray-200 bg-white p-8 hover:shadow-md transition-shadow">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                {{ $relatedService->title }}
                            </h3>
                            <p class="text-gray-600 mb-4 line-clamp-2">
                                {{ $relatedService->description }}
                            </p>
                            <a href="{{ route('services.show', $relatedService->id) }}" class="inline-flex items-center text-indigo-600 font-semibold hover:text-indigo-700">
                                Explore
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endsection
