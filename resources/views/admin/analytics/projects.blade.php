@extends('layouts.admin')

@section('title', 'Project Analytics')
@section('header', 'Project Analytics')

@section('content')
    <!-- Project Status Overview -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-5 mb-6">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="text-sm font-medium text-gray-500">Total Projects</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalProjects }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="text-sm font-medium text-gray-500">Planning</dt>
            <dd class="mt-1 text-3xl font-semibold text-blue-600">{{ $planningProjects }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="text-sm font-medium text-gray-500">In Progress</dt>
            <dd class="mt-1 text-3xl font-semibold text-indigo-600">{{ $inProgressProjects }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="text-sm font-medium text-gray-500">Completed</dt>
            <dd class="mt-1 text-3xl font-semibold text-green-600">{{ $completedProjects }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="text-sm font-medium text-gray-500">On Hold</dt>
            <dd class="mt-1 text-3xl font-semibold text-yellow-600">{{ $onHoldProjects }}</dd>
        </div>
    </div>

    <!-- Project Metrics -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-6">
        <div class="rounded-lg bg-white shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Average Project Duration</h3>
            <p class="text-4xl font-semibold text-indigo-600">{{ $avgDuration }} days</p>
        </div>

        <div class="rounded-lg bg-white shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Project Completion Rate</h3>
            <p class="text-4xl font-semibold text-green-600">{{ $completionRate }}%</p>
        </div>
    </div>

    <!-- Top Projects by Revenue -->
    <div class="rounded-lg bg-white shadow">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-lg font-medium text-gray-900">Top Projects by Budget</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Project</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Company</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Budget</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($topProjects ?? [] as $project)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $project->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $project->company->name }}</td>
                        <td class="px-6 py-4"><span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $project->status->badge() }}">{{ $project->status->label() }}</span></td>
                        <td class="px-6 py-4 text-sm text-gray-900">${{ number_format($project->budget ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">No projects found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
