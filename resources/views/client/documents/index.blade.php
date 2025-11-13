@extends('layouts.app')

@section('title', 'My Documents')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">My Documents</h1>
            <p class="text-gray-600">Manage and download your project documents</p>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Document Type Filter -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Document Type
                    </label>
                    <select id="type" name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">All Types</option>
                        <option value="pdf">PDF</option>
                        <option value="docx">Word Document</option>
                        <option value="xlsx">Spreadsheet</option>
                        <option value="image">Image</option>
                        <option value="video">Video</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Project Filter -->
                <div>
                    <label for="project" class="block text-sm font-medium text-gray-700 mb-2">
                        Project
                    </label>
                    <select id="project" name="project" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">All Projects</option>
                        @forelse($documents->groupBy('project.id') as $projectId => $docs)
                            <option value="{{ $projectId }}">{{ $docs->first()->project?->name ?? 'No Project' }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        Search Documents
                    </label>
                    <input type="text" id="search" placeholder="Search by name..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- Documents Grid -->
        @if($documents->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($documents as $document)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 overflow-hidden">
                        <!-- Document Header -->
                        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $icon = match(strtolower($document->type ?? 'other')) {
                                                'pdf' => '📄',
                                                'docx', 'doc' => '📝',
                                                'xlsx', 'xls' => '📊',
                                                'pptx', 'ppt' => '📽️',
                                                'jpg', 'jpeg', 'png', 'gif' => '🖼️',
                                                'mp4', 'avi', 'mov', 'mkv' => '🎬',
                                                'zip', 'rar' => '📦',
                                                default => '📎'
                                            };
                                        @endphp
                                        <span class="text-3xl">{{ $icon }}</span>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white bg-opacity-20 text-white">
                                    {{ strtoupper($document->type ?? 'Other') }}
                                </span>
                            </div>
                        </div>

                        <!-- Document Content -->
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1 truncate">
                                <a href="{{ route('client.documents.show', $document->id) }}" class="hover:text-indigo-600 transition-colors">
                                    {{ $document->name }}
                                </a>
                            </h3>

                            @if($document->project)
                                <p class="text-sm text-gray-500 mb-3">
                                    Project: <span class="font-medium text-gray-700">{{ $document->project->name }}</span>
                                </p>
                            @endif

                            <div class="grid grid-cols-2 gap-3 mb-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Size</p>
                                    <p class="font-medium text-gray-900">{{ number_format($document->size / 1024 / 1024, 2) }} MB</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Uploaded</p>
                                    <p class="font-medium text-gray-900">{{ $document->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>

                            @if($document->description)
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $document->description }}</p>
                            @endif
                        </div>

                        <!-- Document Footer -->
                        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200">
                            <span class="text-xs text-gray-500">
                                v{{ $document->version ?? '1' }}
                            </span>
                            <a href="{{ route('client.documents.download', $document->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No documents found</h3>
                            <p class="text-gray-500">Start by uploading your first document or contact your administrator</p>
                        </div>
                    </div>
                @endforelse
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No documents available</h3>
                <p class="text-gray-500">You don't have any documents yet. Contact your administrator to get started.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Filter functionality
    document.getElementById('type').addEventListener('change', filterDocuments);
    document.getElementById('project').addEventListener('change', filterDocuments);
    document.getElementById('search').addEventListener('input', filterDocuments);

    function filterDocuments() {
        // Add your filter logic here
        console.log('Filter triggered');
    }
</script>
@endpush
@endsection
