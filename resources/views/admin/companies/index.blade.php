@extends('layouts.admin')

@section('content')
<div class="py-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Companies</h1>
            <p class="text-gray-600 mt-1">Manage all companies and their details</p>
        </div>
        <a
            href="{{ route('admin.companies.create') }}"
            class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 font-medium flex items-center gap-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Company
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Companies -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-indigo-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Companies</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalCompanies ?? 0 }}</p>
                </div>
                <div class="bg-indigo-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.5m0 0H9m0 0H3.5M9 3h6m0 0V1m0 2v-1m0 0h-3.5M9 3H5.5M3 21h18" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Companies -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Active Companies</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $activeCompanies ?? 0 }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- With Active Projects -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">With Active Projects</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $companiesWithProjects ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('admin.companies.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input
                        type="text"
                        name="search"
                        placeholder="Search by name, email, or phone..."
                        value="{{ request('search') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    />
                </div>

                <!-- Active Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select
                        name="active"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    >
                        <option value="">All Statuses</option>
                        <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end gap-2">
                    <button
                        type="submit"
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium"
                    >
                        Filter
                    </button>
                    <a
                        href="{{ route('admin.companies.index') }}"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition font-medium text-center"
                    >
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Companies Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($companies->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Company Name</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Email</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Phone</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Projects</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($companies as $company)
                            <tr class="hover:bg-gray-50 transition duration-200">
                                <!-- Company Name with Logo -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($company->logo)
                                            <img
                                                src="{{ asset('storage/' . $company->logo) }}"
                                                alt="{{ $company->name }}"
                                                class="w-10 h-10 rounded-lg object-cover bg-gray-100"
                                            />
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.5m0 0H9m0 0H3.5M9 3h6m0 0V1m0 2v-1m0 0h-3.5M9 3H5.5" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $company->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $company->tax_id ? 'Tax ID: ' . $company->tax_id : 'No tax ID' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600">{{ $company->email }}</p>
                                </td>

                                <!-- Phone -->
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600">{{ $company->phone ?? 'N/A' }}</p>
                                </td>

                                <!-- Projects Count -->
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        {{ $company->projects_count ?? $company->projects()->count() }}
                                    </span>
                                </td>

                                <!-- Status Badge -->
                                <td class="px-6 py-4">
                                    @if($company->active)
                                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                            <span class="w-2 h-2 bg-green-600 rounded-full"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">
                                            <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <a
                                            href="{{ route('admin.companies.show', $company->id) }}"
                                            class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition duration-200"
                                            title="View"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a
                                            href="{{ route('admin.companies.edit', $company->id) }}"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition duration-200"
                                            title="Edit"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form
                                            action="{{ route('admin.companies.destroy', $company->id) }}"
                                            method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Are you sure you want to delete this company? All associated data will be permanently removed.')"
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
                {{ $companies->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.5m0 0H9m0 0H3.5M9 3h6m0 0V1m0 2v-1m0 0h-3.5M9 3H5.5" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900">No companies found</h3>
                <p class="text-gray-500 mt-1">Get started by creating your first company.</p>
                <a
                    href="{{ route('admin.companies.create') }}"
                    class="inline-block mt-4 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 font-medium"
                >
                    Create Company
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
