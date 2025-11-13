@extends('layouts.admin')

@section('content')
<div class="py-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Create Project</h1>
            <p class="text-gray-600 mt-1">Add a new project to your portfolio</p>
        </div>
        <a
            href="{{ route('admin.projects.index') }}"
            class="px-6 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition duration-200 font-medium"
        >
            Back
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm p-8">
        <form action="{{ route('admin.projects.store') }}" method="POST">
            @csrf

            <!-- Company and Name Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Company Select -->
                <div>
                    <label for="company_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Company <span class="text-red-600">*</span>
                    </label>
                    <select
                        id="company_id"
                        name="company_id"
                        required
                        class="w-full px-4 py-3 border {{ $errors->has('company_id') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                        <option value="">Select a company</option>
                        @forelse($companies ?? [] as $company)
                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @empty
                        @endforelse
                    </select>
                    @error('company_id')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Project Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Project Name <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        required
                        value="{{ old('name') }}"
                        placeholder="Enter project name"
                        class="w-full px-4 py-3 border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    />
                    @error('name')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    placeholder="Enter project description..."
                    class="w-full px-4 py-3 border {{ $errors->has('description') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status and Priority Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-600">*</span>
                    </label>
                    <select
                        id="status"
                        name="status"
                        required
                        class="w-full px-4 py-3 border {{ $errors->has('status') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                        <option value="">Select status</option>
                        <option value="planning" {{ old('status') === 'planning' ? 'selected' : '' }}>Planning</option>
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="on-hold" {{ old('status') === 'on-hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                        Priority <span class="text-red-600">*</span>
                    </label>
                    <select
                        id="priority"
                        name="priority"
                        required
                        class="w-full px-4 py-3 border {{ $errors->has('priority') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                        <option value="">Select priority</option>
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                    @error('priority')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Budget and Estimated Hours Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Budget -->
                <div>
                    <label for="budget" class="block text-sm font-medium text-gray-700 mb-2">Budget ($)</label>
                    <input
                        type="number"
                        id="budget"
                        name="budget"
                        step="0.01"
                        min="0"
                        value="{{ old('budget') }}"
                        placeholder="0.00"
                        class="w-full px-4 py-3 border {{ $errors->has('budget') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    />
                    @error('budget')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estimated Hours -->
                <div>
                    <label for="estimated_hours" class="block text-sm font-medium text-gray-700 mb-2">Estimated Hours</label>
                    <input
                        type="number"
                        id="estimated_hours"
                        name="estimated_hours"
                        step="0.5"
                        min="0"
                        value="{{ old('estimated_hours') }}"
                        placeholder="0"
                        class="w-full px-4 py-3 border {{ $errors->has('estimated_hours') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    />
                    @error('estimated_hours')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Start Date and Deadline Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input
                        type="date"
                        id="start_date"
                        name="start_date"
                        value="{{ old('start_date') }}"
                        class="w-full px-4 py-3 border {{ $errors->has('start_date') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    />
                    @error('start_date')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deadline -->
                <div>
                    <label for="deadline" class="block text-sm font-medium text-gray-700 mb-2">Deadline</label>
                    <input
                        type="date"
                        id="deadline"
                        name="deadline"
                        value="{{ old('deadline') }}"
                        class="w-full px-4 py-3 border {{ $errors->has('deadline') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    />
                    @error('deadline')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Hourly Rate -->
            <div class="mb-6">
                <label for="hourly_rate" class="block text-sm font-medium text-gray-700 mb-2">Hourly Rate ($)</label>
                <input
                    type="number"
                    id="hourly_rate"
                    name="hourly_rate"
                    step="0.01"
                    min="0"
                    value="{{ old('hourly_rate') }}"
                    placeholder="0.00"
                    class="w-full px-4 py-3 border {{ $errors->has('hourly_rate') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                />
                @error('hourly_rate')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Technologies -->
            <div class="mb-6">
                <label for="technologies" class="block text-sm font-medium text-gray-700 mb-2">Technologies</label>
                <textarea
                    id="technologies"
                    name="technologies"
                    rows="3"
                    placeholder="Enter technologies separated by commas (e.g., Laravel, Vue.js, MySQL)"
                    class="w-full px-4 py-3 border {{ $errors->has('technologies') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-mono text-sm"
                >{{ old('technologies') }}</textarea>
                <p class="text-sm text-gray-500 mt-1">Separate multiple technologies with commas</p>
                @error('technologies')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Is Billable Checkbox -->
            <div class="mb-8">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input
                        type="checkbox"
                        name="is_billable"
                        value="1"
                        {{ old('is_billable') ? 'checked' : '' }}
                        class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-2 focus:ring-indigo-500"
                    />
                    <span class="text-sm font-medium text-gray-700">This project is billable</span>
                </label>
                @error('is_billable')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <a
                    href="{{ route('admin.projects.index') }}"
                    class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition duration-200 font-medium"
                >
                    Cancel
                </a>
                <button
                    type="submit"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 font-medium"
                >
                    Create Project
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
