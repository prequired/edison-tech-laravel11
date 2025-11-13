@extends('layouts.admin')

@section('title', 'Create Blog Category')
@section('header', 'Create Blog Category')

@section('content')
    <div class="mx-auto max-w-3xl">
        <form method="POST" action="{{ route('admin.blog-categories.store') }}" class="space-y-6">
            @csrf

            <!-- Basic Information Card -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="border-b border-gray-200 bg-white px-6 py-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Basic Information</h3>
                </div>
                <div class="px-6 py-5 space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700">
                            Slug <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-50 px-3 text-sm text-gray-500">
                                /blog/category/
                            </span>
                            <input type="text" name="slug" id="slug" value="{{ old('slug') }}" required maxlength="255" class="block w-full min-w-0 flex-1 rounded-none rounded-r-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('slug') border-red-300 @enderror">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">URL-friendly version of the name. Letters, numbers, and hyphens only.</p>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="3" maxlength="500" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Brief description of this category (optional, max 500 characters).</p>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Color -->
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700">
                            Color
                        </label>
                        <div class="mt-1 flex items-center space-x-3">
                            <input type="color" name="color" id="color" value="{{ old('color', '#6366f1') }}" class="h-10 w-20 rounded border border-gray-300 @error('color') border-red-300 @enderror">
                            <input type="text" id="color-hex" value="{{ old('color', '#6366f1') }}" readonly class="block w-24 rounded-md border-gray-300 bg-gray-50 text-sm">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Category color for visual identification.</p>
                        @error('color')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror>
                    </div>

                    <!-- Display Order -->
                    <div>
                        <label for="order" class="block text-sm font-medium text-gray-700">
                            Display Order
                        </label>
                        <input type="number" name="order" id="order" value="{{ old('order', 0) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('order') border-red-300 @enderror">
                        <p class="mt-1 text-sm text-gray-500">Lower numbers appear first in lists.</p>
                        @error('order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-start">
                        <div class="flex h-5 items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_active" class="font-medium text-gray-700">Active</label>
                            <p class="text-gray-500">Make this category visible on the website.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.blog-categories.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Create Category
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // Auto-generate slug from name
        document.getElementById('name').addEventListener('input', function(e) {
            const slug = e.target.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            document.getElementById('slug').value = slug;
        });

        // Sync color picker with hex input
        document.getElementById('color').addEventListener('input', function(e) {
            document.getElementById('color-hex').value = e.target.value;
        });
    </script>
    @endpush
@endsection
