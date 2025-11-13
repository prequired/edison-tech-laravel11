@extends('layouts.admin')

@section('title', $item->title)
@section('header', $item->title)

@section('header-actions')
    <a href="{{ route('admin.portfolio.edit', $item) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
        Edit
    </a>
@endsection

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            @if($item->featured_image)
                <img src="{{ asset('storage/' . $item->featured_image) }}" alt="{{ $item->title }}" class="w-full h-96 object-cover rounded-lg shadow">
            @endif

            <div class="rounded-lg bg-white shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Description</h3>
                <div class="prose max-w-none">{{ $item->description }}</div>
            </div>

            @if($item->content)
                <div class="rounded-lg bg-white shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Full Content</h3>
                    <div class="prose max-w-none">{!! nl2br(e($item->content)) !!}</div>
                </div>
            @endif

            @if($item->images && count($item->images) > 0)
                <div class="rounded-lg bg-white shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Gallery</h3>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($item->images as $image)
                            <img src="{{ asset('storage/' . $image) }}" alt="{{ $item->title }}" class="rounded-lg">
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="rounded-lg bg-white shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Details</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $item->category }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Client</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $item->client_name ?? 'N/A' }}</dd>
                    </div>
                    @if($item->project_url)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Project URL</dt>
                            <dd class="mt-1 text-sm"><a href="{{ $item->project_url }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">View Live</a></dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $item->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </dd>
                    </div>
                    @if($item->is_featured)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Featured</dt>
                            <dd class="mt-1">
                                <span class="inline-flex rounded-full bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-800">Yes</span>
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            @if($item->technologies && count($item->technologies) > 0)
                <div class="rounded-lg bg-white shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Technologies</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($item->technologies as $tech)
                            <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-sm font-medium text-indigo-800">{{ $tech }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="rounded-lg bg-white shadow p-6 space-y-3">
                <a href="{{ route('admin.portfolio.edit', $item) }}" class="block w-full rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white hover:bg-indigo-500">
                    Edit Item
                </a>
                <form method="POST" action="{{ route('admin.portfolio.destroy', $item) }}" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">Delete</button>
                </form>
            </div>
        </div>
    </div>
@endsection
