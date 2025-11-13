@extends('layouts.admin')

@section('title', $member->name)
@section('header', $member->name)

@section('header-actions')
    <a href="{{ route('admin.team.edit', $member) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Edit</a>
@endsection

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="rounded-lg bg-white shadow p-6">
                <div class="flex items-center mb-6">
                    @if($member->avatar)
                        <img src="{{ asset('storage/' . $member->avatar) }}" alt="{{ $member->name }}" class="h-20 w-20 rounded-full object-cover">
                    @else
                        <div class="h-20 w-20 rounded-full bg-indigo-600 flex items-center justify-center">
                            <span class="text-3xl font-medium text-white">{{ substr($member->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <div class="ml-4">
                        <h3 class="text-xl font-medium text-gray-900">{{ $member->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $member->email }}</p>
                    </div>
                </div>

                <dl class="grid grid-cols-2 gap-4">
                    <div><dt class="text-sm font-medium text-gray-500">Role</dt><dd class="mt-1 text-sm text-gray-900">{{ ucfirst($member->role) }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">Status</dt><dd class="mt-1">
                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $member->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $member->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">Joined</dt><dd class="mt-1 text-sm text-gray-900">{{ $member->created_at->format('M d, Y') }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">Last Login</dt><dd class="mt-1 text-sm text-gray-900">{{ $member->last_login_at ? $member->last_login_at->format('M d, Y g:i A') : 'Never' }}</dd></div>
                </dl>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-lg bg-white shadow p-6 space-y-3">
                <a href="{{ route('admin.team.edit', $member) }}" class="block w-full text-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Edit Member</a>
                @if($member->is_active)
                    <form method="POST" action="{{ route('admin.team.deactivate', $member) }}">
                        @csrf
                        <button type="submit" class="w-full rounded-md bg-yellow-600 px-3 py-2 text-sm font-semibold text-white hover:bg-yellow-500">Deactivate</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.team.activate', $member) }}">
                        @csrf
                        <button type="submit" class="w-full rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-500">Activate</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
