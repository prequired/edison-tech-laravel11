@extends('layouts.app')

@section('title', $project->name)

@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ $project->name }}</h1>
        <a href="{{ route('client.projects.index') }}"
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Projects
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Project Overview Section -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Project Overview</h2>
        </div>
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Status -->
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Status</p>
                    <div class="mt-2">
                        @switch($project->status)
                            @case('planning')
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-4 py-2 text-sm font-medium text-blue-800">
                                    Planning
                                </span>
                            @break
                            @case('in_progress')
                                <span class="inline-flex items-center rounded-full bg-purple-100 px-4 py-2 text-sm font-medium text-purple-800">
                                    In Progress
                                </span>
                            @break
                            @case('on_hold')
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-4 py-2 text-sm font-medium text-yellow-800">
                                    On Hold
                                </span>
                            @break
                            @case('completed')
                                <span class="inline-flex items-center rounded-full bg-green-100 px-4 py-2 text-sm font-medium text-green-800">
                                    Completed
                                </span>
                            @break
                            @case('cancelled')
                                <span class="inline-flex items-center rounded-full bg-red-100 px-4 py-2 text-sm font-medium text-red-800">
                                    Cancelled
                                </span>
                            @break
                        @endswitch
                    </div>
                </div>

                <!-- Progress -->
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Progress</p>
                    <div class="mt-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-2xl font-bold text-indigo-600">{{ $project->progress ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-indigo-500 to-blue-500 h-3 rounded-full transition-all duration-300"
                                 style="width: {{ $project->progress ?? 0 }}%">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Company -->
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Company</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900">
                        {{ $project->company->name ?? 'N/A' }}
                    </p>
                </div>
            </div>

            <!-- Dates Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100">
                    <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Start Date</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900">
                        {{ $project->start_date ? $project->start_date->format('M d, Y') : 'N/A' }}
                    </p>
                    @if($project->start_date)
                        <p class="mt-1 text-xs text-gray-500">
                            {{ $project->start_date->format('l') }}
                        </p>
                    @endif
                </div>

                <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100">
                    <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Deadline</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900">
                        {{ $project->deadline ? $project->deadline->format('M d, Y') : 'N/A' }}
                    </p>
                    @if($project->deadline)
                        <p class="mt-1 text-xs text-gray-500">
                            {{ $project->deadline->format('l') }}
                        </p>
                    @endif
                </div>

                @if($project->budget)
                    <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Budget</p>
                        <p class="mt-2 text-lg font-semibold text-gray-900">
                            ${{ number_format($project->budget, 2) }}
                        </p>
                    </div>
                @endif

                @if($project->budget && $project->spent)
                    <div class="bg-orange-50 rounded-lg p-4 border border-orange-100">
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Spent</p>
                        <p class="mt-2 text-lg font-semibold text-gray-900">
                            ${{ number_format($project->spent, 2) }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ number_format(($project->spent / $project->budget) * 100, 1) }}% of budget
                        </p>
                    </div>
                @endif
            </div>

            <!-- Description -->
            @if($project->description)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900 uppercase tracking-wide mb-2">Description</h3>
                    <p class="text-gray-600 whitespace-pre-wrap">{{ $project->description }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- My Tasks Section -->
    @if($myTasks->count())
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">My Tasks</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wide">Task Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wide">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wide">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wide">Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myTasks as $task)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $task->title }}</p>
                                    @if($task->description)
                                        <p class="text-sm text-gray-500 mt-1 line-clamp-1">{{ $task->description }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($task->status)
                                        @case('pending')
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-800">
                                                Pending
                                            </span>
                                        @break
                                        @case('in_progress')
                                            <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800">
                                                In Progress
                                            </span>
                                        @break
                                        @case('completed')
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                                Completed
                                            </span>
                                        @break
                                        @case('blocked')
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800">
                                                Blocked
                                            </span>
                                        @break
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($task->priority)
                                        @case('low')
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                                Low
                                            </span>
                                        @break
                                        @case('medium')
                                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-800">
                                                Medium
                                            </span>
                                        @break
                                        @case('high')
                                            <span class="inline-flex items-center rounded-full bg-orange-100 px-3 py-1 text-xs font-medium text-orange-800">
                                                High
                                            </span>
                                        @break
                                        @case('critical')
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800">
                                                Critical
                                            </span>
                                        @break
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    @if($task->due_date)
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $task->due_date->format('M d, Y') }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                @if($task->due_date->isFuture())
                                                    In {{ $task->due_date->diffForHumans() }}
                                                @elseif($task->due_date->isToday())
                                                    Today
                                                @else
                                                    {{ $task->due_date->diffForHumans() }}
                                                @endif
                                            </p>
                                        </div>
                                    @else
                                        <span class="text-gray-400">No deadline</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Project Documents Section -->
    @if($project->documents && $project->documents->count())
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Project Documents</h2>
            </div>
            <div class="px-6 py-6">
                <div class="space-y-3">
                    @foreach($project->documents as $document)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-indigo-50 transition-colors">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <svg class="h-8 w-8 flex-shrink-0 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21l-8.97-8.97A8 8 0 0128.03 4.03v0a8 8 0 010 11.31L12 21z"></path>
                                </svg>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $document->name }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        @php
                                            $fileSize = $document->file_size ?? 0;
                                            if ($fileSize < 1024) {
                                                $size = $fileSize . ' B';
                                            } elseif ($fileSize < 1024 * 1024) {
                                                $size = round($fileSize / 1024, 2) . ' KB';
                                            } else {
                                                $size = round($fileSize / (1024 * 1024), 2) . ' MB';
                                            }
                                        @endphp
                                        {{ $size }}
                                    </p>
                                </div>
                            </div>
                            @if($document->file_path)
                                <a href="{{ Storage::url($document->file_path) }}"
                                   download
                                   class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors"
                                   title="Download document">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Project Team Section -->
    @if($project->team && $project->team->count())
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Project Team</h2>
            </div>
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($project->team as $member)
                        <div class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg">
                            <div class="flex-shrink-0">
                                @if($member->avatar)
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($member->avatar) }}" alt="{{ $member->name }}">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center">
                                        <span class="text-sm font-medium text-white">{{ substr($member->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $member->name }}</p>
                                <p class="text-xs text-gray-500">{{ $member->email }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Time Tracking Summary Section -->
    @if($project->time_entries && $project->time_entries->count())
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Time Tracking Summary</h2>
            </div>
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Total Hours -->
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg p-6 border border-indigo-100">
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Total Hours Logged</p>
                        <p class="mt-3 text-3xl font-bold text-indigo-600">
                            @php
                                $totalHours = $project->time_entries->sum('hours');
                            @endphp
                            {{ number_format($totalHours, 1) }}
                        </p>
                    </div>

                    <!-- Average Hours Per Day -->
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg p-6 border border-purple-100">
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Average Per Entry</p>
                        <p class="mt-3 text-3xl font-bold text-purple-600">
                            @php
                                $avgHours = $project->time_entries->count() > 0
                                    ? $project->time_entries->sum('hours') / $project->time_entries->count()
                                    : 0;
                            @endphp
                            {{ number_format($avgHours, 1) }}h
                        </p>
                    </div>

                    <!-- Last Entry -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-6 border border-green-100">
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Last Entry</p>
                        <p class="mt-3 text-xl font-bold text-green-600">
                            @php
                                $lastEntry = $project->time_entries->sortByDesc('created_at')->first();
                            @endphp
                            @if($lastEntry)
                                {{ $lastEntry->created_at->format('M d, Y') }}
                            @else
                                No entries
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
