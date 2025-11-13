@extends('layouts.app')

@section('title', 'Invoice #' . $invoice->number)

@section('header')
    <div class="flex items-center justify-between">
        <h1>Invoice #{{ $invoice->number }}</h1>
        <div class="flex gap-3">
            <button type="button" class="rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <svg class="inline h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8m0 8l-9-2m9 2l9-2m-9-8l9 2m-9-2l-9 2m9-2v8m0 0l-9-2m9 2l9-2" />
                </svg>
                Download PDF
            </button>
            @if($invoice->status !== 'paid')
                <a href="{{ route('client.invoices.pay', $invoice->id) }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg class="inline h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Pay Now
                </a>
            @endif
        </div>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Status Badge -->
        <div class="flex items-center gap-4">
            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium @switch($invoice->status) @case('paid') bg-green-100 text-green-800 @break @case('pending') bg-amber-100 text-amber-800 @break @case('overdue') bg-red-100 text-red-800 @break @default bg-gray-100 text-gray-800 @endswitch">
                {{ ucfirst($invoice->status) }}
            </span>
        </div>

        <!-- Invoice Card -->
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="px-4 py-6 sm:px-6">
                <!-- Invoice Header Info -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 mb-8">
                    <!-- Company Info -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">From</h3>
                        <div class="space-y-1">
                            <p class="text-lg font-bold text-gray-900">{{ config('app.name', 'Edison Tech') }}</p>
                            <p class="text-sm text-gray-600">{{ $invoice->company_address ?? '123 Business Street' }}</p>
                            <p class="text-sm text-gray-600">{{ $invoice->company_city ?? 'New York, NY 10001' }}</p>
                            <p class="text-sm text-gray-600">{{ $invoice->company_phone ?? '+1 (555) 123-4567' }}</p>
                            <p class="text-sm text-gray-600">{{ $invoice->company_email ?? config('mail.from.address') }}</p>
                        </div>
                    </div>

                    <!-- Client Info -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">Bill To</h3>
                        <div class="space-y-1">
                            <p class="text-lg font-bold text-gray-900">{{ $invoice->client_name ?? auth()->user()->name }}</p>
                            <p class="text-sm text-gray-600">{{ $invoice->client_address ?? auth()->user()->email }}</p>
                            <p class="text-sm text-gray-600">{{ $invoice->client_city ?? '' }}</p>
                            <p class="text-sm text-gray-600">{{ $invoice->client_phone ?? '' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Invoice Metadata -->
                <div class="grid grid-cols-2 gap-6 border-t border-gray-200 pt-8 mb-8 sm:grid-cols-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Invoice Number</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $invoice->number }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Invoice Date</p>
                        <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($invoice->issued_date)->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Due Date</p>
                        <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Amount Due</p>
                        <p class="text-lg font-semibold text-indigo-600">${{ number_format($invoice->total, 2) }}</p>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="border-t border-gray-200 pt-8 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Invoice Items</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-900">Description</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-900">Quantity</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-900">Unit Price</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-900">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoice->items ?? [] as $item)
                                    <tr class="border-b border-gray-100">
                                        <td class="px-4 py-3 text-gray-900">{{ $item->description }}</td>
                                        <td class="px-4 py-3 text-right text-gray-600">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-right text-gray-600">${{ number_format($item->unit_price, 2) }}</td>
                                        <td class="px-4 py-3 text-right font-semibold text-gray-900">${{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-3 text-center text-gray-500">No items</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Summary -->
                <div class="border-t border-gray-200 pt-8 mb-8">
                    <div class="flex justify-end">
                        <div class="w-full sm:w-80">
                            <div class="flex justify-between mb-4">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-semibold text-gray-900">${{ number_format($invoice->subtotal ?? $invoice->total, 2) }}</span>
                            </div>
                            @if(($invoice->tax ?? 0) > 0)
                                <div class="flex justify-between mb-4">
                                    <span class="text-gray-600">Tax</span>
                                    <span class="font-semibold text-gray-900">${{ number_format($invoice->tax, 2) }}</span>
                                </div>
                            @endif
                            @if(($invoice->discount ?? 0) > 0)
                                <div class="flex justify-between mb-4">
                                    <span class="text-gray-600">Discount</span>
                                    <span class="font-semibold text-gray-900">-${{ number_format($invoice->discount, 2) }}</span>
                                </div>
                            @endif
                            <div class="border-t border-gray-200 pt-4 flex justify-between">
                                <span class="font-semibold text-gray-900">Total</span>
                                <span class="text-2xl font-bold text-indigo-600">${{ number_format($invoice->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                @if($invoice->payments && $invoice->payments->count() > 0)
                    <div class="border-t border-gray-200 pt-8 mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment History</h3>
                        <div class="space-y-4">
                            @foreach($invoice->payments as $payment)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-semibold text-gray-900">Payment: ${{ number_format($payment->amount, 2) }}</p>
                                        <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($payment->paid_date)->format('M d, Y') }}</p>
                                        <p class="text-sm text-gray-500">{{ $payment->payment_method ?? 'Payment' }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        Paid
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Notes Section -->
                @if($invoice->notes)
                    <div class="border-t border-gray-200 pt-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Notes</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $invoice->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 justify-center">
            <a href="{{ route('client.invoices.index') }}" class="rounded-md bg-gray-200 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                Back to Invoices
            </a>
            <button type="button" class="rounded-md bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <svg class="inline h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download PDF
            </button>
        </div>
    </div>
@endsection
