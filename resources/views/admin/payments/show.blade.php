@extends('layouts.admin')

@section('content')
<div class="py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Payment #{{ $payment->payment_number }}</h1>
                <p class="text-gray-500 mt-2">Payment details and transaction information</p>
            </div>
            @if($payment->status === 'completed')
                <button onclick="openRefundModal()" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"></path>
                    </svg>
                    Refund Payment
                </button>
            @endif
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-6">
        @if($payment->status === 'completed')
            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold bg-green-100 text-green-800">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Completed
            </span>
        @elseif($payment->status === 'pending')
            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold bg-amber-100 text-amber-800">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                Pending
            </span>
        @elseif($payment->status === 'failed')
            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold bg-red-100 text-red-800">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                Failed
            </span>
        @elseif($payment->status === 'refunded')
            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold bg-gray-100 text-gray-800">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.947.484 5.002 5.002 0 00-8.308-1.979V5a1 1 0 01-2 0V2a1 1 0 011-1zm.008 9a1 1 0 01.992.009l-.008 0a1 1 0 01-.992-.009zm5.5 0a1 1 0 011 1v2.101a7.002 7.002 0 01-11.601 2.566 1 1 0 101.947-.484 5.002 5.002 0 008.308 1.979V15a1 1 0 012 0v3a1 1 0 01-1 1h-3a1 1 0 110-2h2.101A7.002 7.002 0 014.399 13.434a1 1 0 11-1.947.484 5.002 5.002 0 008.308-1.979V11a1 1 0 011-1z" clip-rule="evenodd"></path>
                </svg>
                Refunded
            </span>
        @else
            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold bg-gray-100 text-gray-800">
                {{ ucfirst($payment->status) }}
            </span>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Payment Information Card -->
            <div class="bg-white rounded-lg shadow p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Payment Information</h2>
                <div class="space-y-6">
                    <!-- Payment Date Row -->
                    <div class="flex justify-between pb-6 border-b border-gray-200">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1">Payment Date</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $payment->payment_date->format('M d, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500 font-medium mb-1">Payment Number</p>
                            <p class="text-lg font-semibold text-gray-900">#{{ $payment->payment_number }}</p>
                        </div>
                    </div>

                    <!-- Amount Row -->
                    <div class="flex justify-between pb-6 border-b border-gray-200">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1">Payment Amount</p>
                            <p class="text-3xl font-bold text-green-600">${{ number_format($payment->amount, 2) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500 font-medium mb-1">Status</p>
                            <div class="mt-1">
                                @if($payment->status === 'completed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        Completed
                                    </span>
                                @elseif($payment->status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                                        Pending
                                    </span>
                                @elseif($payment->status === 'failed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        Failed
                                    </span>
                                @elseif($payment->status === 'refunded')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        Refunded
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Row -->
                    <div class="flex justify-between pb-6 border-b border-gray-200">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1">Payment Method</p>
                            <div class="mt-1">
                                @switch($payment->payment_method)
                                    @case('bank_transfer')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Bank Transfer
                                        </span>
                                        @break
                                    @case('credit_card')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            Credit Card
                                        </span>
                                        @break
                                    @case('check')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Check
                                        </span>
                                        @break
                                    @case('stripe')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            Stripe
                                        </span>
                                        @break
                                    @case('paypal')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            PayPal
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                        </span>
                                @endswitch
                            </div>
                        </div>
                    </div>

                    <!-- Transaction ID Row -->
                    @if($payment->transaction_id)
                        <div class="pb-6 border-b border-gray-200">
                            <p class="text-sm text-gray-500 font-medium mb-2">Transaction ID</p>
                            <p class="text-gray-900 font-mono text-sm bg-gray-50 p-3 rounded">{{ $payment->transaction_id }}</p>
                        </div>
                    @endif

                    <!-- Stripe Details (if applicable) -->
                    @if($payment->stripe_charge_id || $payment->stripe_payment_intent_id)
                        <div class="pb-6 border-b border-gray-200">
                            <p class="text-sm text-gray-500 font-medium mb-3">Stripe Transaction Details</p>
                            <div class="space-y-2">
                                @if($payment->stripe_payment_intent_id)
                                    <div>
                                        <p class="text-xs text-gray-500">Payment Intent ID</p>
                                        <p class="text-gray-900 font-mono text-xs bg-gray-50 p-2 rounded">{{ $payment->stripe_payment_intent_id }}</p>
                                    </div>
                                @endif
                                @if($payment->stripe_charge_id)
                                    <div>
                                        <p class="text-xs text-gray-500">Charge ID</p>
                                        <p class="text-gray-900 font-mono text-xs bg-gray-50 p-2 rounded">{{ $payment->stripe_charge_id }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Notes -->
                    @if($payment->notes)
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-2">Notes</p>
                            <p class="text-gray-900">{{ $payment->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Invoice Details Card -->
            <div class="bg-white rounded-lg shadow p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Invoice Details</h2>
                <div class="space-y-4">
                    <!-- Invoice Number -->
                    <div class="flex justify-between pb-4 border-b border-gray-200">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1">Invoice Number</p>
                            <p class="text-lg font-semibold text-gray-900">#{{ $payment->invoice->invoice_number }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500 font-medium mb-1">Invoice Status</p>
                            <div class="mt-1">
                                @if($payment->invoice->status === 'paid')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        Paid
                                    </span>
                                @elseif($payment->invoice->status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                                        Pending
                                    </span>
                                @elseif($payment->invoice->status === 'overdue')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        Overdue
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        {{ ucfirst($payment->invoice->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Company -->
                    <div class="pb-4 border-b border-gray-200">
                        <p class="text-sm text-gray-500 font-medium mb-1">Company</p>
                        <p class="text-gray-900 font-semibold">{{ $payment->invoice->company->name ?? 'N/A' }}</p>
                        <p class="text-gray-600 text-sm mt-1">{{ $payment->invoice->company->email ?? '' }}</p>
                    </div>

                    <!-- Project -->
                    <div class="pb-4 border-b border-gray-200">
                        <p class="text-sm text-gray-500 font-medium mb-1">Project</p>
                        <p class="text-gray-900 font-semibold">{{ $payment->invoice->project->name ?? 'N/A' }}</p>
                    </div>

                    <!-- Invoice Amount -->
                    <div class="flex justify-between">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1">Invoice Amount</p>
                            <p class="text-2xl font-bold text-gray-900">${{ number_format($payment->invoice->total_amount, 2) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500 font-medium mb-1">Invoice Date</p>
                            <p class="text-gray-900 font-semibold">{{ $payment->invoice->issue_date->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- View Invoice Button -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.invoices.show', $payment->invoice) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        View Invoice
                    </a>
                </div>
            </div>

            <!-- Refund Details (if refunded) -->
            @if($payment->status === 'refunded' && $payment->metadata && isset($payment->metadata['refund_amount']))
                <div class="bg-white rounded-lg shadow p-8 border-l-4 border-gray-500">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Refund Details</h2>
                    <div class="space-y-4">
                        <!-- Refund Amount -->
                        <div class="flex justify-between pb-4 border-b border-gray-200">
                            <div>
                                <p class="text-sm text-gray-500 font-medium mb-1">Refund Amount</p>
                                <p class="text-2xl font-bold text-red-600">${{ number_format($payment->metadata['refund_amount'] ?? 0, 2) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500 font-medium mb-1">Refund Date</p>
                                <p class="text-gray-900 font-semibold">
                                    @if(isset($payment->metadata['refunded_at']))
                                        {{ \Carbon\Carbon::parse($payment->metadata['refunded_at'])->format('M d, Y') }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Refund Reason -->
                        @if(isset($payment->metadata['refund_reason']))
                            <div>
                                <p class="text-sm text-gray-500 font-medium mb-2">Refund Reason</p>
                                <p class="text-gray-900">{{ $payment->metadata['refund_reason'] }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Transaction Details Card -->
            <div class="bg-white rounded-lg shadow p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Transaction History</h2>
                <div class="space-y-4">
                    <!-- Created At -->
                    <div class="flex justify-between pb-4 border-b border-gray-200">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Created</p>
                            <p class="text-gray-900 font-semibold">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>

                    <!-- Updated At -->
                    <div class="flex justify-between">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Last Updated</p>
                            <p class="text-gray-900 font-semibold">{{ $payment->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Info Card -->
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg shadow p-6 border border-indigo-200">
                <h3 class="text-lg font-bold text-indigo-900 mb-4">Quick Summary</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-indigo-600 font-semibold uppercase tracking-wide mb-1">Payment ID</p>
                        <p class="text-indigo-900 font-mono text-sm">{{ $payment->id }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-indigo-600 font-semibold uppercase tracking-wide mb-1">Amount</p>
                        <p class="text-2xl font-bold text-indigo-900">${{ number_format($payment->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-indigo-600 font-semibold uppercase tracking-wide mb-1">Method</p>
                        <p class="text-indigo-900 font-semibold capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-indigo-600 font-semibold uppercase tracking-wide mb-1">Status</p>
                        <div class="mt-1">
                            @if($payment->status === 'completed')
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                    Completed
                                </span>
                            @elseif($payment->status === 'pending')
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-amber-100 text-amber-800">
                                    Pending
                                </span>
                            @elseif($payment->status === 'failed')
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                    Failed
                                </span>
                            @elseif($payment->status === 'refunded')
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    Refunded
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Link Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Related Invoice</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500 font-medium mb-1">Invoice #</p>
                        <p class="text-gray-900 font-semibold">#{{ $payment->invoice->invoice_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium mb-1">Company</p>
                        <p class="text-gray-900 font-semibold">{{ $payment->invoice->company->name ?? 'N/A' }}</p>
                    </div>
                    <a href="{{ route('admin.invoices.show', $payment->invoice) }}" class="block w-full text-center mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                        View Invoice
                    </a>
                </div>
            </div>

            <!-- Back Link -->
            <div>
                <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Payments
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Refund Modal -->
<div id="refundModal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Process Refund</h3>
            <button onclick="closeRefundModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form action="{{ route('admin.payments.refund', $payment) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <!-- Refund Amount -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Refund Amount</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-500">$</span>
                        <input type="number" name="refund_amount" step="0.01" min="0.01" max="{{ $payment->amount }}" required class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="0.00">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Maximum: ${{ number_format($payment->amount, 2) }}</p>
                </div>

                <!-- Refund Reason -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason (Optional)</label>
                    <textarea name="refund_reason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Enter reason for refund..."></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition duration-200 font-medium">
                        Process Refund
                    </button>
                    <button type="button" onclick="closeRefundModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200 font-medium">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openRefundModal() {
    document.getElementById('refundModal').classList.remove('hidden');
}

function closeRefundModal() {
    document.getElementById('refundModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('refundModal');
    if (event.target == modal) {
        modal.classList.add('hidden');
    }
}
</script>
@endsection
