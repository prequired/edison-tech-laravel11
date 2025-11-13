@extends('layouts.admin')

@section('title', 'Documents')
@section('header', 'Documents')

@section('header-actions')
    <a href="{{ route('admin.documents.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Upload Document
    </a>
@endsection

@section('content')
    <div class="mb-6 rounded-lg bg-white p-4 shadow">
        <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-5">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search documents..." class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <select name="type" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">All Types</option>
                <option value="contract" {{ request('type') == 'contract' ? 'selected' : '' }}>Contract</option>
                <option value="invoice" {{ request('type') == 'invoice' ? 'selected' : '' }}>Invoice</option>
                <option value="proposal" {{ request('type') == 'proposal' ? 'selected' : '' }}>Proposal</option>
                <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
            <select name="company_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">All Companies</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Apply</button>
            <a href="{{ route('admin.documents.index') }}" class="rounded-md bg-gray-200 px-3 py-2 text-center text-sm font-semibold text-gray-700 hover:bg-gray-300">Clear</a>
        </form>
    </div>

    <div class="rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Document</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Company</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Size</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Uploaded</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($documents as $document)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">{{ $document->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $document->file_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ ucfirst($document->document_type) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $document->company->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ number_format($document->file_size / 1024, 2) }} KB</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $document->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm font-medium space-x-2">
                            <a href="{{ route('admin.documents.show', $document) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                            <a href="{{ route('admin.documents.download', $document) }}" class="text-green-600 hover:text-green-900">Download</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">No documents found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($documents->hasPages())
            <div class="border-t px-4 py-3">{{ $documents->withQueryString()->links() }}</div>
        @endif
    </div>
@endsection
