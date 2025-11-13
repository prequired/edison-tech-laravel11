@extends('layouts.app')

@section('title', 'My Invoices')

@section('header', 'Invoices')

@section('content')
    <div class="space-y-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <!-- Total Amount Card -->
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-500">Total Amount</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            ${{ number_format($totalAmount, 2) }}
                        </p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-md bg-indigo-100">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Paid Amount Card -->
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-500">Paid Amount</p>
                        <p class="mt-2 text-3xl font-bold text-green-600">
                            ${{ number_format($paidAmount, 2) }}
                        </p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-md bg-green-100">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pending Amount Card -->
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-500">Pending Amount</p>
                        <p class="mt-2 text-3xl font-bold text-amber-600">
                            ${{ number_format($pendingAmount, 2) }}
                        </p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-md bg-amber-100">
                        <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white px-4 py-5 shadow rounded-lg sm:px-6">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
                    <select id="status-filter" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                        <option value="">All Invoices</option>
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                        <option value="overdue">Overdue</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="button" class="w-full sm:w-auto rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Invoices Grid -->
        @if($invoices->count() > 0)
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                @foreach($invoices as $invoice)
                    <div class="overflow-hidden rounded-lg bg-white shadow hover:shadow-lg transition-shadow">
                        <div class="px-4 py-5 sm:px-6">
                            <!-- Invoice Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">
                                        Invoice #{{ $invoice->number }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        @php
                                            $issuedDate = \Carbon\Carbon::parse($invoice->issued_date);
                                            $dueDate = \Carbon\Carbon::parse($invoice->due_date);
                                        @endphp
                                        Issued: {{ $issuedDate->format('M d, Y') }} | Due: {{ $dueDate->format('M d, Y') }}
                                    </p>
                                </div>
                                <!-- Status Badge -->
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium @switch($invoice->status) @case('paid') bg-green-100 text-green-800 @break @case('pending') bg-amber-100 text-amber-800 @break @case('overdue') bg-red-100 text-red-800 @break @default bg-gray-100 text-gray-800 @endswitch">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </div>

                            <!-- Amount -->
                            <div class="mb-4 border-t border-gray-200 pt-4">
                                <p class="text-sm text-gray-500">Total Amount</p>
                                <p class="text-4xl font-bold text-indigo-600">
                                    ${{ number_format($invoice->total, 2) }}
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-3 border-t border-gray-200 pt-4">
                                <a href="{{ route('client.invoices.show', $invoice->id) }}" class="flex-1 rounded-md bg-indigo-600 px-4 py-2 text-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    View Invoice
                                </a>
                                @if($invoice->status !== 'paid')
                                    <a href="{{ route('client.invoices.pay', $invoice->id) }}" class="flex-1 rounded-md border-2 border-indigo-600 px-4 py-2 text-center text-sm font-medium text-indigo-600 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        Pay Now
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-lg bg-white px-4 py-12 text-center shadow">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No invoices</h3>
                <p class="mt-1 text-sm text-gray-500">You don't have any invoices yet.</p>
            </div>
        @endif
    </div>

    <script>
        document.getElementById('status-filter').addEventListener('change', function(e) {
            const status = e.target.value;
            const url = new URL(window.location);
            if (status) {
                url.searchParams.set('status', status);
            } else {
                url.searchParams.delete('status');
            }
            window.location = url;
        });
    </script>
@endsection
