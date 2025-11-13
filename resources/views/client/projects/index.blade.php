@extends('layouts.app')

@section('title', 'My Projects')

@section('header', 'Projects')

@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Projects</h3>
                <form method="GET" action="{{ route('client.projects.index') }}" class="flex gap-2">
                    <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Statuses</option>
                        <option value="planning" @selected(request('status') === 'planning')>Planning</option>
                        <option value="in_progress" @selected(request('status') === 'in_progress')>In Progress</option>
                        <option value="on_hold" @selected(request('status') === 'on_hold')>On Hold</option>
                        <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                        <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                        Filter
                    </button>
                    @if(request('status'))
                        <a href="{{ route('client.projects.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Projects Grid -->
    @if($projects->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($projects as $project)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                    <!-- Card Header with Status -->
                    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 px-6 py-4 border-b border-indigo-100">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 truncate">
                                    {{ $project->name }}
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $project->company->name ?? 'N/A' }}</p>
                            </div>
                            <!-- Status Badge -->
                            <div class="flex-shrink-0">
                                @switch($project->status)
                                    @case('planning')
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800">
                                            Planning
                                        </span>
                                    @break
                                    @case('in_progress')
                                        <span class="inline-flex items-center rounded-full bg-purple-100 px-3 py-1 text-xs font-medium text-purple-800">
                                            In Progress
                                        </span>
                                    @break
                                    @case('on_hold')
                                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-800">
                                            On Hold
                                        </span>
                                    @break
                                    @case('completed')
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                            Completed
                                        </span>
                                    @break
                                    @case('cancelled')
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800">
                                            Cancelled
                                        </span>
                                    @break
                                @endswitch
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="px-6 py-4 space-y-4">
                        <!-- Description -->
                        @if($project->description)
                            <p class="text-sm text-gray-600 line-clamp-2">
                                {{ $project->description }}
                            </p>
                        @endif

                        <!-- Progress -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Progress</span>
                                <span class="text-sm font-semibold text-indigo-600">{{ $project->progress ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-gradient-to-r from-indigo-500 to-blue-500 h-2.5 rounded-full transition-all duration-300"
                                     style="width: {{ $project->progress ?? 0 }}%">
                                </div>
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500 text-xs uppercase tracking-wide">Start Date</p>
                                <p class="text-gray-900 font-medium mt-1">
                                    {{ $project->start_date ? $project->start_date->format('M d, Y') : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase tracking-wide">Deadline</p>
                                <p class="text-gray-900 font-medium mt-1">
                                    {{ $project->deadline ? $project->deadline->format('M d, Y') : 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <a href="{{ route('client.projects.show', $project) }}"
                           class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                            View Details
                            <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($projects->hasPages())
            <div class="bg-white rounded-lg shadow-sm p-6">
                {{ $projects->links() }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No projects found</h3>
            <p class="mt-2 text-sm text-gray-500">
                @if(request('status'))
                    Try adjusting your filter criteria
                @else
                    You don't have any projects yet
                @endif
            </p>
        </div>
    @endif
</div>
@endsection
