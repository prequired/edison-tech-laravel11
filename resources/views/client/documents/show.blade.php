@extends('layouts.app')

@section('title', $document->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Back Link -->
        <div class="mb-6">
            <a href="{{ route('client.documents.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Documents
            </a>
        </div>

        <!-- Main Document Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
            <!-- Header with Download Button -->
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-8">
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-4">
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
                        <div class="text-5xl">{{ $icon }}</div>
                        <div>
                            <h1 class="text-3xl font-bold text-white mb-2">{{ $document->name }}</h1>
                            <p class="text-indigo-100 text-lg">{{ strtoupper($document->type ?? 'Document') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('client.documents.download', $document->id) }}" class="inline-flex items-center px-8 py-4 bg-white text-indigo-600 font-bold rounded-lg hover:bg-indigo-50 transition-colors shadow-lg hover:shadow-xl">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download Now
                    </a>
                </div>
            </div>

            <!-- Document Information Grid -->
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Left Column -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-200">Document Information</h2>

                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">File Type</p>
                                <p class="text-gray-900 font-medium text-lg">{{ $document->type ?? 'Unknown' }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">File Size</p>
                                <p class="text-gray-900 font-medium text-lg">{{ number_format($document->size / 1024 / 1024, 2) }} MB</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Uploaded Date</p>
                                <p class="text-gray-900 font-medium text-lg">{{ $document->created_at->format('F d, Y \a\t g:i A') }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Current Version</p>
                                <p class="text-gray-900 font-medium text-lg">v{{ $document->version ?? '1' }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Uploaded By</p>
                                <div class="flex items-center gap-2 mt-2">
                                    <img src="{{ $document->uploadedBy?->avatar_url ?? 'https://via.placeholder.com/32' }}" alt="{{ $document->uploadedBy?->name }}" class="w-8 h-8 rounded-full">
                                    <p class="text-gray-900 font-medium">{{ $document->uploadedBy?->name ?? 'Unknown' }}</p>
                                </div>
                            </div>
                            @if($document->description)
                                <div>
                                    <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Description</p>
                                    <p class="text-gray-700 mt-2">{{ $document->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div>
                        @if($document->project)
                            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-200">Associated Project</h2>

                            <div class="bg-indigo-50 rounded-lg p-4 mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $document->project->name }}</h3>
                                <p class="text-sm text-gray-600 mb-3">{{ $document->project->description ?? 'No description available' }}</p>

                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Status:</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ ucfirst($document->project->status ?? 'active') }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Start Date:</span>
                                        <span class="text-gray-900 font-medium">{{ $document->project->start_date?->format('M d, Y') ?? 'N/A' }}</span>
                                    </div>
                                </div>

                                <a href="{{ route('client.projects.show', $document->project->id) }}" class="inline-block mt-4 text-indigo-600 hover:text-indigo-700 font-medium text-sm">
                                    View Project →
                                </a>
                            </div>
                        @endif

                        @if($document->company)
                            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-200">Company</h2>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center gap-3 mb-3">
                                    @if($document->company->logo_url)
                                        <img src="{{ $document->company->logo_url }}" alt="{{ $document->company->name }}" class="w-12 h-12 rounded-lg">
                                    @else
                                        <div class="w-12 h-12 bg-indigo-200 rounded-lg flex items-center justify-center">
                                            <span class="text-indigo-600 font-bold text-lg">{{ substr($document->company->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $document->company->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $document->company->industry ?? 'Company' }}</p>
                                    </div>
                                </div>

                                <a href="{{ route('client.company.show') }}" class="inline-block text-indigo-600 hover:text-indigo-700 font-medium text-sm">
                                    View Company Profile →
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Version History Section -->
                @if($versions && $versions->count() > 1)
                    <div class="mt-12 pt-8 border-t border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">Version History</h2>

                        <div class="space-y-3">
                            @forelse($versions->sortByDesc('created_at') as $version)
                                <div class="flex items-start justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">Version {{ $version->version }}</p>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Uploaded {{ $version->created_at->diffForHumans() }}
                                            @if($version->uploadedBy)
                                                by <span class="font-medium">{{ $version->uploadedBy->name }}</span>
                                            @endif
                                        </p>
                                        @if($version->notes)
                                            <p class="text-sm text-gray-600 mt-2">{{ $version->notes }}</p>
                                        @endif
                                    </div>
                                    <a href="{{ route('client.documents.download-version', $version->id) }}" class="ml-4 text-indigo-600 hover:text-indigo-700 font-medium text-sm whitespace-nowrap">
                                        Download
                                    </a>
                                </div>
                            @empty
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4">
            <a href="{{ route('client.documents.index') }}" class="flex-1 inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to List
            </a>
            <a href="{{ route('client.documents.download', $document->id) }}" class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download Document
            </a>
        </div>
    </div>
</div>
@endsection
