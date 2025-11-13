@extends('layouts.admin')

@section('title', 'Edit Document')
@section('header', 'Edit Document')

@section('content')
    <div class="mx-auto max-w-3xl">
        <form method="POST" action="{{ route('admin.documents.update', $document) }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="rounded-lg bg-white shadow p-6 space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $document->title) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-300 @enderror">
                    @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $document->description) }}</textarea>
                </div>

                <div>
                    <label for="document_type" class="block text-sm font-medium text-gray-700">Document Type *</label>
                    <select name="document_type" id="document_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="contract" {{ old('document_type', $document->document_type) == 'contract' ? 'selected' : '' }}>Contract</option>
                        <option value="invoice" {{ old('document_type', $document->document_type) == 'invoice' ? 'selected' : '' }}>Invoice</option>
                        <option value="proposal" {{ old('document_type', $document->document_type) == 'proposal' ? 'selected' : '' }}>Proposal</option>
                        <option value="report" {{ old('document_type', $document->document_type) == 'report' ? 'selected' : '' }}>Report</option>
                        <option value="other" {{ old('document_type', $document->document_type) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label for="company_id" class="block text-sm font-medium text-gray-700">Company</label>
                    <select name="company_id" id="company_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select company</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id', $document->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700">Project</label>
                    <select name="project_id" id="project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $document->project_id) == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="rounded-md bg-blue-50 p-4">
                    <p class="text-sm text-blue-800">Current file: <strong>{{ $document->file_name }}</strong> ({{ number_format($document->file_size / 1024, 2) }} KB)</p>
                    <p class="text-xs text-blue-600 mt-1">To replace the file, upload a new version using the "Upload Version" feature.</p>
                </div>
            </div>

            <div class="flex justify-between">
                <form method="POST" action="{{ route('admin.documents.destroy', $document) }}" onsubmit="return confirm('Delete this document?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">Delete</button>
                </form>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.documents.show', $document) }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Update</button>
                </div>
            </div>
        </form>
    </div>
@endsection
