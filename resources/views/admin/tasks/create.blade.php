@extends('layouts.admin')

@section('title', 'Create Task')

@section('header', 'Create Task')

@section('content')
    <div class="mx-auto max-w-2xl rounded-lg bg-white shadow">
        <form method="POST" action="{{ route('admin.tasks.store') }}" class="space-y-6 px-6 py-8">
            @csrf

            <!-- Project -->
            <div>
                <label for="project_id" class="block text-sm font-medium text-gray-900">Project</label>
                <select name="project_id" id="project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2 {{ $errors->has('project_id') ? 'border-red-500' : '' }}">
                    <option value="">Select a project</option>
                    @foreach ($projects ?? [] as $project)
                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
                @error('project_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Task Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-900">Task Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Enter task name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2 {{ $errors->has('name') ? 'border-red-500' : '' }}" required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-900">Description</label>
                <textarea name="description" id="description" rows="4" placeholder="Enter task description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2 {{ $errors->has('description') ? 'border-red-500' : '' }}">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-900">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2 {{ $errors->has('status') ? 'border-red-500' : '' }}" required>
                    <option value="">Select status</option>
                    <option value="todo" {{ old('status') === 'todo' ? 'selected' : '' }}>To Do</option>
                    <option value="in-progress" {{ old('status') === 'in-progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Priority -->
            <div>
                <label for="priority" class="block text-sm font-medium text-gray-900">Priority <span class="text-red-500">*</span></label>
                <select name="priority" id="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2 {{ $errors->has('priority') ? 'border-red-500' : '' }}" required>
                    <option value="">Select priority</option>
                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
                @error('priority')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Assigned To -->
            <div>
                <label for="assigned_to" class="block text-sm font-medium text-gray-900">Assigned To</label>
                <select name="assigned_to" id="assigned_to" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2 {{ $errors->has('assigned_to') ? 'border-red-500' : '' }}">
                    <option value="">Select a user</option>
                    @foreach ($users ?? [] as $user)
                        <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
                @error('assigned_to')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Due Date -->
            <div>
                <label for="due_date" class="block text-sm font-medium text-gray-900">Due Date</label>
                <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2 {{ $errors->has('due_date') ? 'border-red-500' : '' }}">
                @error('due_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Estimated Hours -->
            <div>
                <label for="estimated_hours" class="block text-sm font-medium text-gray-900">Estimated Hours</label>
                <input type="number" name="estimated_hours" id="estimated_hours" step="0.5" min="0" value="{{ old('estimated_hours') }}" placeholder="0.0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2 {{ $errors->has('estimated_hours') ? 'border-red-500' : '' }}">
                @error('estimated_hours')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between border-t border-gray-200 pt-6">
                <a href="{{ route('admin.tasks.index') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Create Task
                </button>
            </div>
        </form>
    </div>
@endsection
