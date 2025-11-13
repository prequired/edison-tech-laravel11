@extends('layouts.admin')

@section('title', 'Edit Portfolio Item')
@section('header', 'Edit Portfolio Item')

@section('content')
    <div class="mx-auto max-w-3xl">
        <form method="POST" action="{{ route('admin.portfolio.update', $item) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="rounded-lg bg-white shadow p-6 space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $item->title) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-300 @enderror">
                    @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700">Slug *</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $item->slug) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('slug') border-red-300 @enderror">
                    @error('slug')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                    <textarea name="description" id="description" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-300 @enderror">{{ old('description', $item->description) }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                    <textarea name="content" id="content" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('content') border-red-300 @enderror">{{ old('content', $item->content) }}</textarea>
                    @error('content')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Category *</label>
                    <input type="text" name="category" id="category" value="{{ old('category', $item->category) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('category') border-red-300 @enderror">
                    @error('category')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="client_name" class="block text-sm font-medium text-gray-700">Client Name</label>
                    <input type="text" name="client_name" id="client_name" value="{{ old('client_name', $item->client_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="project_url" class="block text-sm font-medium text-gray-700">Project URL</label>
                    <input type="url" name="project_url" id="project_url" value="{{ old('project_url', $item->project_url) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                @if($item->featured_image)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Current Image</label>
                        <img src="{{ asset('storage/' . $item->featured_image) }}" class="mt-2 h-32 w-32 object-cover rounded">
                        <label class="mt-2 flex items-center">
                            <input type="checkbox" name="remove_image" value="1" class="h-4 w-4 rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Remove image</span>
                        </label>
                    </div>
                @endif

                <div>
                    <label for="featured_image" class="block text-sm font-medium text-gray-700">{{ $item->featured_image ? 'Replace' : 'Featured' }} Image</label>
                    <input type="file" name="featured_image" id="featured_image" accept="image/*" class="mt-1 block w-full text-sm">
                </div>

                <div>
                    <label for="technologies" class="block text-sm font-medium text-gray-700">Technologies (comma-separated)</label>
                    <input type="text" name="technologies" id="technologies" value="{{ old('technologies', is_array($item->technologies) ? implode(', ', $item->technologies) : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                    <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="published" {{ old('status', $item->status) == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ old('status', $item->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $item->is_featured) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600">
                    <label for="is_featured" class="ml-2 block text-sm text-gray-700">Featured</label>
                </div>
            </div>

            <div class="flex justify-between">
                <form method="POST" action="{{ route('admin.portfolio.destroy', $item) }}" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">Delete</button>
                </form>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.portfolio.show', $item) }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Update</button>
                </div>
            </div>
        </form>
    </div>
@endsection
