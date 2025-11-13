@extends('layouts.admin')

@section('title', 'Analytics Dashboard')
@section('header', 'Analytics Dashboard')

@section('content')
    <!-- Overview Stats -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="text-sm font-medium text-gray-500">Total Revenue</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">${{ number_format($totalRevenue, 2) }}</dd>
            <dd class="mt-1 text-sm text-green-600">{{ $revenueGrowth }}% from last month</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="text-sm font-medium text-gray-500">Active Projects</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $activeProjects }}</dd>
            <dd class="mt-1 text-sm text-gray-500">{{ $totalProjects }} total</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="text-sm font-medium text-gray-500">Total Clients</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalClients }}</dd>
            <dd class="mt-1 text-sm text-gray-500">{{ $activeClients }} active</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="text-sm font-medium text-gray-500">Pending Invoices</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">${{ number_format($pendingInvoices, 2) }}</dd>
            <dd class="mt-1 text-sm text-gray-500">{{ $pendingInvoiceCount }} invoices</dd>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 mb-6">
        <a href="{{ route('admin.analytics.projects') }}" class="rounded-lg bg-white p-6 shadow hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-12 w-12 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div class="ml-5">
                    <h3 class="text-lg font-medium text-gray-900">Project Analytics</h3>
                    <p class="mt-1 text-sm text-gray-500">View detailed project statistics and trends</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.analytics.financial') }}" class="rounded-lg bg-white p-6 shadow hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-5">
                    <h3 class="text-lg font-medium text-gray-900">Financial Analytics</h3>
                    <p class="mt-1 text-sm text-gray-500">View revenue, invoices, and payment analytics</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Recent Activity -->
    <div class="rounded-lg bg-white shadow">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
        </div>
        <ul role="list" class="divide-y divide-gray-200">
            @forelse($recentActivity ?? [] as $activity)
                <li class="px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">{{ $activity['message'] }}</p>
                            <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-6 py-12 text-center text-sm text-gray-500">No recent activity</li>
            @endforelse
        </ul>
    </div>
@endsection
