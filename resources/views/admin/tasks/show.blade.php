@extends('layouts.admin')

@section('title', $task->name)

@section('header', $task->name)

@section('header-actions')
    <a href="{{ route('admin.tasks.edit', $task) }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
        Edit Task
    </a>
@endsection

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Task Info Card -->
            <div class="rounded-lg bg-white shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Task Information</h3>
                </div>
                <div class="px-6 py-5">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Project -->
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-500">Project</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if ($task->project)
                                    <a href="{{ route('admin.projects.show', $task->project) }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $task->project->name }}
                                    </a>
                                @else
                                    <span class="text-gray-400">Not assigned</span>
                                @endif
                            </p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-500">Status</label>
                            <p class="mt-1">
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
                            </p>
                        </div>

                        <!-- Priority -->
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-500">Priority</label>
                            <p class="mt-1">
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
                            </p>
                        </div>

                        <!-- Assigned To -->
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-500">Assigned To</label>
                            <p class="mt-1">
                                @if ($task->assigned_to_user)
                                    <div class="flex items-center space-x-2">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 text-xs font-semibold text-indigo-600">
                                            {{ substr($task->assigned_to_user->name, 0, 1) }}
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $task->assigned_to_user->name }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">Not assigned</span>
                                @endif
                            </p>
                        </div>

                        <!-- Created By -->
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-500">Created By</label>
                            <p class="mt-1">
                                @if ($task->created_by_user)
                                    <div class="flex items-center space-x-2">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-200 text-xs font-semibold text-gray-600">
                                            {{ substr($task->created_by_user->name, 0, 1) }}
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $task->created_by_user->name }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">Unknown</span>
                                @endif
                            </p>
                        </div>

                        <!-- Due Date -->
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-500">Due Date</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if ($task->due_date)
                                    <span class="{{ \Carbon\Carbon::parse($task->due_date)->isPast() && $task->status !== 'completed' ? 'text-red-600 font-semibold' : '' }}">
                                        {{ \Carbon\Carbon::parse($task->due_date)->format('F d, Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">Not set</span>
                                @endif
                            </p>
                        </div>

                        <!-- Estimated Hours -->
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-500">Estimated Hours</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $task->estimated_hours ?? 'Not set' }}
                            </p>
                        </div>

                        <!-- Actual Hours -->
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-500">Actual Hours</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $task->actual_hours ?? '0' }}
                            </p>
                        </div>
                    </div>

                    <!-- Description -->
                    @if ($task->description)
                        <div class="mt-6 border-t border-gray-200 pt-6">
                            <label class="block text-xs font-medium uppercase tracking-wider text-gray-500 mb-3">Description</label>
                            <div class="prose prose-sm max-w-none text-gray-700">
                                {{ nl2br($task->description) }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Progress Indicator -->
            @if ($task->estimated_hours > 0)
                <div class="rounded-lg bg-white shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Progress</h3>
                    @php
                        $progress = $task->actual_hours > 0 ? ($task->actual_hours / $task->estimated_hours) * 100 : 0;
                        $progress = min($progress, 100);
                    @endphp
                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">{{ $task->actual_hours }} / {{ $task->estimated_hours }} hours</span>
                        <span class="text-sm font-medium text-gray-600">{{ round($progress) }}%</span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-gray-200">
                        <div class="h-2 rounded-full bg-indigo-600" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            @endif

            <!-- Activity Timeline -->
            @if ($task->activities && count($task->activities) > 0)
                <div class="rounded-lg bg-white shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Activity Timeline</h3>
                    </div>
                    <div class="px-6 py-5">
                        <div class="space-y-6">
                            @foreach ($task->activities as $activity)
                                <div class="flex gap-4">
                                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-600">
                                        @if ($activity->user)
                                            {{ substr($activity->user->name, 0, 1) }}
                                        @else
                                            S
                                        @endif
                                    </div>
                                    <div class="flex-1 pt-0.5">
                                        <p class="text-sm font-medium text-gray-900">
                                            @if ($activity->user)
                                                {{ $activity->user->name }}
                                            @else
                                                System
                                            @endif
                                        </p>
                                        <p class="text-sm text-gray-600">{{ $activity->description ?? 'Activity recorded' }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Related Time Entries -->
            @if ($task->timeEntries && count($task->timeEntries) > 0)
                <div class="rounded-lg bg-white shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Time Entries</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach ($task->timeEntries as $entry)
                            <div class="px-6 py-4">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm font-medium text-gray-900">
                                        @if ($entry->user)
                                            {{ $entry->user->name }}
                                        @else
                                            Unknown User
                                        @endif
                                    </p>
                                    <span class="text-sm font-semibold text-indigo-600">{{ $entry->hours }} hrs</span>
                                </div>
                                <p class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($entry->date)->format('M d, Y') }}
                                </p>
                                @if ($entry->description)
                                    <p class="text-xs text-gray-600 mt-1">{{ $entry->description }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="rounded-lg bg-white shadow p-6">
                    <p class="text-sm text-gray-500">No time entries recorded</p>
                </div>
            @endif

            <!-- Quick Info -->
            <div class="rounded-lg bg-white shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Info</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Created</p>
                        <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($task->created_at)->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Last Updated</p>
                        <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($task->updated_at)->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
