@extends('layouts.admin')

@section('content')
<div class="py-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $company->name }}</h1>
            <p class="text-gray-600 mt-1">View and manage company details</p>
        </div>
        <div class="flex gap-3">
            <a
                href="{{ route('admin.companies.edit', $company->id) }}"
                class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 font-medium flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
            <a
                href="{{ route('admin.companies.index') }}"
                class="px-6 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition duration-200 font-medium"
            >
                Back
            </a>
        </div>
    </div>

    <!-- Company Info Card -->
    <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Logo Section -->
            <div class="flex flex-col items-center justify-center">
                @if($company->logo)
                    <img
                        src="{{ asset('storage/' . $company->logo) }}"
                        alt="{{ $company->name }}"
                        class="w-32 h-32 rounded-lg object-cover shadow-md mb-4"
                    />
                @else
                    <div class="w-32 h-32 rounded-lg bg-indigo-100 flex items-center justify-center mb-4">
                        <svg class="w-16 h-16 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.5m0 0H9m0 0H3.5M9 3h6m0 0V1m0 2v-1m0 0h-3.5M9 3H5.5" />
                        </svg>
                    </div>
                @endif
                <p class="text-sm text-gray-500">{{ $company->name }}</p>
            </div>

            <!-- Company Information -->
            <div class="lg:col-span-2 space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <p class="text-sm font-medium text-gray-500">Company Name</p>
                        <p class="text-gray-900 font-semibold text-lg mt-1">{{ $company->name }}</p>
                    </div>

                    <!-- Status -->
                    <div>
                        <p class="text-sm font-medium text-gray-500">Status</p>
                        <div class="mt-1">
                            @if($company->active)
                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">
                                    <span class="w-2 h-2 bg-green-600 rounded-full"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 text-gray-800 text-sm font-semibold rounded-full">
                                    <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <hr />

                <div class="grid grid-cols-2 gap-6">
                    <!-- Email -->
                    <div>
                        <p class="text-sm font-medium text-gray-500">Email</p>
                        <p class="text-gray-900 font-medium mt-1">{{ $company->email }}</p>
                    </div>

                    <!-- Phone -->
                    <div>
                        <p class="text-sm font-medium text-gray-500">Phone</p>
                        <p class="text-gray-900 font-medium mt-1">{{ $company->phone ?? 'N/A' }}</p>
                    </div>
                </div>

                <hr />

                <div class="grid grid-cols-2 gap-6">
                    <!-- Website -->
                    <div>
                        <p class="text-sm font-medium text-gray-500">Website</p>
                        @if($company->website)
                            <a href="{{ $company->website }}" target="_blank" class="text-indigo-600 font-medium mt-1 hover:text-indigo-700">{{ $company->website }}</a>
                        @else
                            <p class="text-gray-500 mt-1">N/A</p>
                        @endif
                    </div>

                    <!-- Tax ID -->
                    <div>
                        <p class="text-sm font-medium text-gray-500">Tax ID</p>
                        <p class="text-gray-900 font-medium mt-1">{{ $company->tax_id ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Section -->
        <hr class="mb-6" />
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Main Address -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Address</h3>
                <div class="space-y-2 text-gray-600">
                    <p>{{ $company->address ?? 'N/A' }}</p>
                    <p>{{ $company->city ?? '' }}{{ $company->city && $company->state ? ', ' : '' }}{{ $company->state ?? '' }}</p>
                    <p>{{ $company->country ?? '' }} {{ $company->postal_code ?? '' }}</p>
                </div>
            </div>

            <!-- Billing Address -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Billing Address</h3>
                <div class="space-y-2 text-gray-600">
                    <p>{{ $company->billing_address ?? 'Same as main address' }}</p>
                    <p>{{ $company->billing_city ?? $company->city ?? '' }}{{ ($company->billing_city ?? $company->city) && ($company->billing_state ?? $company->state) ? ', ' : '' }}{{ $company->billing_state ?? $company->state ?? '' }}</p>
                    <p>{{ $company->billing_country ?? $company->country ?? '' }} {{ $company->billing_postal_code ?? $company->postal_code ?? '' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Projects -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-indigo-600">
            <p class="text-gray-600 text-sm font-medium">Total Projects</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $company->projects()->count() }}</p>
        </div>

        <!-- Active Projects -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-600">
            <p class="text-gray-600 text-sm font-medium">Active Projects</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $company->projects()->where('status', 'active')->count() }}</p>
        </div>

        <!-- Total Invoices -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-600">
            <p class="text-gray-600 text-sm font-medium">Total Invoices</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $company->invoices()->count() }}</p>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-600">
            <p class="text-gray-600 text-sm font-medium">Total Revenue</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">${{ number_format($company->invoices()->sum('total_amount') ?? 0, 2) }}</p>
        </div>
    </div>

    <!-- Active Projects Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Active Projects</h2>
        @if($company->projects()->where('status', 'active')->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Project Name</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Status</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Progress</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Budget</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($company->projects()->where('status', 'active')->get() as $project)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-900 font-medium">{{ $project->name }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-16 bg-gray-200 rounded-full h-2">
                                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $project->progress ?? 0 }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-700">{{ $project->progress ?? 0 }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-900 font-medium">${{ number_format($project->budget ?? 0, 2) }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.projects.show', $project->id) }}" class="text-indigo-600 hover:text-indigo-700 font-medium">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500">No active projects found.</p>
        @endif
    </div>

    <!-- Recent Invoices Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Recent Invoices</h2>
        @if($company->invoices()->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Invoice #</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Date</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Amount</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Status</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($company->invoices()->latest()->limit(10)->get() as $invoice)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-900 font-medium">{{ $invoice->invoice_number ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $invoice->created_at?->format('M d, Y') ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-900 font-medium">${{ number_format($invoice->total_amount ?? 0, 2) }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusColors = [
                                            'draft' => 'bg-gray-100 text-gray-800',
                                            'sent' => 'bg-blue-100 text-blue-800',
                                            'paid' => 'bg-green-100 text-green-800',
                                            'overdue' => 'bg-red-100 text-red-800',
                                            'cancelled' => 'bg-gray-100 text-gray-800',
                                        ];
                                        $statusClass = $statusColors[$invoice->status ?? 'draft'] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ ucfirst($invoice->status ?? 'draft') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="text-indigo-600 hover:text-indigo-700 font-medium">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500">No invoices found.</p>
        @endif
    </div>

    <!-- Company Users Section -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Company Users</h2>
        @if($company->users()->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Name</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Email</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Role</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($company->users()->get() as $user)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-900 font-medium">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ ucfirst($user->role ?? 'user') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($user->email_verified_at)
                                        <span class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <span class="w-2 h-2 bg-green-600 rounded-full"></span>
                                            Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <span class="w-2 h-2 bg-yellow-600 rounded-full"></span>
                                            Pending
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500">No users assigned to this company.</p>
        @endif
    </div>
</div>
@endsection
