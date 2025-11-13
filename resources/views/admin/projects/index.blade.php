@extends('layouts.admin')

@section('content')
<div class="py-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Projects</h1>
            <p class="text-gray-600 mt-1">Manage all your projects in one place</p>
        </div>
        <a
            href="{{ route('admin.projects.create') }}"
            class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 font-medium flex items-center gap-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Project
        </a>
    </div>

    <!-- Filters -->
    @include('admin.projects._filters', ['companies' => $companies ?? []])

    <!-- Projects Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($projects->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Name</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Company</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Priority</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Progress</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Budget</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($projects as $project)
                            <tr class="hover:bg-gray-50 transition duration-200">
                                <!-- Project Name -->
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $project->name }}</div>
                                </td>

                                <!-- Company -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">{{ $project->company?->name ?? 'N/A' }}</div>
                                </td>

                                <!-- Status Badge -->
                                <td class="px-6 py-4">
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
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                        {{ ucfirst(str_replace('-', ' ', $project->status)) }}
                                    </span>
                                </td>

                                <!-- Priority Badge -->
                                <td class="px-6 py-4">
                                    @php
                                        $priorityColors = [
                                            'low' => 'bg-gray-100 text-gray-800',
                                            'medium' => 'bg-blue-100 text-blue-800',
                                            'high' => 'bg-orange-100 text-orange-800',
                                            'urgent' => 'bg-red-100 text-red-800',
                                        ];
                                        $priorityClass = $priorityColors[$project->priority] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $priorityClass }}">
                                        {{ ucfirst($project->priority) }}
                                    </span>
                                </td>

                                <!-- Progress Bar -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                                            <div
                                                class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                                                style="width: {{ $project->progress ?? 0 }}%"
                                            ></div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-700 w-8 text-right">{{ $project->progress ?? 0 }}%</span>
                                    </div>
                                </td>

                                <!-- Budget -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 font-medium">
                                        ${{ number_format($project->budget ?? 0, 2) }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <a
                                            href="{{ route('admin.projects.show', $project->id) }}"
                                            class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition duration-200"
                                            title="View"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a
                                            href="{{ route('admin.projects.edit', $project->id) }}"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition duration-200"
                                            title="Edit"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form
                                            action="{{ route('admin.projects.destroy', $project->id) }}"
                                            method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Are you sure you want to delete this project?')"
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
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white border-t border-gray-200 px-6 py-4">
                {{ $projects->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900">No projects found</h3>
                <p class="text-gray-500 mt-1">Get started by creating your first project.</p>
                <a
                    href="{{ route('admin.projects.create') }}"
                    class="inline-block mt-4 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 font-medium"
                >
                    Create Project
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
