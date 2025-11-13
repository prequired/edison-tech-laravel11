@extends('layouts.app')

@section('title', 'Dashboard')

@section('header', 'My Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Statistics Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Active Projects Card -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Active Projects</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">
                            {{ $stats['active_projects'] ?? 0 }}
                        </p>
                        <p class="text-indigo-600 text-sm mt-2">Currently in progress</p>
                    </div>
                    <div class="bg-indigo-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
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
                        <p class="text-amber-600 text-sm mt-2">Awaiting your payment</p>
                    </div>
                    <div class="bg-amber-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Outstanding Card -->
        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Outstanding</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">
                            ${{ number_format($stats['total_outstanding'] ?? 0, 2) }}
                        </p>
                        <p class="text-red-600 text-sm mt-2">Due for payment</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Active Projects Section -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Active Projects</h2>
                </div>
                <div class="p-6 space-y-4">
                    @forelse($activeProjects ?? [] as $project)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 hover:shadow-sm transition-all">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $project['name'] ?? 'Untitled Project' }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $project['description'] ?? '' }}</p>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Progress</span>
                                    <span class="font-medium text-gray-900">{{ $project['progress'] ?? 0 }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ $project['progress'] ?? 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-gray-500">No active projects at the moment</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Documents Section -->
        <div>
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Documents</h2>
                </div>
                <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                    @forelse($recentDocuments ?? [] as $document)
                        <div class="px-6 py-4 hover:bg-gray-50 transition-colors cursor-pointer">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $document['name'] ?? 'Document' }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $document['type'] ?? 'File' }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $document['uploaded_at'] ?? 'N/A' }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center">
                            <p class="text-gray-500 text-sm">No recent documents</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Invoices Section -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Pending Invoices</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($pendingInvoices ?? [] as $invoice)
                    <div class="border border-amber-200 rounded-lg p-4 bg-amber-50">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <p class="text-sm text-gray-600">Invoice Number</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $invoice['number'] ?? '#0000' }}</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                Pending
                            </span>
                        </div>

                        <div class="space-y-3 border-t border-amber-200 pt-3 mb-4">
                            <div>
                                <p class="text-xs text-gray-600">Amount Due</p>
                                <p class="text-xl font-bold text-gray-900">${{ number_format($invoice['amount'] ?? 0, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Due Date</p>
                                <p class="text-sm font-medium text-gray-900">{{ $invoice['due_date'] ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <a href="#" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Pay Now
                        </a>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8">
                        <p class="text-gray-500">No pending invoices</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
