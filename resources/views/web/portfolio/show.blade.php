@extends('layouts.guest')

@section('title', $portfolioItem->title)

@section('content')
    <!-- Project Hero with Image -->
    <div class="relative w-full h-96 overflow-hidden bg-gray-100">
        @if($portfolioItem->featured_image)
            <img src="{{ $portfolioItem->featured_image }}" alt="{{ $portfolioItem->title }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-indigo-300 flex items-center justify-center">
                <svg class="w-24 h-24 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        @endif
    </div>

    <!-- Project Details -->
    <div class="mx-auto max-w-4xl px-6 py-16 lg:px-8">
        <!-- Title and Meta -->
        <div class="mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $portfolioItem->title }}</h1>

            <!-- Meta Information -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 py-8 border-y border-gray-200">
                @if($portfolioItem->category)
                    <div>
                        <p class="text-sm text-gray-500 font-semibold uppercase">Category</p>
                        <p class="text-lg text-gray-900 mt-1">{{ $portfolioItem->category->name }}</p>
                    </div>
                @endif

                @if($portfolioItem->client)
                    <div>
                        <p class="text-sm text-gray-500 font-semibold uppercase">Client</p>
                        <p class="text-lg text-gray-900 mt-1">{{ $portfolioItem->client }}</p>
                    </div>
                @endif

                @if($portfolioItem->completion_date)
                    <div>
                        <p class="text-sm text-gray-500 font-semibold uppercase">Completed</p>
                        <p class="text-lg text-gray-900 mt-1">{{ $portfolioItem->completion_date->format('M Y') }}</p>
                    </div>
                @endif

                @if($portfolioItem->project_url)
                    <div>
                        <p class="text-sm text-gray-500 font-semibold uppercase">Visit Project</p>
                        <a href="{{ $portfolioItem->project_url }}" target="_blank" class="text-lg text-indigo-600 font-semibold hover:text-indigo-700 mt-1 inline-flex items-center">
                            View Live
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4m4-6h-2.5a2.5 2.5 0 00-2.5 2.5v2.5M9 5h.01M5 9h.01"></path>
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Description -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Project Overview</h2>
            <div class="text-lg leading-8 text-gray-600 prose prose-indigo">
                {!! nl2br(e($portfolioItem->description)) !!}
            </div>
        </div>

        <!-- Technologies -->
        @if($portfolioItem->technologies && count($portfolioItem->technologies) > 0)
            <div class="mb-12">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Technologies Used</h3>
                <div class="flex flex-wrap gap-3">
                    @foreach($portfolioItem->technologies as $tech)
                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-600/20">
                            {{ $tech }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Gallery -->
        @if($portfolioItem->gallery_images && count($portfolioItem->gallery_images) > 0)
            <div class="mb-12">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Project Gallery</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($portfolioItem->gallery_images as $image)
                        <div class="relative h-64 overflow-hidden rounded-lg bg-gray-100">
                            <img src="{{ $image }}" alt="{{ $portfolioItem->title }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- CTA Section -->
        <div class="rounded-lg bg-indigo-600 px-8 py-12 text-center sm:px-12 mb-16">
            <h3 class="text-2xl font-bold text-white mb-4">Ready to start a similar project?</h3>
            <p class="text-indigo-100 mb-8 max-w-2xl mx-auto">
                Contact us to discuss how we can create something amazing for your business.
            </p>
            <a href="{{ route('contact') }}" class="inline-flex items-center rounded-md bg-white px-8 py-3 text-sm font-semibold text-indigo-600 shadow-sm hover:bg-indigo-50 transition-colors">
                Start Your Project
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>

    <!-- Related Projects -->
    @if($relatedItems && count($relatedItems) > 0)
        <div class="bg-gray-50 px-6 py-16 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <h2 class="text-3xl font-bold text-gray-900 mb-12">Related Projects</h2>
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($relatedItems as $relatedItem)
                        <div class="group relative overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-lg transition-all duration-300">
                            <!-- Image -->
                            <div class="relative h-48 overflow-hidden bg-gray-100">
                                @if($relatedItem->featured_image)
                                    <img src="{{ $relatedItem->featured_image }}" alt="{{ $relatedItem->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-indigo-200 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="p-6">
                                @if($relatedItem->category)
                                    <span class="inline-block px-3 py-1 text-xs font-semibold text-indigo-700 bg-indigo-100 rounded-full mb-3">
                                        {{ $relatedItem->category->name }}
                                    </span>
                                @endif

                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $relatedItem->title }}</h3>

                                <a href="{{ route('portfolio.show', $relatedItem->id) }}" class="inline-flex items-center text-indigo-600 font-semibold hover:text-indigo-700 group/link">
                                    View Project
                                    <svg class="w-4 h-4 ml-2 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endsection
