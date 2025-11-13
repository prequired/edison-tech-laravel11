@extends('layouts.admin')

@section('title', 'Team Members')
@section('header', 'Team Members')

@section('header-actions')
    <a href="{{ route('admin.team.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Add Team Member
    </a>
@endsection

@section('content')
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($members as $member)
            <div class="overflow-hidden rounded-lg bg-white shadow hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        @if($member->avatar)
                            <img src="{{ asset('storage/' . $member->avatar) }}" alt="{{ $member->name }}" class="h-16 w-16 rounded-full object-cover">
                        @else
                            <div class="h-16 w-16 rounded-full bg-indigo-600 flex items-center justify-center">
                                <span class="text-2xl font-medium text-white">{{ substr($member->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-medium text-gray-900">{{ $member->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $member->email }}</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Role</span>
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $member->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($member->role) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Status</span>
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $member->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $member->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 flex space-x-2">
                        <a href="{{ route('admin.team.show', $member) }}" class="flex-1 text-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">View</a>
                        <a href="{{ route('admin.team.edit', $member) }}" class="flex-1 text-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Edit</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 rounded-lg bg-white p-12 text-center shadow">
                <p class="text-sm text-gray-500">No team members found.</p>
            </div>
        @endforelse
    </div>

    @if($members->hasPages())
        <div class="mt-6">{{ $members->links() }}</div>
    @endif
@endsection
