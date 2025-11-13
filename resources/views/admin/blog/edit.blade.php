@extends('layouts.admin')

@section('title', 'Edit ' . $blog->title)

@section('header', 'Edit: ' . $blog->title)

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="rounded-lg bg-white shadow">
                <form method="POST" action="{{ route('admin.blog.update', $blog) }}" enctype="multipart/form-data" class="space-y-6 px-6 py-8">
                    @csrf
                    @method('PUT')

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-900">Category <span class="text-red-500">*</span></label>
                        <select name="category_id" id="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2 {{ $errors->has('category_id') ? 'border-red-500' : '' }}" required>
                            <option value="">Select a category</option>
                            @foreach ($categories ?? [] as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $blog->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-900">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title', $blog->title) }}" placeholder="Enter post title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2 {{ $errors->has('title') ? 'border-red-500' : '' }}" required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-900">Slug <span class="text-red-500">*</span></label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug', $blog->slug) }}" placeholder="post-slug" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2 {{ $errors->has('slug') ? 'border-red-500' : '' }}" required>
                        <p class="mt-1 text-xs text-gray-500">URL-friendly version of the title</p>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Excerpt -->
                    <div>
                        <label for="excerpt" class="block text-sm font-medium text-gray-900">Excerpt</label>
                        <textarea name="excerpt" id="excerpt" rows="3" placeholder="Brief summary of the post" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2 {{ $errors->has('excerpt') ? 'border-red-500' : '' }}">{{ old('excerpt', $blog->excerpt) }}</textarea>
                        @error('excerpt')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Content -->
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-900">Content <span class="text-red-500">*</span></label>
                        <textarea name="content" id="content" rows="12" placeholder="Write your post content here (Markdown supported)" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2 font-mono {{ $errors->has('content') ? 'border-red-500' : '' }}" required>{{ old('content', $blog->content) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Markdown formatting is supported</p>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Featured Image -->
                    <div>
                        <label for="featured_image" class="block text-sm font-medium text-gray-900">Featured Image</label>

                        <!-- Current Image -->
                        @if ($blog->featured_image)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">Current Image:</p>
                                <div class="relative w-48 h-32 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center">
                                    <img src="{{ asset('storage/' . $blog->featured_image) }}" alt="{{ $blog->title }}" class="w-full h-full object-cover">
                                    <button type="button" onclick="document.getElementById('removeImage').value = '1'; document.getElementById('removeImageLabel').textContent = 'Image will be removed';" class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <input type="hidden" name="remove_image" id="removeImage" value="0">
                                <p id="removeImageLabel" class="mt-1 text-xs text-gray-500"></p>
                            </div>
                        @endif

                        <!-- Upload New Image -->
                        <div class="flex justify-center rounded-lg border-2 border-dashed border-gray-300 px-6 py-10 {{ $errors->has('featured_image') ? 'border-red-500' : '' }}">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20a4 4 0 004 4h24a4 4 0 004-4V16a4 4 0 00-4-4h-8l-4-4z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">
                                    <span class="font-semibold">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                <input type="file" name="featured_image" id="featured_image" accept="image/*" class="sr-only" onchange="previewNewImage(event)">
                                <label for="featured_image" class="mt-3 inline-flex items-center rounded-md bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-600 cursor-pointer hover:bg-indigo-100">
                                    Select Image
                                </label>
                            </div>
                        </div>
                        <div id="newImagePreview" class="mt-4 hidden">
                            <p class="text-sm text-gray-600 mb-2">New Image Preview:</p>
                            <img id="newPreviewImg" src="" alt="Preview" class="rounded-lg max-h-64 max-w-sm">
                        </div>
                        @error('featured_image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-between border-t border-gray-200 pt-6">
                        <a href="{{ route('admin.blog.index') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Update Post
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Status & Publishing -->
            <div class="rounded-lg bg-white shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Publishing</h3>
                <div class="space-y-4">
                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2 {{ $errors->has('status') ? 'border-red-500' : '' }}">
                            <option value="draft" {{ old('status', $blog->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $blog->status) === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="scheduled" {{ old('status', $blog->status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Is Featured -->
                    <div class="flex items-center">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $blog->is_featured) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="is_featured" class="ml-2 text-sm text-gray-700">Mark as Featured</label>
                    </div>

                    <!-- Published At -->
                    <div>
                        <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">Published At</label>
                        <input type="datetime-local" name="published_at" id="published_at" value="{{ old('published_at', $blog->published_at ? $blog->published_at->format('Y-m-d\TH:i') : '') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2 {{ $errors->has('published_at') ? 'border-red-500' : '' }}">
                        @error('published_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Tags -->
            <div class="rounded-lg bg-white shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tags</h3>
                <div class="space-y-2">
                    <input type="text" name="tags" id="tags" placeholder="Enter tags separated by commas" value="{{ old('tags', $blog->tags ? $blog->tags->pluck('name')->implode(', ') : '') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2">
                    <p class="text-xs text-gray-500">Add multiple tags separated by commas</p>
                    @error('tags')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- SEO -->
            <div class="rounded-lg bg-white shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">SEO</h3>
                <div class="space-y-4">
                    <!-- Meta Title -->
                    <div>
                        <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $blog->meta_title) }}" placeholder="SEO title" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2" maxlength="60">
                        <p class="mt-1 text-xs text-gray-500">Max 60 characters</p>
                        @error('meta_title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Meta Description -->
                    <div>
                        <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" rows="3" placeholder="SEO description" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2" maxlength="160">{{ old('meta_description', $blog->meta_description) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Max 160 characters</p>
                        @error('meta_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="rounded-lg bg-white shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Post Info</h3>
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
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Word Count</p>
                        <p class="mt-1 text-gray-900">{{ str_word_count($blog->content) }} words</p>
                    </div>
                </div>
            </div>

            <!-- Delete Section -->
            <div class="rounded-lg bg-red-50 shadow p-6">
                <h3 class="text-lg font-medium text-red-900 mb-2">Danger Zone</h3>
                <p class="text-sm text-red-800 mb-4">Once you delete this post, there is no going back. Please be certain.</p>
                <form method="POST" action="{{ route('admin.blog.destroy', $blog) }}" onsubmit="return confirm('Are you sure you want to delete this post? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex items-center justify-center rounded-md border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 shadow-sm hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        Delete Post
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewNewImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('newImagePreview');
            const previewImg = document.getElementById('newPreviewImg');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('hidden');
            }
        }

        // Reset remove_image flag when a new file is selected
        document.getElementById('featured_image').addEventListener('change', function() {
            document.getElementById('removeImage').value = '0';
            document.getElementById('removeImageLabel').textContent = '';
        });
    </script>
@endsection
