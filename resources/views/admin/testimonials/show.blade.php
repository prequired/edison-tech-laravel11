@extends('layouts.admin')

@section('title', 'Testimonial Details')
@section('header', 'Testimonial Details')

@section('header-actions')
    <div class="flex space-x-3">
        @if($testimonial->status === 'pending')
            <form method="POST" action="{{ route('admin.testimonials.approve', $testimonial) }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Approve
                </button>
            </form>
        @endif
        <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
            </svg>
            Edit
        </a>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Testimonial Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Main Testimonial Card -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="px-6 py-8">
                    <!-- Author Info -->
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0">
                            @if($testimonial->image_url)
                                <img src="{{ asset('storage/' . $testimonial->image_url) }}" alt="{{ $testimonial->client_name }}" class="h-16 w-16 rounded-full object-cover">
                            @else
                                <div class="h-16 w-16 rounded-full bg-indigo-600 flex items-center justify-center">
                                    <span class="text-2xl font-medium text-white">{{ substr($testimonial->client_name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">{{ $testimonial->client_name }}</h3>
                            <p class="text-sm text-gray-500">
                                {{ $testimonial->client_position }}{{ $testimonial->company_name ? ' at ' . $testimonial->company_name : '' }}
                            </p>
                            @if($testimonial->client_email)
                                <a href="mailto:{{ $testimonial->client_email }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                                    {{ $testimonial->client_email }}
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Rating -->
                    <div class="mb-6">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $testimonial->rating)
                                    <svg class="h-6 w-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @else
                                    <svg class="h-6 w-6 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endif
                            @endfor
                            <span class="ml-2 text-sm font-medium text-gray-700">{{ $testimonial->rating }}/5</span>
                        </div>
                    </div>

                    <!-- Testimonial Text -->
                    <div class="prose prose-indigo max-w-none">
                        <blockquote class="text-gray-700 text-lg leading-relaxed border-l-4 border-indigo-600 pl-4">
                            "{{ $testimonial->testimonial }}"
                        </blockquote>
                    </div>
                </div>
            </div>

            <!-- Associated Project -->
            @if($testimonial->project)
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 bg-white px-6 py-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Associated Project</h3>
                    </div>
                    <div class="px-6 py-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-base font-medium text-gray-900">{{ $testimonial->project->name }}</h4>
                                <p class="text-sm text-gray-500">{{ $testimonial->project->company->name }}</p>
                                <div class="mt-2">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $testimonial->project->status->badge() }}">
                                        {{ $testimonial->project->status->label() }}
                                    </span>
                                </div>
                            </div>
                            <a href="{{ route('admin.projects.show', $testimonial->project) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                View Project
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="border-b border-gray-200 bg-white px-6 py-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Status</h3>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Approval Status</dt>
                        <dd class="mt-1">
                            @if($testimonial->status === 'approved')
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                    Approved
                                </span>
                            @elseif($testimonial->status === 'pending')
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">
                                    Pending Review
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                    Rejected
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Featured</dt>
                        <dd class="mt-1">
                            @if($testimonial->is_featured)
                                <span class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800">
                                    Yes
                                </span>
                            @else
                                <span class="text-sm text-gray-900">No</span>
                            @endif
                        </dd>
                    </div>
                    @if($testimonial->approved_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Approved Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $testimonial->approved_at->format('M d, Y g:i A') }}</dd>
                        </div>
                    @endif
                    @if($testimonial->approved_by)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Approved By</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $testimonial->approver->name }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Metadata Card -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="border-b border-gray-200 bg-white px-6 py-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Metadata</h3>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Display Order</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $testimonial->display_order ?? 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $testimonial->created_at->format('M d, Y g:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $testimonial->updated_at->format('M d, Y g:i A') }}</dd>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="border-b border-gray-200 bg-white px-6 py-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Actions</h3>
                </div>
                <div class="px-6 py-5 space-y-3">
                    @if($testimonial->status === 'pending')
                        <form method="POST" action="{{ route('admin.testimonials.approve', $testimonial) }}">
                            @csrf
                            <button type="submit" class="w-full rounded-md bg-green-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                                Approve Testimonial
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.testimonials.reject', $testimonial) }}">
                            @csrf
                            <button type="submit" class="w-full rounded-md bg-red-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-red-500" onclick="return confirm('Are you sure you want to reject this testimonial?');">
                                Reject Testimonial
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="block w-full rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        Edit Testimonial
                    </a>
                    <form method="POST" action="{{ route('admin.testimonials.destroy', $testimonial) }}" class="block" onsubmit="return confirm('Are you sure you want to delete this testimonial? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full rounded-md bg-white px-3 py-2 text-center text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                            Delete Testimonial
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
