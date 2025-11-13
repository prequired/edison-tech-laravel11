@extends('layouts.guest')

@section('title', 'Blog')

@section('content')
    <!-- Hero Section -->
    <div class="relative isolate overflow-hidden bg-gradient-to-b from-indigo-50 to-white px-6 py-24 sm:py-32 lg:overflow-visible lg:px-0">
        <div class="absolute inset-0 -z-10 overflow-hidden">
            <svg class="absolute left-[max(50%,25rem)] top-0 h-[64rem] w-[128rem] -translate-x-1/2 stroke-gray-200 [mask-image:radial-gradient(64rem_64rem_at_50%_0%,white,transparent)]" aria-hidden="true">
                <defs>
                    <pattern id="blog-pattern" x="50%" y="50%" patternUnits="userSpaceOnUse" patternTransform="translate(-64 0)" width="200" height="200">
                        <path d="M.5,200V.5H200" fill="none" />
                    </pattern>
                </defs>
                <svg x="50%" y="50%" class="overflow-visible fill-gray-50">
                    <path d="M-200 0h201v201h-201Z M600 0h201v201h-201Z M-400 600h201v201h-201Z M200 800h201v201h-201Z" stroke-width="0" />
                </svg>
                <rect width="100%" height="100%" stroke-width="0" fill="url(#blog-pattern)" />
            </svg>
        </div>
        <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">
                Our <span class="text-indigo-600">Blog</span>
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-gray-600">
                Stay updated with the latest insights, tips, and news from our team on web development, digital strategy, and business growth.
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="mx-auto max-w-7xl px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-3">
            <!-- Blog Posts -->
            <div class="lg:col-span-2">
                <div class="space-y-12">
                    @forelse($posts as $post)
                        <article class="border-b border-gray-200 pb-12 last:border-b-0">
                            <!-- Featured Image -->
                            @if($post->featured_image)
                                <div class="relative h-64 overflow-hidden rounded-lg bg-gray-100 mb-6">
                                    <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                </div>
                            @endif

                            <!-- Category -->
                            @if($post->category)
                                <div class="mb-3">
                                    <span class="inline-block px-3 py-1 text-xs font-semibold text-indigo-700 bg-indigo-100 rounded-full">
                                        {{ $post->category->name }}
                                    </span>
                                </div>
                            @endif

                            <!-- Title -->
                            <h2 class="text-3xl font-bold text-gray-900 mb-3">
                                <a href="{{ route('blog.show', $post->id) }}" class="hover:text-indigo-600 transition-colors">
                                    {{ $post->title }}
                                </a>
                            </h2>

                            <!-- Meta Information -->
                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-4">
                                @if($post->author)
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-900">{{ $post->author }}</span>
                                    </div>
                                @endif
                                <time datetime="{{ $post->published_at->toIso8601String() }}">
                                    {{ $post->published_at->format('M d, Y') }}
                                </time>
                                <span>{{ $post->reading_time ?? 5 }} min read</span>
                            </div>

                            <!-- Excerpt -->
                            <p class="text-lg text-gray-600 mb-6">
                                {{ $post->excerpt }}
                            </p>

                            <!-- Read More Link -->
                            <a href="{{ route('blog.show', $post->id) }}" class="inline-flex items-center text-indigo-600 font-semibold hover:text-indigo-700 group/link">
                                Read More
                                <svg class="w-4 h-4 ml-2 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </article>
                    @empty
                        <div class="text-center py-12">
                            <p class="text-gray-500 text-lg">No blog posts available at the moment.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($posts->hasPages())
                    <div class="mt-12 flex justify-center">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Categories -->
                @if($categories && count($categories) > 0)
                    <div class="mb-8 rounded-lg border border-gray-200 bg-white p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Categories</h3>
                        <ul class="space-y-2">
                            @foreach($categories as $category)
                                <li>
                                    <a href="{{ route('blog.index', ['category' => $category->slug]) }}" class="text-gray-600 hover:text-indigo-600 transition-colors flex items-center justify-between">
                                        <span>{{ $category->name }}</span>
                                        <span class="text-sm text-gray-500">({{ $category->posts_count ?? 0 }})</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Recent Posts -->
                @if($recentPosts && count($recentPosts) > 0)
                    <div class="mb-8 rounded-lg border border-gray-200 bg-white p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Posts</h3>
                        <ul class="space-y-4">
                            @foreach($recentPosts as $recentPost)
                                <li class="pb-4 border-b border-gray-100 last:border-b-0 last:pb-0">
                                    <a href="{{ route('blog.show', $recentPost->id) }}" class="text-gray-900 hover:text-indigo-600 font-semibold text-sm">
                                        {{ $recentPost->title }}
                                    </a>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $recentPost->published_at->format('M d, Y') }}
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Tags -->
                @if($allTags && count($allTags) > 0)
                    <div class="rounded-lg border border-gray-200 bg-white p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($allTags as $tag)
                                <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" class="px-3 py-1 text-xs font-semibold text-indigo-600 bg-indigo-50 rounded-full hover:bg-indigo-100 transition-colors">
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Newsletter CTA -->
    <div class="bg-indigo-600 py-16">
        <div class="mx-auto max-w-2xl px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Subscribe to our newsletter</h2>
            <p class="text-indigo-100 mb-8">
                Get the latest articles delivered to your inbox every week.
            </p>
            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="sm:flex sm:max-w-md mx-auto">
                @csrf
                <input type="email" name="email" placeholder="Enter your email" required class="w-full rounded-md border-0 px-4 py-3 text-gray-900 placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-400">
                <button type="submit" class="mt-3 sm:mt-0 sm:ml-3 w-full sm:w-auto rounded-md bg-white px-6 py-3 font-semibold text-indigo-600 shadow-sm hover:bg-indigo-50 transition-colors">
                    Subscribe
                </button>
            </form>
        </div>
    </div>
@endsection
