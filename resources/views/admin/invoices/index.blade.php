@extends('layouts.admin')

@section('content')
<div class="py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Invoices</h1>
                <p class="text-gray-500 mt-2">Manage and track all client invoices</p>
            </div>
            <a href="{{ route('admin.invoices.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Invoice
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Revenue</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">${{ number_format($totalRevenue, 2) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Amount -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Pending Amount</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">${{ number_format($pendingAmount, 2) }}</p>
                </div>
                <div class="bg-amber-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Overdue Amount -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Overdue Amount</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">${{ number_format($overdueAmount, 2) }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 5v1m0-1c4.418 0 8-1.79 8-4s-3.582-4-8-4-8 1.79-8 4 3.582 4 8 4zm0-12C6.48 2 2 4.462 2 7.5c0 1.762.664 3.4 1.88 4.823l-.9 5.08c-.045.25.122.51.36.63.24.12.56.03.7-.14l2.7-3.03C6.96 14.38 9.35 15 12 15s5.04-.62 6.86-1.73l2.7 3.03c.14.17.46.26.7.14.238-.12.405-.38.36-.63l-.9-5.08C21.336 10.9 22 9.262 22 7.5 22 4.462 17.52 2 12 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form action="{{ route('admin.invoices.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Invoice # or company..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                    Filter
                </button>
                <a href="{{ route('admin.invoices.index') }}" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200 text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Company</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Issue Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-gray-900">#{{ $invoice->invoice_number }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-gray-900">{{ $invoice->company->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-gray-600">{{ $invoice->project->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-gray-600">{{ $invoice->issue_date->format('M d, Y') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-gray-600">{{ $invoice->due_date->format('M d, Y') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-gray-900">${{ number_format($invoice->total_amount, 2) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($invoice->status === 'paid')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        Paid
                                    </span>
                                @elseif($invoice->status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                                        Pending
                                    </span>
                                @elseif($invoice->status === 'overdue')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        Overdue
                                    </span>
                                @elseif($invoice->status === 'draft')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        Draft
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        Cancelled
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-900 font-medium" title="View">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.invoices.edit', $invoice) }}" class="text-indigo-600 hover:text-indigo-900 font-medium" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.invoices.download', $invoice) }}" class="text-green-600 hover:text-green-900 font-medium" title="Download">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </a>
                                    @if($invoice->status !== 'paid')
                                        <form action="{{ route('admin.invoices.mark-paid', $invoice) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="text-purple-600 hover:text-purple-900 font-medium" title="Mark as Paid">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-gray-500 text-lg">No invoices found</p>
                                    <p class="text-gray-400 text-sm">Try adjusting your filters or create a new invoice</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($invoices->hasPages())
        <div class="mt-6">
            {{ $invoices->render() }}
        </div>
    @endif
</div>
@endsection
