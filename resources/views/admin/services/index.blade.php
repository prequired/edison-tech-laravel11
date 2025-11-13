@extends('layouts.admin')

@section('content')
<div class="py-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Services</h1>
            <p class="text-gray-600 mt-1">Manage all services and their details</p>
        </div>
        <a
            href="{{ route('admin.services.create') }}"
            class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 font-medium flex items-center gap-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Service
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('admin.services.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input
                        type="text"
                        name="search"
                        placeholder="Search by title or description..."
                        value="{{ request('search') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    />
                </div>

                <!-- Featured Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Featured</label>
                    <select
                        name="is_featured"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    >
                        <option value="">All Services</option>
                        <option value="1" {{ request('is_featured') === '1' ? 'selected' : '' }}>Featured</option>
                        <option value="0" {{ request('is_featured') === '0' ? 'selected' : '' }}>Not Featured</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end gap-2">
                    <button
                        type="submit"
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium"
                    >
                        Filter
                    </button>
                    <a
                        href="{{ route('admin.services.index') }}"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition font-medium text-center"
                    >
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Services Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($services->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 w-12" title="Drag to reorder">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Icon/Image</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Title</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Description</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Featured</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Active</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Order</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($services as $service)
                            <tr class="hover:bg-gray-50 transition duration-200">
                                <!-- Drag Handle -->
                                <td class="px-6 py-4 text-center cursor-move hover:text-indigo-600">
                                    <svg class="w-5 h-5 inline text-gray-400 hover:text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                    </svg>
                                </td>

                                <!-- Icon/Image -->
                                <td class="px-6 py-4">
                                    @if($service->image)
                                        <img
                                            src="{{ asset('storage/' . $service->image) }}"
                                            alt="{{ $service->title }}"
                                            class="w-10 h-10 rounded-lg object-cover bg-gray-100"
                                        />
                                    @elseif($service->icon)
                                        <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                            <i class="{{ $service->icon }} text-indigo-600 text-lg"></i>
                                        </div>
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                            </svg>
                                        </div>
                                    @endif
                                </td>

                                <!-- Title -->
                                <td class="px-6 py-4">
                                    <p class="text-gray-900 font-medium">{{ $service->title }}</p>
                                </td>

                                <!-- Short Description -->
                                <td class="px-6 py-4">
                                    <p class="text-gray-600 text-sm truncate max-w-xs">{{ Str::limit($service->short_description, 50) }}</p>
                                </td>

                                <!-- Featured Badge -->
                                <td class="px-6 py-4">
                                    @if($service->is_featured)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                                            <span class="w-2 h-2 bg-yellow-600 rounded-full"></span>
                                            Featured
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">
                                            <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                            Standard
                                        </span>
                                    @endif
                                </td>

                                <!-- Active Badge -->
                                <td class="px-6 py-4">
                                    @if($service->is_active)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                            <span class="w-2 h-2 bg-green-600 rounded-full"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                                            <span class="w-2 h-2 bg-red-600 rounded-full"></span>
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <!-- Display Order -->
                                <td class="px-6 py-4">
                                    <p class="text-gray-900 font-medium">{{ $service->display_order ?? 0 }}</p>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <a
                                            href="{{ route('admin.services.show', $service->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900 font-medium text-sm"
                                        >
                                            View
                                        </a>
                                        <a
                                            href="{{ route('admin.services.edit', $service->id) }}"
                                            class="text-blue-600 hover:text-blue-900 font-medium text-sm"
                                        >
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                {{ $services->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No services found</h3>
                <p class="text-gray-600 mb-6">Get started by creating a new service.</p>
                <a
                    href="{{ route('admin.services.create') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 font-medium"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Service
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
