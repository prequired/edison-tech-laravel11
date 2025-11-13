@extends('layouts.admin')

@section('title', 'Contact Submissions')
@section('header', 'Contact Submissions')

@section('content')
    <div class="mb-6 grid grid-cols-1 gap-5 sm:grid-cols-4">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="text-sm font-medium text-gray-500">Total</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $total }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="text-sm font-medium text-gray-500">Unread</dt>
            <dd class="mt-1 text-3xl font-semibold text-red-600">{{ $unread }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="text-sm font-medium text-gray-500">Replied</dt>
            <dd class="mt-1 text-3xl font-semibold text-green-600">{{ $replied }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="text-sm font-medium text-gray-500">Archived</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-600">{{ $archived }}</dd>
        </div>
    </div>

    <div class="mb-6 rounded-lg bg-white p-4 shadow">
        <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">All Status</option>
                <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread</option>
                <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Replied</option>
                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
            </select>
            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Apply</button>
            <a href="{{ route('admin.contacts.index') }}" class="rounded-md bg-gray-200 px-3 py-2 text-center text-sm font-semibold text-gray-700 hover:bg-gray-300">Clear</a>
        </form>
    </div>

    <div class="rounded-lg bg-white shadow">
        <ul role="list" class="divide-y divide-gray-200">
            @forelse($contacts as $contact)
                <li class="px-6 py-4 hover:bg-gray-50 {{ $contact->status === 'unread' ? 'bg-blue-50' : '' }}">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-3">
                                <h3 class="text-sm font-medium text-gray-900 truncate">{{ $contact->name }}</h3>
                                @if($contact->status === 'unread')
                                    <span class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800">Unread</span>
                                @elseif($contact->status === 'replied')
                                    <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">Replied</span>
                                @endif
                            </div>
                            <div class="mt-1 flex items-center space-x-4">
                                <span class="text-sm text-gray-500">{{ $contact->email }}</span>
                                @if($contact->phone)
                                    <span class="text-sm text-gray-500">{{ $contact->phone }}</span>
                                @endif
                                <span class="text-sm text-gray-400">{{ $contact->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="mt-1 text-sm text-gray-600 line-clamp-2">{{ $contact->message }}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex space-x-2">
                            <a href="{{ route('admin.contacts.show', $contact) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">View</a>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-6 py-12 text-center text-sm text-gray-500">No contact submissions found.</li>
            @endforelse
        </ul>
        @if($contacts->hasPages())
            <div class="border-t px-4 py-3">{{ $contacts->withQueryString()->links() }}</div>
        @endif
    </div>
@endsection
