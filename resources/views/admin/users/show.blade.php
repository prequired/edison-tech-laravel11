@extends('layouts.admin')

@section('content')
<div class="py-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
            <p class="text-gray-600 mt-1">View and manage user profile</p>
        </div>
        <div class="flex gap-3">
            <a
                href="{{ route('admin.users.edit', $user->id) }}"
                class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 font-medium flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
            <a
                href="{{ route('admin.users.index') }}"
                class="px-6 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition duration-200 font-medium"
            >
                Back
            </a>
        </div>
    </div>

    <!-- User Information Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- User Details Card -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">User Information</h2>
            <div class="space-y-6">
                <!-- Avatar and Basic Info -->
                <div class="flex items-start gap-6">
                    <div>
                        @if($user->avatar)
                            <img
                                src="{{ asset('storage/' . $user->avatar) }}"
                                alt="{{ $user->name }}"
                                class="w-24 h-24 rounded-full object-cover"
                            />
                        @else
                            <div class="w-24 h-24 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-2xl font-semibold text-indigo-600">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Name</p>
                                <p class="text-gray-900 font-medium">{{ $user->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Email</p>
                                <p class="text-gray-900 font-medium">{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-2" />

                <!-- Contact Information -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Phone</p>
                        <p class="text-gray-900 font-medium">{{ $user->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Company</p>
                        <p class="text-gray-900 font-medium">{{ $user->company?->name ?? 'N/A' }}</p>
                    </div>
                </div>

                <hr class="my-2" />

                <!-- Role and Status -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Role</p>
                        @php
                            $roleColors = [
                                'admin' => 'bg-purple-100 text-purple-800',
                                'employee' => 'bg-blue-100 text-blue-800',
                                'client' => 'bg-green-100 text-green-800',
                            ];
                            $roleClass = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-block mt-1 px-3 py-1 text-sm font-semibold rounded-full {{ $roleClass }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Status</p>
                        <div class="mt-1">
                            @if($user->is_active)
                                <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <hr class="my-2" />

                <!-- Additional Information -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Timezone</p>
                        <p class="text-gray-900 font-medium">{{ $user->timezone ?? 'UTC' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Last Login</p>
                        <p class="text-gray-900 font-medium">
                            @if($user->last_login_at)
                                {{ $user->last_login_at->format('M d, Y H:i') }}
                            @else
                                Never
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Card (for employees and admins) -->
        @if(in_array($user->role, ['admin', 'employee']))
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Statistics</h2>
                <div class="space-y-6">
                    <!-- Projects Count -->
                    <div class="border-b pb-4">
                        <p class="text-sm font-medium text-gray-500">Assigned Projects</p>
                        <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $user->projects->count() ?? 0 }}</p>
                    </div>

                    <!-- Tasks Count -->
                    <div class="border-b pb-4">
                        <p class="text-sm font-medium text-gray-500">Assigned Tasks</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2">{{ $user->tasks->count() ?? 0 }}</p>
                    </div>

                    <!-- Time Entries -->
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Time Entries</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">{{ $user->timeEntries->count() ?? 0 }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Assigned Projects (for employees and admins) -->
    @if(in_array($user->role, ['admin', 'employee']) && isset($user->projects))
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-900">Assigned Projects</h2>
                @if($user->projects->count() > 0)
                    <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-semibold">
                        {{ $user->projects->count() }}
                    </span>
                @endif
            </div>

            @if($user->projects->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Project Name</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Company</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Progress</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($user->projects as $project)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('admin.projects.show', $project->id) }}" class="text-indigo-600 hover:underline font-medium">
                                            {{ $project->name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $project->company?->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $statusColors = [
                                                'planning' => 'bg-blue-100 text-blue-800',
                                                'active' => 'bg-green-100 text-green-800',
                                                'on-hold' => 'bg-yellow-100 text-yellow-800',
                                                'completed' => 'bg-gray-100 text-gray-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                            ];
                                            $statusClass = $statusColors[$project->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                            {{ ucfirst(str_replace('-', ' ', $project->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2 w-32">
                                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                <div
                                                    class="bg-indigo-600 h-2 rounded-full"
                                                    style="width: {{ $project->progress ?? 0 }}%"
                                                ></div>
                                            </div>
                                            <span class="text-xs font-medium text-gray-700 w-8">{{ $project->progress ?? 0 }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">No assigned projects.</p>
            @endif
        </div>
    @endif

    <!-- Assigned Tasks -->
    @if(isset($user->tasks))
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-900">Assigned Tasks</h2>
                @if($user->tasks->count() > 0)
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                        {{ $user->tasks->count() }}
                    </span>
                @endif
            </div>

            @if($user->tasks->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Task Name</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Project</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Priority</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Due Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($user->tasks as $task)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('admin.tasks.show', $task->id) }}" class="text-indigo-600 hover:underline font-medium">
                                            {{ $task->name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $task->project?->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $taskStatusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'in_progress' => 'bg-blue-100 text-blue-800',
                                                'completed' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                            ];
                                            $taskStatusClass = $taskStatusColors[$task->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full {{ $taskStatusClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $priorityColors = [
                                                'low' => 'bg-gray-100 text-gray-800',
                                                'medium' => 'bg-blue-100 text-blue-800',
                                                'high' => 'bg-orange-100 text-orange-800',
                                                'urgent' => 'bg-red-100 text-red-800',
                                            ];
                                            $priorityClass = $priorityColors[$task->priority] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full {{ $priorityClass }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $task->due_date?->format('M d, Y') ?? 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">No assigned tasks.</p>
            @endif
        </div>
    @endif

    <!-- Recent Time Entries (for employees and admins) -->
    @if(in_array($user->role, ['admin', 'employee']) && isset($user->timeEntries))
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-900">Recent Time Entries</h2>
                @if($user->timeEntries->count() > 0)
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                        {{ $user->timeEntries->count() }}
                    </span>
                @endif
            </div>

            @if($user->timeEntries->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Date</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Project</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Task</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Duration</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($user->timeEntries->take(10) as $entry)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $entry->date?->format('M d, Y') ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $entry->project?->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $entry->task?->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                        {{ $entry->hours ?? 0 }} hours
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ substr($entry->notes ?? '', 0, 50) }}{{ strlen($entry->notes ?? '') > 50 ? '...' : '' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-center text-gray-500">
                                        No time entries found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">No time entries.</p>
            @endif
        </div>
    @endif
</div>
@endsection
