@extends('layouts.admin')

@section('content')
<div class="py-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $service->title }}</h1>
            <p class="text-gray-600 mt-1">View and manage service details</p>
        </div>
        <div class="flex gap-3">
            <a
                href="{{ route('admin.services.edit', $service->id) }}"
                class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 font-medium flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
            <a
                href="{{ route('admin.services.index') }}"
                class="px-6 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition duration-200 font-medium"
            >
                Back
            </a>
        </div>
    </div>

    <!-- Service Info Card -->
    <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 mb-8">
            <!-- Icon/Image Section -->
            <div class="flex flex-col items-center justify-center">
                @if($service->image)
                    <img
                        src="{{ asset('storage/' . $service->image) }}"
                        alt="{{ $service->title }}"
                        class="w-32 h-32 rounded-lg object-cover shadow-md mb-4"
                    />
                @elseif($service->icon)
                    <div class="w-32 h-32 rounded-lg bg-indigo-100 flex items-center justify-center mb-4">
                        <i class="{{ $service->icon }} text-indigo-600 text-5xl"></i>
                    </div>
                @else
                    <div class="w-32 h-32 rounded-lg bg-gray-100 flex items-center justify-center mb-4">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                        </svg>
                    </div>
                @endif
                <p class="text-sm text-gray-500 text-center">{{ $service->title }}</p>
            </div>

            <!-- Service Information -->
            <div class="lg:col-span-3 space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <!-- Title -->
                    <div>
                        <p class="text-sm font-medium text-gray-500">Service Title</p>
                        <p class="text-gray-900 font-semibold text-lg mt-1">{{ $service->title }}</p>
                    </div>

                    <!-- Status -->
                    <div>
                        <p class="text-sm font-medium text-gray-500">Status</p>
                        <div class="mt-1">
                            @if($service->is_active)
                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">
                                    <span class="w-2 h-2 bg-green-600 rounded-full"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-red-100 text-red-800 text-sm font-semibold rounded-full">
                                    <span class="w-2 h-2 bg-red-600 rounded-full"></span>
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Featured Status -->
                    <div>
                        <p class="text-sm font-medium text-gray-500">Featured</p>
                        <div class="mt-1">
                            @if($service->is_featured)
                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-semibold rounded-full">
                                    <span class="w-2 h-2 bg-yellow-600 rounded-full"></span>
                                    Featured
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 text-gray-800 text-sm font-semibold rounded-full">
                                    <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                                    Standard
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Display Order -->
                    <div>
                        <p class="text-sm font-medium text-gray-500">Display Order</p>
                        <p class="text-gray-900 font-semibold text-lg mt-1">{{ $service->display_order ?? 0 }}</p>
                    </div>
                </div>

                <hr />

                <!-- Slug -->
                <div>
                    <p class="text-sm font-medium text-gray-500">Slug</p>
                    <p class="text-gray-900 font-semibold mt-1 break-all">{{ $service->slug }}</p>
                </div>
            </div>
        </div>

        <hr class="my-8" />

        <!-- Descriptions Section -->
        <div class="mb-8">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Description</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Short Description -->
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-2">Short Description</p>
                    <p class="text-gray-900">{{ $service->short_description }}</p>
                </div>

                <!-- Full Description -->
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-2">Full Description</p>
                    <div class="text-gray-900 prose prose-sm max-w-none">
                        {!! nl2br($service->description) !!}
                    </div>
                </div>
            </div>
        </div>

        @if($service->features && count($service->features) > 0)
            <hr class="my-8" />

            <!-- Features Section -->
            <div class="mb-8">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Features</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($service->features as $feature)
                        <span class="inline-flex items-center px-4 py-2 bg-indigo-100 text-indigo-800 text-sm font-medium rounded-full">
                            {{ $feature }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        @if($service->price_starting_at)
            <hr class="my-8" />

            <!-- Pricing Info -->
            <div class="mb-8">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Pricing</h2>
                <div class="bg-indigo-50 rounded-lg p-6 border border-indigo-200">
                    <p class="text-sm font-medium text-indigo-600 mb-2">Price Starting At</p>
                    <p class="text-3xl font-bold text-indigo-900">{{ $service->price_starting_at }}</p>
                </div>
            </div>
        @endif

        <hr class="my-8" />

        <!-- Meta Information Section -->
        <div>
            <h2 class="text-lg font-bold text-gray-900 mb-4">SEO & Meta Information</h2>
            <div class="space-y-6">
                <!-- Meta Title -->
                <div>
                    <p class="text-sm font-medium text-gray-500">Meta Title</p>
                    <p class="text-gray-900 font-semibold mt-1">{{ $service->meta_title ?? 'Not set' }}</p>
                </div>

                <!-- Meta Description -->
                <div>
                    <p class="text-sm font-medium text-gray-500">Meta Description</p>
                    <p class="text-gray-900 mt-1">{{ $service->meta_description ?? 'Not set' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
