@extends('layouts.admin')

@section('title', 'Tasks')

@section('header', 'Tasks')

@section('header-actions')
    <a href="{{ route('admin.tasks.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
        Create Task
    </a>
@endsection

@section('content')
    <!-- Statistics Section -->
    <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
        <!-- Total Tasks -->
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-md bg-indigo-500">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 flex-1">
                    <dl>
                        <dt class="truncate text-sm font-medium text-gray-500">Total Tasks</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['total'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- To Do -->
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-md bg-gray-400">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 flex-1">
                    <dl>
                        <dt class="truncate text-sm font-medium text-gray-500">To Do</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['to_do'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-md bg-blue-400">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 flex-1">
                    <dl>
                        <dt class="truncate text-sm font-medium text-gray-500">In Progress</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['in_progress'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-md bg-green-400">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 flex-1">
                    <dl>
                        <dt class="truncate text-sm font-medium text-gray-500">Completed</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['completed'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Overdue -->
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-md bg-red-400">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 flex-1">
                    <dl>
                        <dt class="truncate text-sm font-medium text-gray-500">Overdue</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['overdue'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="mb-6 rounded-lg bg-white p-6 shadow">
        <form method="GET" action="{{ route('admin.tasks.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Tasks</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Task name..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2">
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2">
                        <option value="">All Statuses</option>
                        <option value="todo" {{ request('status') === 'todo' ? 'selected' : '' }}>To Do</option>
                        <option value="in-progress" {{ request('status') === 'in-progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                    <select name="priority" id="priority" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2">
                        <option value="">All Priorities</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>

                <!-- Project -->
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                    <select name="project_id" id="project_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2">
                        <option value="">All Projects</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Assigned To -->
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                    <select name="assigned_to" id="assigned_to" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2">
                        <option value="">All Users</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('admin.tasks.index') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Clear Filters</a>
                <button type="submit" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tasks Table -->
    <div class="overflow-hidden rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Task Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Project</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Assigned To</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Due Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($tasks as $task)
                    <tr class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $task->name }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                            @if ($task->project)
                                {{ $task->project->name }}
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            @if ($task->assigned_to_user)
                                <div class="flex items-center space-x-2">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-600">
                                        {{ substr($task->assigned_to_user->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $task->assigned_to_user->name }}</span>
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">—</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            @php
                                $statusColors = [
                                    'todo' => 'bg-gray-100 text-gray-800',
                                    'in-progress' => 'bg-blue-100 text-blue-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $statusLabel = ucwords(str_replace('-', ' ', $task->status));
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColors[$task->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            @php
                                $priorityColors = [
                                    'low' => 'bg-green-100 text-green-800',
                                    'medium' => 'bg-yellow-100 text-yellow-800',
                                    'high' => 'bg-orange-100 text-orange-800',
                                    'urgent' => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $priorityColors[$task->priority] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                            @if ($task->due_date)
                                <span class="{{ \Carbon\Carbon::parse($task->due_date)->isPast() ? 'text-red-600 font-semibold' : '' }}">
                                    {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-3">
                                <a href="{{ route('admin.tasks.show', $task) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                <a href="{{ route('admin.tasks.edit', $task) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No tasks found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $tasks->links() }}
    </div>
@endsection
