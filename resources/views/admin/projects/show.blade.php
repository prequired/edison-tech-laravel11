@extends('layouts.admin')

@section('content')
<div class="py-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $project->name }}</h1>
            <p class="text-gray-600 mt-1">View and manage project details</p>
        </div>
        <div class="flex gap-3">
            <a
                href="{{ route('admin.projects.edit', $project->id) }}"
                class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 font-medium flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
            <a
                href="{{ route('admin.projects.index') }}"
                class="px-6 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition duration-200 font-medium"
            >
                Back
            </a>
        </div>
    </div>

    <!-- Information Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Project Details Card -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Project Details</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Company</p>
                        <p class="text-gray-900 font-medium">{{ $project->company?->name ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Status</p>
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
                        <span class="inline-block mt-1 px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                            {{ ucfirst(str_replace('-', ' ', $project->status)) }}
                        </span>
                    </div>
                </div>
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Priority</p>
                        @php
                            $priorityColors = [
                                'low' => 'bg-gray-100 text-gray-800',
                                'medium' => 'bg-blue-100 text-blue-800',
                                'high' => 'bg-orange-100 text-orange-800',
                                'urgent' => 'bg-red-100 text-red-800',
                            ];
                            $priorityClass = $priorityColors[$project->priority] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-block mt-1 px-3 py-1 text-xs font-semibold rounded-full {{ $priorityClass }}">
                            {{ ucfirst($project->priority) }}
                        </span>
                    </div>
                </div>
                <hr class="my-4" />
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Start Date</p>
                        <p class="text-gray-900 font-medium">{{ $project->start_date?->format('M d, Y') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Deadline</p>
                        <p class="text-gray-900 font-medium">{{ $project->deadline?->format('M d, Y') ?? 'N/A' }}</p>
                    </div>
                </div>
                <hr class="my-4" />
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Budget</p>
                        <p class="text-gray-900 font-medium">${{ number_format($project->budget ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Hourly Rate</p>
                        <p class="text-gray-900 font-medium">${{ number_format($project->hourly_rate ?? 0, 2) }}/hour</p>
                    </div>
                </div>
                <hr class="my-4" />
                <div>
                    <p class="text-sm font-medium text-gray-500">Billable</p>
                    <p class="mt-1">
                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $project->is_billable ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $project->is_billable ? 'Yes' : 'No' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Progress Card -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Progress</h2>
            <div class="space-y-6">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <p class="text-sm font-medium text-gray-500">Overall Progress</p>
                        <span class="text-lg font-bold text-indigo-600">{{ $project->progress ?? 0 }}%</span>
                    </div>
                    <div class="bg-gray-200 rounded-full h-3">
                        <div
                            class="bg-indigo-600 h-3 rounded-full transition-all duration-300"
                            style="width: {{ $project->progress ?? 0 }}%"
                        ></div>
                    </div>
                </div>

                <hr />

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Estimated Hours</p>
                        <p class="text-gray-900 font-medium text-lg mt-1">{{ $project->estimated_hours ?? 0 }} hrs</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Actual Hours</p>
                        <p class="text-gray-900 font-medium text-lg mt-1">{{ $project->actual_hours ?? 0 }} hrs</p>
                    </div>
                </div>

                <hr />

                <div>
                    <p class="text-sm font-medium text-gray-500 mb-2">Tasks Completed</p>
                    <div class="flex items-center gap-3">
                        <span class="text-3xl font-bold text-indigo-600">{{ $project->tasksCompleted?->count() ?? 0 }}</span>
                        <span class="text-gray-600">/ {{ $project->tasks?->count() ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Technologies Section -->
    @if($project->technologies && count($project->technologies) > 0)
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Technologies</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($project->technologies as $tech)
                    <span class="inline-block px-4 py-2 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium">
                        {{ $tech }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Team Members Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Team Members</h2>
        @if($project->team && $project->team->count() > 0)
            <div class="space-y-3">
                @foreach($project->team as $member)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-indigo-600 font-semibold text-sm">
                                    {{ substr($member->name ?? 'N', 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $member->name ?? 'Unknown' }}</p>
                                <p class="text-sm text-gray-500">{{ $member->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-medium text-gray-600">{{ $member->pivot->role ?? 'Team Member' }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-6">No team members assigned yet.</p>
        @endif
    </div>

    <!-- Tasks Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-900">Tasks</h2>
            @if($project->tasks?->count() > 0)
                <span class="text-sm font-medium text-gray-600">{{ $project->tasks->count() }} tasks</span>
            @endif
        </div>
        @if($project->tasks && $project->tasks->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-200 bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Task Name</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Assigned To</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Status</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Due Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($project->tasks as $task)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-900 font-medium">{{ $task->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $task->assignedTo?->name ?? 'Unassigned' }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $taskStatusColors = [
                                            'pending' => 'bg-gray-100 text-gray-800',
                                            'in-progress' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'blocked' => 'bg-red-100 text-red-800',
                                        ];
                                        $taskStatusClass = $taskStatusColors[$task->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded {{ $taskStatusClass }}">
                                        {{ ucfirst(str_replace('-', ' ', $task->status ?? 'pending')) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $task->due_date?->format('M d, Y') ?? 'No date' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-6">No tasks created yet.</p>
        @endif
    </div>

    <!-- Time Entries Section -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-900">Time Entries</h2>
            @if($project->timeEntries?->count() > 0)
                <span class="text-sm font-medium text-gray-600">{{ $project->timeEntries->count() }} entries</span>
            @endif
        </div>
        @if($project->timeEntries && $project->timeEntries->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-200 bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Date</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">User</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Hours</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Billable Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($project->timeEntries as $entry)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-900 font-medium">{{ $entry->date?->format('M d, Y') ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $entry->user?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $entry->hours ?? 0 }} hrs</td>
                                <td class="px-4 py-3 text-gray-900 font-medium">${{ number_format($entry->billable_amount ?? 0, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-6">No time entries recorded yet.</p>
        @endif
    </div>
</div>
@endsection
