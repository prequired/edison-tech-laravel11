@extends('layouts.admin')

@section('title', 'Dashboard')

@section('header', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Statistics Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Projects Card -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Projects</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">
                            {{ $stats['total_projects'] ?? 0 }}
                        </p>
                        <p class="text-green-600 text-sm mt-2">+12% from last month</p>
                    </div>
                    <div class="bg-indigo-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Projects Card -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Active Projects</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">
                            {{ $stats['active_projects'] ?? 0 }}
                        </p>
                        <p class="text-green-600 text-sm mt-2">{{ $stats['active_projects'] ?? 0 }} in progress</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue Card -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Revenue</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">
                            ${{ number_format($stats['total_revenue'] ?? 0, 0) }}
                        </p>
                        <p class="text-green-600 text-sm mt-2">+8% from last month</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Invoices Card -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Pending Invoices</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">
                            {{ $stats['pending_invoices'] ?? 0 }}
                        </p>
                        <p class="text-amber-600 text-sm mt-2">Awaiting payment</p>
                    </div>
                    <div class="bg-amber-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Projects Section -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Recent Projects</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Project Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Company</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Progress</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentProjects ?? [] as $project)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $project['name'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $project['company'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm">
                                @switch($project['status'] ?? 'pending')
                                    @case('active')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                        @break
                                    @case('completed')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Completed
                                        </span>
                                        @break
                                    @case('pending')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            Pending
                                        </span>
                                        @break
                                    @case('on_hold')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            On Hold
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $project['status'] ?? 'Unknown' }}
                                        </span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $project['progress'] ?? 0 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-600 mt-1">{{ $project['progress'] ?? 0 }}%</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                <p>No recent projects found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Invoices Section -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Recent Invoices</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Company</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentInvoices ?? [] as $invoice)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-indigo-600">{{ $invoice['number'] ?? '#0000' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $invoice['company'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">${{ number_format($invoice['amount'] ?? 0, 2) }}</td>
                            <td class="px-6 py-4 text-sm">
                                @switch($invoice['status'] ?? 'pending')
                                    @case('paid')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Paid
                                        </span>
                                        @break
                                    @case('pending')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            Pending
                                        </span>
                                        @break
                                    @case('overdue')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Overdue
                                        </span>
                                        @break
                                    @case('cancelled')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Cancelled
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $invoice['status'] ?? 'Unknown' }}
                                        </span>
                                @endswitch
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                <p>No recent invoices found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
