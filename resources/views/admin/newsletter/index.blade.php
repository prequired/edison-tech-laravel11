@extends('layouts.admin')

@section('title', 'Newsletter Subscribers')
@section('header', 'Newsletter Subscribers')

@section('header-actions')
    <a href="{{ route('admin.newsletter.export') }}" class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
        Export CSV
    </a>
@endsection

@section('content')
    <div class="mb-6 grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="text-sm font-medium text-gray-500">Total Subscribers</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $total }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="text-sm font-medium text-gray-500">Active</dt>
            <dd class="mt-1 text-3xl font-semibold text-green-600">{{ $active }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="text-sm font-medium text-gray-500">Unverified</dt>
            <dd class="mt-1 text-3xl font-semibold text-yellow-600">{{ $unverified }}</dd>
        </div>
    </div>

    <div class="mb-6 rounded-lg bg-white p-4 shadow">
        <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search emails..." class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="unverified" {{ request('status') == 'unverified' ? 'selected' : '' }}>Unverified</option>
                <option value="unsubscribed" {{ request('status') == 'unsubscribed' ? 'selected' : '' }}>Unsubscribed</option>
            </select>
            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Apply</button>
            <a href="{{ route('admin.newsletter.index') }}" class="rounded-md bg-gray-200 px-3 py-2 text-center text-sm font-semibold text-gray-700 hover:bg-gray-300">Clear</a>
        </form>
    </div>

    <div class="rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Subscribed</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($subscribers as $subscriber)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $subscriber->email }}</td>
                        <td class="px-6 py-4">
                            @if($subscriber->status === 'active')
                                <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">Active</span>
                            @elseif($subscriber->status === 'unverified')
                                <span class="inline-flex rounded-full bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-800">Unverified</span>
                            @else
                                <span class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-800">Unsubscribed</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $subscriber->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <form method="POST" action="{{ route('admin.newsletter.destroy', $subscriber) }}" class="inline" onsubmit="return confirm('Remove this subscriber?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">No subscribers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($subscribers->hasPages())
            <div class="border-t px-4 py-3">{{ $subscribers->withQueryString()->links() }}</div>
        @endif
    </div>
@endsection
