@extends('layouts.admin')

@section('title', $document->title)
@section('header', $document->title)

@section('header-actions')
    <div class="flex space-x-3">
        <a href="{{ route('admin.documents.download', $document) }}" class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">Download</a>
        <a href="{{ route('admin.documents.edit', $document) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Edit</a>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="rounded-lg bg-white shadow p-6">
                <h3 class="text-lg font-medium mb-4">Document Information</h3>
                <dl class="grid grid-cols-2 gap-4">
                    <div><dt class="text-sm font-medium text-gray-500">Title</dt><dd class="mt-1 text-sm text-gray-900">{{ $document->title }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">Type</dt><dd class="mt-1 text-sm text-gray-900">{{ ucfirst($document->document_type) }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">File Name</dt><dd class="mt-1 text-sm text-gray-900">{{ $document->file_name }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">File Size</dt><dd class="mt-1 text-sm text-gray-900">{{ number_format($document->file_size / 1024, 2) }} KB</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">MIME Type</dt><dd class="mt-1 text-sm text-gray-900">{{ $document->mime_type }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">Uploaded</dt><dd class="mt-1 text-sm text-gray-900">{{ $document->created_at->format('M d, Y g:i A') }}</dd></div>
                    @if($document->description)
                        <div class="col-span-2"><dt class="text-sm font-medium text-gray-500">Description</dt><dd class="mt-1 text-sm text-gray-900">{{ $document->description }}</dd></div>
                    @endif
                </dl>
            </div>
        </div>
        <div>
            <div class="rounded-lg bg-white shadow p-6 space-y-4">
                <div><dt class="text-sm font-medium text-gray-500">Company</dt><dd class="mt-1 text-sm text-gray-900">{{ $document->company->name ?? 'N/A' }}</dd></div>
                @if($document->project)
                    <div><dt class="text-sm font-medium text-gray-500">Project</dt><dd class="mt-1 text-sm text-gray-900">{{ $document->project->name }}</dd></div>
                @endif
                <div><dt class="text-sm font-medium text-gray-500">Uploaded By</dt><dd class="mt-1 text-sm text-gray-900">{{ $document->uploadedBy->name }}</dd></div>
            </div>
            <div class="mt-6 rounded-lg bg-white shadow p-6 space-y-3">
                <a href="{{ route('admin.documents.download', $document) }}" class="block w-full text-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-500">Download</a>
                <a href="{{ route('admin.documents.edit', $document) }}" class="block w-full text-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Edit</a>
                <form method="POST" action="{{ route('admin.documents.destroy', $document) }}" onsubmit="return confirm('Delete this document?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">Delete</button>
                </form>
            </div>
        </div>
    </div>
@endsection
