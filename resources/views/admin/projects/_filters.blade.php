<!-- Project Filters Component -->
<div class="mb-6 bg-white rounded-lg shadow-sm p-6">
    <form method="GET" action="{{ route('admin.projects.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <!-- Search Filter -->
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input
                type="text"
                id="search"
                name="search"
                placeholder="Search by name or company..."
                value="{{ request('search') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
        </div>

        <!-- Status Filter -->
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select
                id="status"
                name="status"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
                <option value="">All Statuses</option>
                <option value="planning" {{ request('status') === 'planning' ? 'selected' : '' }}>Planning</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="on-hold" {{ request('status') === 'on-hold' ? 'selected' : '' }}>On Hold</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <!-- Priority Filter -->
        <div>
            <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
            <select
                id="priority"
                name="priority"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
                <option value="">All Priorities</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
            </select>
        </div>

        <!-- Company Filter -->
        <div>
            <label for="company" class="block text-sm font-medium text-gray-700 mb-2">Company</label>
            <select
                id="company"
                name="company_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
                <option value="">All Companies</option>
                @forelse($companies ?? [] as $company)
                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                @empty
                @endforelse
            </select>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-end gap-2 md:col-span-4">
            <button
                type="submit"
                class="flex-1 md:flex-initial px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 font-medium"
            >
                Filter
            </button>
            <a
                href="{{ route('admin.projects.index') }}"
                class="flex-1 md:flex-initial px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition duration-200 font-medium text-center"
            >
                Clear
            </a>
        </div>
    </form>
</div>
