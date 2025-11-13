@extends('layouts.admin')

@section('title', 'Edit Testimonial')
@section('header', 'Edit Testimonial')

@section('content')
    <div class="mx-auto max-w-3xl">
        <form method="POST" action="{{ route('admin.testimonials.update', $testimonial) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Client Information Card -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="border-b border-gray-200 bg-white px-6 py-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Client Information</h3>
                </div>
                <div class="px-6 py-5 space-y-6">
                    <!-- Client Name -->
                    <div>
                        <label for="client_name" class="block text-sm font-medium text-gray-700">
                            Client Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="client_name" id="client_name" value="{{ old('client_name', $testimonial->client_name) }}" required maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('client_name') border-red-300 @enderror">
                        @error('client_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Client Position -->
                    <div>
                        <label for="client_position" class="block text-sm font-medium text-gray-700">
                            Position/Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="client_position" id="client_position" value="{{ old('client_position', $testimonial->client_position) }}" required maxlength="255" placeholder="e.g., CEO, Marketing Director" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('client_position') border-red-300 @enderror">
                        @error('client_position')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Company Name -->
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700">
                            Company Name
                        </label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $testimonial->company_name) }}" maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('company_name') border-red-300 @enderror">
                        @error('company_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Client Email -->
                    <div>
                        <label for="client_email" class="block text-sm font-medium text-gray-700">
                            Email Address
                        </label>
                        <input type="email" name="client_email" id="client_email" value="{{ old('client_email', $testimonial->client_email) }}" maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('client_email') border-red-300 @enderror">
                        <p class="mt-1 text-sm text-gray-500">Optional, for internal reference only.</p>
                        @error('client_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Image -->
                    @if($testimonial->image_url)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Current Photo</label>
                            <div class="mt-2 flex items-center">
                                <img src="{{ asset('storage/' . $testimonial->image_url) }}" alt="{{ $testimonial->client_name }}" class="h-16 w-16 rounded-full object-cover">
                                <div class="ml-4">
                                    <label for="remove_image" class="flex items-center">
                                        <input type="checkbox" name="remove_image" id="remove_image" value="1" class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                        <span class="ml-2 text-sm text-gray-700">Remove current photo</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Client Image -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700">
                            {{ $testimonial->image_url ? 'Replace Photo' : 'Client Photo' }}
                        </label>
                        <input type="file" name="image" id="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 @error('image') border-red-300 @enderror">
                        <p class="mt-1 text-sm text-gray-500">Recommended: Square image, at least 200x200px.</p>
                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Testimonial Content Card -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="border-b border-gray-200 bg-white px-6 py-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Testimonial Content</h3>
                </div>
                <div class="px-6 py-5 space-y-6">
                    <!-- Testimonial Text -->
                    <div>
                        <label for="testimonial" class="block text-sm font-medium text-gray-700">
                            Testimonial <span class="text-red-500">*</span>
                        </label>
                        <textarea name="testimonial" id="testimonial" rows="6" required maxlength="1000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('testimonial') border-red-300 @enderror">{{ old('testimonial', $testimonial->testimonial) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">The testimonial content (max 1000 characters).</p>
                        @error('testimonial')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Rating -->
                    <div>
                        <label for="rating" class="block text-sm font-medium text-gray-700">
                            Rating <span class="text-red-500">*</span>
                        </label>
                        <select name="rating" id="rating" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('rating') border-red-300 @enderror">
                            <option value="">Select rating</option>
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" {{ old('rating', $testimonial->rating) == $i ? 'selected' : '' }}>
                                    {{ $i }} {{ Str::plural('Star', $i) }}
                                </option>
                            @endfor
                        </select>
                        @error('rating')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Associated Project -->
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700">
                            Associated Project
                        </label>
                        <select name="project_id" id="project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('project_id') border-red-300 @enderror">
                            <option value="">None</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $testimonial->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }} ({{ $project->company->name }})
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Link this testimonial to a project (optional).</p>
                        @error('project_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Settings Card -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="border-b border-gray-200 bg-white px-6 py-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Display Settings</h3>
                </div>
                <div class="px-6 py-5 space-y-6">
                    <!-- Display Order -->
                    <div>
                        <label for="display_order" class="block text-sm font-medium text-gray-700">
                            Display Order
                        </label>
                        <input type="number" name="display_order" id="display_order" value="{{ old('display_order', $testimonial->display_order ?? 0) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('display_order') border-red-300 @enderror">
                        <p class="mt-1 text-sm text-gray-500">Lower numbers appear first.</p>
                        @error('display_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('status') border-red-300 @enderror">
                            <option value="approved" {{ old('status', $testimonial->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="pending" {{ old('status', $testimonial->status) == 'pending' ? 'selected' : '' }}>Pending Review</option>
                            <option value="rejected" {{ old('status', $testimonial->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Featured -->
                    <div class="flex items-start">
                        <div class="flex h-5 items-center">
                            <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $testimonial->is_featured) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_featured" class="font-medium text-gray-700">Featured</label>
                            <p class="text-gray-500">Display this testimonial prominently on the website.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-between">
                <button type="button" onclick="document.getElementById('delete-form').submit();" class="inline-flex justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                    Delete Testimonial
                </button>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.testimonials.show', $testimonial) }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        Update Testimonial
                    </button>
                </div>
            </div>
        </form>

        <!-- Delete Form (hidden) -->
        <form id="delete-form" method="POST" action="{{ route('admin.testimonials.destroy', $testimonial) }}" class="hidden" onsubmit="return confirm('Are you sure you want to delete this testimonial? This action cannot be undone.');">
            @csrf
            @method('DELETE')
        </form>
    </div>
@endsection
