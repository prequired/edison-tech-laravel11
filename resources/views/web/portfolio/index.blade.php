@extends('layouts.guest')

@section('title', 'Portfolio')

@section('content')
    <!-- Hero Section -->
    <div class="relative isolate overflow-hidden bg-gradient-to-b from-indigo-50 to-white px-6 py-24 sm:py-32 lg:overflow-visible lg:px-0">
        <div class="absolute inset-0 -z-10 overflow-hidden">
            <svg class="absolute left-[max(50%,25rem)] top-0 h-[64rem] w-[128rem] -translate-x-1/2 stroke-gray-200 [mask-image:radial-gradient(64rem_64rem_at_50%_0%,white,transparent)]" aria-hidden="true">
                <defs>
                    <pattern id="portfolio-pattern" x="50%" y="50%" patternUnits="userSpaceOnUse" patternTransform="translate(-64 0)" width="200" height="200">
                        <path d="M.5,200V.5H200" fill="none" />
                    </pattern>
                </defs>
                <svg x="50%" y="50%" class="overflow-visible fill-gray-50">
                    <path d="M-200 0h201v201h-201Z M600 0h201v201h-201Z M-400 600h201v201h-201Z M200 800h201v201h-201Z" stroke-width="0" />
                </svg>
                <rect width="100%" height="100%" stroke-width="0" fill="url(#portfolio-pattern)" />
            </svg>
        </div>
        <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">
                Our <span class="text-indigo-600">Portfolio</span>
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-gray-600">
                Explore our latest projects and see how we've helped businesses achieve their goals through innovative digital solutions.
            </p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="mx-auto max-w-7xl px-6 lg:px-8 py-12">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('portfolio.index') }}" class="px-4 py-2 rounded-full border-2 border-indigo-600 text-indigo-600 font-semibold hover:bg-indigo-50 transition-colors">
                All Projects
            </a>
            @foreach($categories as $category)
                <a href="{{ route('portfolio.index', ['category' => $category->slug]) }}" class="px-4 py-2 rounded-full border-2 border-gray-300 text-gray-700 font-semibold hover:border-indigo-600 hover:text-indigo-600 transition-colors">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Portfolio Grid -->
    <div class="mx-auto max-w-7xl px-6 lg:px-8 py-12 pb-24">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
            @forelse($portfolioItems as $item)
                <div class="group relative overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-lg transition-all duration-300">
                    <!-- Image -->
                    <div class="relative h-48 overflow-hidden bg-gray-100">
                        @if($item->featured_image)
                            <img src="{{ $item->featured_image }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
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
                        <!-- Category Badge -->
                        @if($item->category)
                            <span class="inline-block px-3 py-1 text-xs font-semibold text-indigo-700 bg-indigo-100 rounded-full mb-3">
                                {{ $item->category->name }}
                            </span>
                        @endif

                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $item->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $item->excerpt }}</p>

                        <!-- Technologies -->
                        @if($item->technologies && count($item->technologies) > 0)
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach(array_slice($item->technologies, 0, 3) as $tech)
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">
                                        {{ $tech }}
                                    </span>
                                @endforeach
                                @if(count($item->technologies) > 3)
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">
                                        +{{ count($item->technologies) - 3 }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        <!-- Link -->
                        <a href="{{ route('portfolio.show', $item->id) }}" class="inline-flex items-center text-indigo-600 font-semibold hover:text-indigo-700 group/link">
                            View Project
                            <svg class="w-4 h-4 ml-2 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">No portfolio items found.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-indigo-600 py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                Impressed by our work?
            </h2>
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-indigo-100">
                Let's discuss how we can bring your next project to life with our expertise and innovation.
            </p>
            <div class="mt-10 flex justify-center gap-x-6">
                <a href="{{ route('contact') }}" class="rounded-md bg-white px-6 py-3 text-sm font-semibold text-indigo-600 shadow-sm hover:bg-indigo-50 transition-colors">
                    Start Your Project
                </a>
                <a href="{{ route('services.index') }}" class="inline-flex items-center text-sm font-semibold leading-6 text-white hover:text-indigo-100">
                    Explore Our Services
                    <span aria-hidden="true" class="ml-2">→</span>
                </a>
            </div>
        </div>
    </div>
@endsection
