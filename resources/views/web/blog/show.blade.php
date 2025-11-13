@extends('layouts.guest')

@section('title', $post->title)

@section('content')
    <!-- Featured Image -->
    @if($post->featured_image)
        <div class="relative w-full h-96 overflow-hidden bg-gray-100">
            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
        </div>
    @else
        <div class="w-full h-96 bg-gradient-to-br from-indigo-100 to-indigo-300"></div>
    @endif

    <!-- Article Content -->
    <div class="mx-auto max-w-4xl px-6 py-16 lg:px-8">
        <!-- Category -->
        @if($post->category)
            <div class="mb-4">
                <span class="inline-block px-3 py-1 text-xs font-semibold text-indigo-700 bg-indigo-100 rounded-full">
                    {{ $post->category->name }}
                </span>
            </div>
        @endif

        <!-- Title -->
        <h1 class="text-4xl font-bold text-gray-900 mb-6">{{ $post->title }}</h1>

        <!-- Meta Information -->
        <div class="flex flex-wrap items-center gap-6 pb-6 border-b border-gray-200 mb-8">
            @if($post->author)
                <div class="flex items-center gap-3">
                    @if($post->author_image)
                        <img src="{{ $post->author_image }}" alt="{{ $post->author }}" class="w-10 h-10 rounded-full">
                    @else
                        <div class="w-10 h-10 rounded-full bg-indigo-200 flex items-center justify-center text-indigo-700 font-semibold text-sm">
                            {{ substr($post->author, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <p class="font-semibold text-gray-900">{{ $post->author }}</p>
                    </div>
                </div>
            @endif
            <time datetime="{{ $post->published_at->toIso8601String() }}" class="text-gray-600">
                {{ $post->published_at->format('F d, Y') }}
            </time>
            <span class="text-gray-600">{{ $post->reading_time ?? 5 }} min read</span>
        </div>

        <!-- Article Content -->
        <div class="prose prose-lg prose-indigo max-w-4xl mb-12">
            {!! $post->content !!}
        </div>

        <!-- Tags -->
        @if($post->tags && count($post->tags) > 0)
            <div class="py-8 border-y border-gray-200 mb-8">
                <div class="flex flex-wrap gap-2">
                    @foreach($post->tags as $tag)
                        <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" class="px-4 py-2 text-sm font-semibold text-indigo-600 bg-indigo-50 rounded-full hover:bg-indigo-100 transition-colors">
                            #{{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Share Buttons -->
        <div class="py-8 mb-12">
            <p class="text-sm font-semibold text-gray-700 mb-4">Share this article:</p>
            <div class="flex gap-4">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-colors" title="Share on Facebook">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ urlencode($post->title) }}" target="_blank" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-400 text-white hover:bg-blue-500 transition-colors" title="Share on Twitter">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2s9 5 20 5a9.5 9.5 0 00-9-5.5c4.75 2.25 7-7 7-7"/>
                    </svg>
                </a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ url()->current() }}" target="_blank" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-700 text-white hover:bg-blue-800 transition-colors" title="Share on LinkedIn">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.475-2.236-1.986-2.236-1.081 0-1.722.722-2.002 1.413-.103.249-.129.597-.129.946v5.446h-3.554s.047-8.842 0-9.769h3.554v1.391c.432-.668 1.202-1.618 2.926-1.618 2.138 0 3.745 1.398 3.745 4.402v5.594zM5.337 9.433c-1.144 0-1.915-.759-1.915-1.71 0-.956.768-1.71 1.959-1.71 1.188 0 1.914.754 1.939 1.71 0 .951-.751 1.71-1.983 1.71zm1.946 10.019H3.39V9.683h3.893v9.769zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.225 0z"/>
                    </svg>
                </a>
                <button onclick="navigator.clipboard.writeText('{{ url()->current() }}')" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-600 text-white hover:bg-gray-700 transition-colors" title="Copy link">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.658 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Author Box -->
        @if($post->author)
            <div class="rounded-lg bg-indigo-50 p-8 border border-indigo-200 mb-12">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">About the Author</h3>
                <div class="flex items-start gap-4">
                    @if($post->author_image)
                        <img src="{{ $post->author_image }}" alt="{{ $post->author }}" class="w-16 h-16 rounded-full">
                    @else
                        <div class="w-16 h-16 rounded-full bg-indigo-200 flex items-center justify-center text-indigo-700 font-bold text-xl">
                            {{ substr($post->author, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <p class="font-semibold text-gray-900">{{ $post->author }}</p>
                        @if($post->author_bio)
                            <p class="text-gray-600 mt-2">{{ $post->author_bio }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Previous/Next Navigation -->
    @if(($previousPost || $nextPost))
        <div class="border-t border-gray-200 bg-gray-50">
            <div class="mx-auto max-w-4xl px-6 py-12 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @if($previousPost)
                        <a href="{{ route('blog.show', $previousPost->id) }}" class="group block rounded-lg border border-gray-200 bg-white p-6 hover:shadow-md transition-shadow">
                            <p class="text-sm font-semibold text-indigo-600 mb-2">Previous Article</p>
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $previousPost->title }}</h3>
                            <p class="text-sm text-gray-500 mt-2">{{ $previousPost->published_at->format('M d, Y') }}</p>
                        </a>
                    @else
                        <div></div>
                    @endif

                    @if($nextPost)
                        <a href="{{ route('blog.show', $nextPost->id) }}" class="group block rounded-lg border border-gray-200 bg-white p-6 hover:shadow-md transition-shadow md:text-right">
                            <p class="text-sm font-semibold text-indigo-600 mb-2">Next Article</p>
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $nextPost->title }}</h3>
                            <p class="text-sm text-gray-500 mt-2">{{ $nextPost->published_at->format('M d, Y') }}</p>
                        </a>
                    @else
                        <div></div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Related Posts -->
    @if($relatedPosts && count($relatedPosts) > 0)
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-12">Related Articles</h2>
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach($relatedPosts as $relatedPost)
                    <article class="group rounded-lg border border-gray-200 bg-white overflow-hidden hover:shadow-lg transition-shadow">
                        @if($relatedPost->featured_image)
                            <div class="relative h-48 overflow-hidden bg-gray-100">
                                <img src="{{ $relatedPost->featured_image }}" alt="{{ $relatedPost->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                        @endif
                        <div class="p-6">
                            @if($relatedPost->category)
                                <span class="inline-block px-3 py-1 text-xs font-semibold text-indigo-700 bg-indigo-100 rounded-full mb-3">
                                    {{ $relatedPost->category->name }}
                                </span>
                            @endif
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                <a href="{{ route('blog.show', $relatedPost->id) }}" class="hover:text-indigo-600 transition-colors">
                                    {{ $relatedPost->title }}
                                </a>
                            </h3>
                            <p class="text-sm text-gray-500">{{ $relatedPost->published_at->format('M d, Y') }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @endif
@endsection
