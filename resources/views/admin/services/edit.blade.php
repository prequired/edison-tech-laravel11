@extends('layouts.admin')

@section('content')
<div class="py-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Service</h1>
            <p class="text-gray-600 mt-1">Update service details and settings</p>
        </div>
        <a
            href="{{ route('admin.services.index') }}"
            class="px-6 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition duration-200 font-medium"
        >
            Back
        </a>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-lg shadow-sm p-8">
        <form action="{{ route('admin.services.update', $service->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Basic Information Section -->
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-6">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                            Title
                            <span class="text-red-600">*</span>
                        </label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title', $service->title) }}"
                            placeholder="Enter service title"
                            class="w-full px-4 py-2 border @error('title') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            required
                        />
                        @error('title')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div>
                        <label for="slug" class="block text-sm font-semibold text-gray-700 mb-2">
                            Slug
                            <span class="text-red-600">*</span>
                        </label>
                        <input
                            type="text"
                            id="slug"
                            name="slug"
                            value="{{ old('slug', $service->slug) }}"
                            placeholder="service-slug"
                            class="w-full px-4 py-2 border @error('slug') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            required
                        />
                        @error('slug')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Icon (Font Awesome class) -->
                    <div>
                        <label for="icon" class="block text-sm font-semibold text-gray-700 mb-2">Icon Class</label>
                        <input
                            type="text"
                            id="icon"
                            name="icon"
                            value="{{ old('icon', $service->icon) }}"
                            placeholder="fas fa-briefcase"
                            class="w-full px-4 py-2 border @error('icon') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        />
                        <p class="text-gray-500 text-xs mt-1">Font Awesome icon class (e.g., fas fa-briefcase)</p>
                        @error('icon')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image Upload -->
                    <div>
                        <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">Image</label>
                        <input
                            type="file"
                            id="image"
                            name="image"
                            accept="image/*"
                            class="w-full px-4 py-2 border @error('image') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        />
                        <p class="text-gray-500 text-xs mt-1">Allowed formats: JPG, PNG, GIF, WebP</p>
                        @error('image')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Current Image Display -->
                @if($service->image)
                    <div class="mt-6 pt-6 border-t">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Current Image</p>
                        <div class="flex items-start gap-4">
                            <img
                                src="{{ asset('storage/' . $service->image) }}"
                                alt="{{ $service->title }}"
                                class="w-24 h-24 rounded-lg object-cover shadow-md"
                            />
                            <div>
                                <p class="text-sm text-gray-600 mb-2">{{ $service->image }}</p>
                                <label class="flex items-center text-sm">
                                    <input
                                        type="checkbox"
                                        name="remove_image"
                                        value="1"
                                        class="w-4 h-4 text-red-600 rounded border-gray-300 focus:ring-red-500 cursor-pointer"
                                    />
                                    <span class="ml-2 text-red-600 font-medium">Remove image</span>
                                </label>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <hr />

            <!-- Description Section -->
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-6">Descriptions</h2>
                <div class="space-y-6">
                    <!-- Short Description -->
                    <div>
                        <label for="short_description" class="block text-sm font-semibold text-gray-700 mb-2">
                            Short Description
                            <span class="text-red-600">*</span>
                        </label>
                        <input
                            type="text"
                            id="short_description"
                            name="short_description"
                            value="{{ old('short_description', $service->short_description) }}"
                            placeholder="Brief description of the service"
                            class="w-full px-4 py-2 border @error('short_description') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            required
                        />
                        @error('short_description')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Full Description -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                            Full Description
                            <span class="text-red-600">*</span>
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="8"
                            placeholder="Detailed description of the service"
                            class="w-full px-4 py-2 border @error('description') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            required
                        >{{ old('description', $service->description) }}</textarea>
                        @error('description')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <hr />

            <!-- Features Section -->
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-6">Features</h2>
                <div class="space-y-3" id="features-container">
                    @php
                        $features = old('features') ?? ($service->features ?? []);
                        if (is_string($features)) {
                            $features = json_decode($features, true) ?? [];
                        }
                    @endphp
                    @foreach($features as $index => $feature)
                        <div class="feature-item flex gap-2">
                            <input
                                type="text"
                                name="features[]"
                                value="{{ $feature }}"
                                placeholder="Add a feature"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            />
                            <button
                                type="button"
                                onclick="removeFeature(this)"
                                class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
                <button
                    type="button"
                    onclick="addFeature()"
                    class="mt-4 px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition font-medium flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Feature
                </button>
            </div>

            <hr />

            <!-- Pricing Section -->
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-6">Pricing</h2>
                <div>
                    <label for="price_starting_at" class="block text-sm font-semibold text-gray-700 mb-2">
                        Price Starting At
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-2 text-gray-600 font-medium">$</span>
                        <input
                            type="number"
                            id="price_starting_at"
                            name="price_starting_at"
                            value="{{ old('price_starting_at', $service->price_starting_at) }}"
                            placeholder="0.00"
                            step="0.01"
                            min="0"
                            class="w-full pl-8 pr-4 py-2 border @error('price_starting_at') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        />
                    </div>
                    @error('price_starting_at')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <hr />

            <!-- Status Section -->
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-6">Status & Visibility</h2>
                <div class="space-y-4">
                    <!-- Featured Checkbox -->
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            name="is_featured"
                            value="1"
                            {{ old('is_featured', $service->is_featured) ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500 cursor-pointer"
                        />
                        <span class="ml-3 text-gray-700 font-medium">Featured Service</span>
                    </label>

                    <!-- Active Checkbox -->
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            {{ old('is_active', $service->is_active) ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500 cursor-pointer"
                        />
                        <span class="ml-3 text-gray-700 font-medium">Active</span>
                    </label>
                </div>
            </div>

            <hr />

            <!-- Display Order Section -->
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-6">Display Settings</h2>
                <div>
                    <label for="display_order" class="block text-sm font-semibold text-gray-700 mb-2">
                        Display Order
                    </label>
                    <input
                        type="number"
                        id="display_order"
                        name="display_order"
                        value="{{ old('display_order', $service->display_order ?? 0) }}"
                        min="0"
                        placeholder="0"
                        class="w-full px-4 py-2 border @error('display_order') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    />
                    <p class="text-gray-500 text-xs mt-1">Lower numbers appear first</p>
                    @error('display_order')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <hr />

            <!-- SEO Section -->
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-6">SEO & Meta Information</h2>
                <div class="space-y-6">
                    <!-- Meta Title -->
                    <div>
                        <label for="meta_title" class="block text-sm font-semibold text-gray-700 mb-2">
                            Meta Title
                        </label>
                        <input
                            type="text"
                            id="meta_title"
                            name="meta_title"
                            value="{{ old('meta_title', $service->meta_title) }}"
                            placeholder="SEO meta title"
                            maxlength="60"
                            class="w-full px-4 py-2 border @error('meta_title') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        />
                        <p class="text-gray-500 text-xs mt-1">Recommended: 50-60 characters</p>
                        @error('meta_title')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Meta Description -->
                    <div>
                        <label for="meta_description" class="block text-sm font-semibold text-gray-700 mb-2">
                            Meta Description
                        </label>
                        <textarea
                            id="meta_description"
                            name="meta_description"
                            rows="3"
                            placeholder="SEO meta description"
                            maxlength="160"
                            class="w-full px-4 py-2 border @error('meta_description') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        >{{ old('meta_description', $service->meta_description) }}</textarea>
                        <p class="text-gray-500 text-xs mt-1">Recommended: 150-160 characters</p>
                        @error('meta_description')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4 pt-8 border-t">
                <button
                    type="submit"
                    class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 font-medium"
                >
                    Update Service
                </button>
                <a
                    href="{{ route('admin.services.index') }}"
                    class="flex-1 px-6 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition duration-200 font-medium text-center"
                >
                    Cancel
                </a>
                <button
                    type="button"
                    onclick="confirmDelete()"
                    class="px-6 py-3 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition duration-200 font-medium flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                </button>
            </div>
        </form>

        <!-- Delete Form (Hidden) -->
        <form id="delete-form" action="{{ route('admin.services.destroy', $service->id) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<script>
function addFeature() {
    const container = document.getElementById('features-container');
    const newItem = document.createElement('div');
    newItem.className = 'feature-item flex gap-2';
    newItem.innerHTML = `
        <input
            type="text"
            name="features[]"
            value=""
            placeholder="Add a feature"
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
        />
        <button
            type="button"
            onclick="removeFeature(this)"
            class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    `;
    container.appendChild(newItem);
}

function removeFeature(button) {
    const container = document.getElementById('features-container');
    if (container.children.length > 0) {
        button.parentElement.remove();
    }
}

function confirmDelete() {
    if (confirm('Are you sure you want to delete this service? This action cannot be undone.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endsection
