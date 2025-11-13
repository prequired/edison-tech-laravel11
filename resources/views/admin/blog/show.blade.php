@extends('layouts.admin')

@section('title', $blog->title)

@section('header', $blog->title)

@section('header-actions')
    <a href="{{ route('admin.blog.edit', $blog) }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
        Edit Post
    </a>
@endsection

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Featured Image -->
            @if ($blog->featured_image)
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <img src="{{ asset('storage/' . $blog->featured_image) }}" alt="{{ $blog->title }}" class="h-96 w-full object-cover">
                </div>
            @else
                <div class="overflow-hidden rounded-lg bg-gray-100 shadow h-96 flex items-center justify-center">
                    <svg class="h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif

            <!-- Post Information -->
            <div class="rounded-lg bg-white shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Post Information</h3>
                </div>
                <div class="px-6 py-5">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Category -->
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-500">Category</label>
                            <p class="mt-1">
                                @if ($blog->category)
                                    <span class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800">
                                        {{ $blog->category->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400">Not assigned</span>
                                @endif
                            </p>
                        </div>

                        <!-- Author -->
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-500">Author</label>
                            <p class="mt-1">
                                @if ($blog->author)
                                    <div class="flex items-center space-x-2">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 text-xs font-semibold text-indigo-600">
                                            {{ substr($blog->author->name, 0, 1) }}
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $blog->author->name }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">Unknown</span>
                                @endif
                            </p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-500">Status</label>
                            <p class="mt-1">
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-gray-100 text-gray-800',
                                        'published' => 'bg-green-100 text-green-800',
                                        'scheduled' => 'bg-blue-100 text-blue-800',
                                    ];
                                    $statusLabel = ucfirst($blog->status);
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColors[$blog->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabel }}
                                </span>
                            </p>
                        </div>

                        <!-- Published Date -->
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-500">Published Date</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if ($blog->published_at)
                                    {{ \Carbon\Carbon::parse($blog->published_at)->format('F d, Y') }}
                                @else
                                    <span class="text-gray-400">Not published</span>
                                @endif
                            </p>
                        </div>

                        <!-- Featured Badge -->
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-500">Featured</label>
                            <p class="mt-1">
                                @if ($blog->is_featured)
                                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">
                                        <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        Featured Post
                                    </span>
                                @else
                                    <span class="text-gray-400 text-sm">Not featured</span>
                                @endif
                            </p>
                        </div>

                        <!-- Slug -->
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-500">Slug</label>
                            <p class="mt-1 text-sm text-gray-900 font-mono">{{ $blog->slug }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Excerpt -->
            @if ($blog->excerpt)
                <div class="rounded-lg bg-white shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Excerpt</h3>
                    <p class="text-sm text-gray-700">{{ $blog->excerpt }}</p>
                </div>
            @endif

            <!-- Content -->
            <div class="rounded-lg bg-white shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Content</h3>
                </div>
                <div class="px-6 py-5">
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(htmlspecialchars($blog->content)) !!}
                    </div>
                </div>
            </div>

            <!-- Tags -->
            @if ($blog->tags && $blog->tags->count() > 0)
                <div class="rounded-lg bg-white shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tags</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($blog->tags as $tag)
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Meta Information -->
            <div class="rounded-lg bg-white shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Meta Information</h3>
                <div class="space-y-4">
                    @if ($blog->meta_title)
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Meta Title</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $blog->meta_title }}</p>
                        </div>
                    @endif

                    @if ($blog->meta_description)
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Meta Description</p>
                            <p class="mt-1 text-sm text-gray-700">{{ $blog->meta_description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="rounded-lg bg-white shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Info</h3>
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Created</p>
                        <p class="mt-1 text-gray-900">{{ \Carbon\Carbon::parse($blog->created_at)->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Last Updated</p>
                        <p class="mt-1 text-gray-900">{{ \Carbon\Carbon::parse($blog->updated_at)->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Content Length</p>
                        <p class="mt-1 text-gray-900">{{ strlen($blog->content) }} characters</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Word Count</p>
                        <p class="mt-1 text-gray-900">{{ str_word_count($blog->content) }} words</p>
                    </div>
                </div>
            </div>

            <!-- Post Actions -->
            <div class="rounded-lg bg-indigo-50 shadow p-6">
                <h3 class="text-lg font-medium text-indigo-900 mb-4">Preview</h3>
                <p class="text-sm text-indigo-800 mb-4">View this post on the website:</p>
                <a href="#" class="w-full inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Visit Post
                </a>
            </div>
        </div>
    </div>
@endsection
