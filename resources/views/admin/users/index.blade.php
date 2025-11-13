@extends('layouts.admin')

@section('content')
<div class="py-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Users</h1>
            <p class="text-gray-600 mt-1">Manage all users in your system</p>
        </div>
        <a
            href="{{ route('admin.users.create') }}"
            class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 font-medium flex items-center gap-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create User
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-indigo-100 rounded-lg">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10h.01M13 16h2v2h-2v-2zm2-5h2v2h-2v-2zM9 16h2v2H9v-2zm2-5h2v2h-2v-2zM5 16h2v2H5v-2zm2-5h2v2H7v-2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Active Users</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['active'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Admins -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Admins</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['admins'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Employees -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Employees</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['employees'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Clients -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Clients</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['clients'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by name or email..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
            </div>

            <!-- Role Filter -->
            <div>
                <select
                    name="role"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="employee" {{ request('role') === 'employee' ? 'selected' : '' }}>Employee</option>
                    <option value="client" {{ request('role') === 'client' ? 'selected' : '' }}>Client</option>
                </select>
            </div>

            <!-- Active Status Filter -->
            <div>
                <select
                    name="status"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <!-- Filter Button -->
            <button
                type="submit"
                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 font-medium"
            >
                Filter
            </button>

            <!-- Clear Filters -->
            @if(request()->hasAny(['search', 'role', 'status']))
                <a
                    href="{{ route('admin.users.index') }}"
                    class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition duration-200 font-medium"
                >
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($users->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">User</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Email</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Company</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Role</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Last Login</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50 transition duration-200">
                                <!-- User Avatar/Initials & Name -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($user->avatar)
                                            <img
                                                src="{{ asset('storage/' . $user->avatar) }}"
                                                alt="{{ $user->name }}"
                                                class="w-10 h-10 rounded-full object-cover"
                                            />
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span class="text-sm font-semibold text-indigo-600">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">{{ $user->email }}</div>
                                </td>

                                <!-- Company -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">{{ $user->company?->name ?? 'N/A' }}</div>
                                </td>

                                <!-- Role Badge -->
                                <td class="px-6 py-4">
                                    @php
                                        $roleColors = [
                                            'admin' => 'bg-purple-100 text-purple-800',
                                            'employee' => 'bg-blue-100 text-blue-800',
                                            'client' => 'bg-green-100 text-green-800',
                                        ];
                                        $roleClass = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $roleClass }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>

                                <!-- Status Badge -->
                                <td class="px-6 py-4">
                                    @if($user->is_active)
                                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <!-- Last Login -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">
                                        @if($user->last_login_at)
                                            {{ $user->last_login_at->format('M d, Y H:i') }}
                                        @else
                                            Never
                                        @endif
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <!-- View -->
                                        <a
                                            href="{{ route('admin.users.show', $user->id) }}"
                                            class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition duration-200"
                                            title="View"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        <!-- Edit -->
                                        <a
                                            href="{{ route('admin.users.edit', $user->id) }}"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition duration-200"
                                            title="Edit"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        <!-- Delete -->
                                        <form
                                            action="{{ route('admin.users.destroy', $user->id) }}"
                                            method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Are you sure you want to delete this user?')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition duration-200"
                                                title="Delete"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>

                                        <!-- Status Toggle Dropdown -->
                                        @if($user->is_active)
                                            <form
                                                action="{{ route('admin.users.deactivate', $user->id) }}"
                                                method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Are you sure you want to deactivate this user?')"
                                            >
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition duration-200"
                                                    title="Deactivate"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <form
                                                action="{{ route('admin.users.activate', $user->id) }}"
                                                method="POST"
                                                class="inline"
                                            >
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition duration-200"
                                                    title="Activate"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white border-t border-gray-200 px-6 py-4">
                {{ $users->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10h.01M13 16h2v2h-2v-2zm2-5h2v2h-2v-2zM9 16h2v2H9v-2zm2-5h2v2h-2v-2zM5 16h2v2H5v-2zm2-5h2v2H7v-2z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900">No users found</h3>
                <p class="text-gray-500 mt-1">Get started by creating your first user.</p>
                <a
                    href="{{ route('admin.users.create') }}"
                    class="inline-block mt-4 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 font-medium"
                >
                    Create User
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
