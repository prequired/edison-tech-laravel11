@extends('layouts.admin')

@section('title', 'Blog Posts')

@section('header', 'Blog Posts')

@section('header-actions')
    <a href="{{ route('admin.blog.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
        Create Post
    </a>
@endsection

@section('content')
    <!-- Statistics Section -->
    <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
        <!-- Total Posts -->
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-md bg-indigo-500">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 flex-1">
                    <dl>
                        <dt class="truncate text-sm font-medium text-gray-500">Total Posts</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['total'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Published -->
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-md bg-green-400">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 flex-1">
                    <dl>
                        <dt class="truncate text-sm font-medium text-gray-500">Published</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['published'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Draft -->
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-md bg-gray-400">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 flex-1">
                    <dl>
                        <dt class="truncate text-sm font-medium text-gray-500">Draft</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['draft'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Scheduled -->
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-md bg-blue-400">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 flex-1">
                    <dl>
                        <dt class="truncate text-sm font-medium text-gray-500">Scheduled</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['scheduled'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Featured -->
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-md bg-yellow-400">
                        <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 flex-1">
                    <dl>
                        <dt class="truncate text-sm font-medium text-gray-500">Featured</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['featured'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="mb-6 rounded-lg bg-white p-6 shadow">
        <form method="GET" action="{{ route('admin.blog.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Posts</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Post title..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2">
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" id="category" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    </select>
                </div>

                <!-- Featured -->
                <div>
                    <label for="is_featured" class="block text-sm font-medium text-gray-700 mb-1">Featured</label>
                    <select name="is_featured" id="is_featured" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2">
                        <option value="">All Posts</option>
                        <option value="1" {{ request('is_featured') === '1' ? 'selected' : '' }}>Featured Only</option>
                        <option value="0" {{ request('is_featured') === '0' ? 'selected' : '' }}>Non-Featured</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('admin.blog.index') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Clear Filters</a>
                <button type="submit" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Blog Posts Table -->
    <div class="overflow-hidden rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Image</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Author</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Featured</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Published</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($posts as $post)
                    <tr class="hover:bg-gray-50">
                        <!-- Featured Image Thumbnail -->
                        <td class="whitespace-nowrap px-6 py-4">
                            @if ($post->featured_image)
                                <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-md bg-gray-100">
                                    <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="h-full w-full object-cover">
                                </div>
                            @else
                                <div class="flex h-12 w-12 items-center justify-center rounded-md bg-gray-200">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </td>

                        <!-- Title -->
                        <td class="px-6 py-4">
                            <p class="text-sm font-semibold text-gray-900 line-clamp-2">{{ $post->title }}</p>
                        </td>

                        <!-- Category -->
                        <td class="whitespace-nowrap px-6 py-4">
                            @if ($post->category)
                                <span class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800">
                                    {{ $post->category->name }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">—</span>
                            @endif
                        </td>

                        <!-- Author -->
                        <td class="whitespace-nowrap px-6 py-4">
                            @if ($post->author)
                                <div class="flex items-center space-x-2">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-600">
                                        {{ substr($post->author->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $post->author->name }}</span>
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">—</span>
                            @endif
                        </td>

                        <!-- Status -->
                        <td class="whitespace-nowrap px-6 py-4">
                            @php
                                $statusColors = [
                                    'draft' => 'bg-gray-100 text-gray-800',
                                    'published' => 'bg-green-100 text-green-800',
                                    'scheduled' => 'bg-blue-100 text-blue-800',
                                ];
                                $statusLabel = ucfirst($post->status);
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColors[$post->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabel }}
                            </span>
                        </td>

                        <!-- Featured Badge -->
                        <td class="whitespace-nowrap px-6 py-4">
                            @if ($post->is_featured)
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">
                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    Featured
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">—</span>
                            @endif
                        </td>

                        <!-- Published Date -->
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                            @if ($post->published_at)
                                <span>{{ \Carbon\Carbon::parse($post->published_at)->format('M d, Y') }}</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-3">
                                <a href="{{ route('admin.blog.show', $post) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                <a href="{{ route('admin.blog.edit', $post) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            No blog posts found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $posts->links() }}
    </div>
@endsection
