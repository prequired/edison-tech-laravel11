@extends('layouts.admin')

@section('title', $category->name)
@section('header', $category->name)

@section('header-actions')
    <a href="{{ route('admin.blog-categories.edit', $category) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
        </svg>
        Edit Category
    </a>
@endsection

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Category Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info Card -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="border-b border-gray-200 bg-white px-6 py-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Category Information</h3>
                </div>
                <div class="px-6 py-5">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 flex items-center text-sm text-gray-900">
                                @if($category->color)
                                    <div class="h-4 w-4 rounded-full mr-2" style="background-color: {{ $category->color }}"></div>
                                @endif
                                {{ $category->name }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Slug</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <code class="rounded bg-gray-100 px-2 py-1">{{ $category->slug }}</code>
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $category->description ?? 'No description provided' }}
                            </dd>
                        </div>
                        @if($category->color)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Color</dt>
                                <dd class="mt-1 flex items-center text-sm text-gray-900">
                                    <div class="h-8 w-8 rounded-lg border border-gray-300 mr-2" style="background-color: {{ $category->color }}"></div>
                                    <code class="rounded bg-gray-100 px-2 py-1">{{ $category->color }}</code>
                                </dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                @if($category->is_active)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Active</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">Inactive</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Display Order</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $category->order ?? 'Not set' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $category->created_at->format('F d, Y g:i A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $category->updated_at->format('F d, Y g:i A') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Blog Posts -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="border-b border-gray-200 bg-white px-6 py-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Blog Posts ({{ $category->posts->count() }})</h3>
                </div>
                @if($category->posts->count() > 0)
                    <ul role="list" class="divide-y divide-gray-200">
                        @foreach($category->posts->take(10) as $post)
                            <li class="px-6 py-4 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-3">
                                            <a href="{{ route('admin.blog.show', $post) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                                                {{ $post->title }}
                                            </a>
                                            @if($post->is_featured)
                                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800">
                                                    Featured
                                                </span>
                                            @endif
                                            @if($post->status === 'draft')
                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800">
                                                    Draft
                                                </span>
                                            @elseif($post->status === 'scheduled')
                                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800">
                                                    Scheduled
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">
                                                    Published
                                                </span>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            By {{ $post->author->name }} • {{ $post->published_at ? $post->published_at->format('M d, Y') : 'Not published' }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="{{ route('admin.blog.show', $post) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                            View
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    @if($category->posts->count() > 10)
                        <div class="bg-gray-50 px-6 py-3 text-center">
                            <a href="{{ route('admin.blog.index', ['category' => $category->id]) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                View all {{ $category->posts->count() }} posts
                            </a>
                        </div>
                    @endif
                @else
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No posts</h3>
                        <p class="mt-1 text-sm text-gray-500">This category doesn't have any blog posts yet.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Statistics Card -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="border-b border-gray-200 bg-white px-6 py-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Statistics</h3>
                </div>
                <div class="px-6 py-5">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Posts</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $category->posts->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Published Posts</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $category->posts->where('status', 'published')->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Draft Posts</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $category->posts->where('status', 'draft')->count() }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="border-b border-gray-200 bg-white px-6 py-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Actions</h3>
                </div>
                <div class="px-6 py-5 space-y-3">
                    <a href="{{ route('admin.blog-categories.edit', $category) }}" class="block w-full rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        Edit Category
                    </a>
                    <a href="{{ route('admin.blog.create', ['category' => $category->id]) }}" class="block w-full rounded-md bg-white px-3 py-2 text-center text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        Create Post in Category
                    </a>
                    @if($category->posts->count() === 0)
                        <form method="POST" action="{{ route('admin.blog-categories.destroy', $category) }}" class="block" onsubmit="return confirm('Are you sure you want to delete this category?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-md bg-red-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                                Delete Category
                            </button>
                        </form>
                    @else
                        <div class="rounded-md bg-yellow-50 p-3">
                            <p class="text-xs text-yellow-800">
                                This category cannot be deleted because it has {{ $category->posts->count() }} {{ Str::plural('post', $category->posts->count()) }} associated with it.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
