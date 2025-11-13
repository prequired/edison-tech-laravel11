@extends('layouts.admin')

@section('title', 'Portfolio')
@section('header', 'Portfolio Items')

@section('header-actions')
    <a href="{{ route('admin.portfolio.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        New Portfolio Item
    </a>
@endsection

@section('content')
    <!-- Statistics -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-4 mb-6">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Total Items</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $items->total() }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Published</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600">{{ $publishedCount }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Featured</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-indigo-600">{{ $featuredCount }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Categories</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $categoriesCount }}</dd>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 rounded-lg bg-white p-4 shadow">
        <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <select name="category" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">All Status</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Apply</button>
        </form>
    </div>

    <!-- Portfolio Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($items as $item)
            <div class="overflow-hidden rounded-lg bg-white shadow hover:shadow-lg transition-shadow">
                @if($item->featured_image)
                    <img src="{{ asset('storage/' . $item->featured_image) }}" alt="{{ $item->title }}" class="h-48 w-full object-cover">
                @else
                    <div class="h-48 w-full bg-gray-200 flex items-center justify-center">
                        <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                @endif
                <div class="p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-indigo-600">{{ $item->category }}</span>
                        <div class="flex space-x-1">
                            @if($item->is_featured)
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800">Featured</span>
                            @endif
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $item->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">{{ $item->title }}</h3>
                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $item->description }}</p>
                    <div class="flex items-center justify-between">
                        <div class="flex flex-wrap gap-1">
                            @foreach(array_slice($item->technologies ?? [], 0, 3) as $tech)
                                <span class="inline-flex items-center rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800">{{ $tech }}</span>
                            @endforeach
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.portfolio.show', $item) }}" class="text-sm text-indigo-600 hover:text-indigo-900">View</a>
                            <a href="{{ route('admin.portfolio.edit', $item) }}" class="text-sm text-indigo-600 hover:text-indigo-900">Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 rounded-lg bg-white p-12 text-center shadow">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No portfolio items</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new portfolio item.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.portfolio.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        New Portfolio Item
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if($items->hasPages())
        <div class="mt-6">{{ $items->withQueryString()->links() }}</div>
    @endif
@endsection
