@extends('layouts.admin')

@section('content')
<div class="py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Invoice #{{ $invoice->invoice_number }}</h1>
                <p class="text-gray-500 mt-2">Invoice details and payment information</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.invoices.edit', $invoice) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('admin.invoices.download', $invoice) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download
                </a>
                <button onclick="sendInvoice()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Send
                </button>
                @if($invoice->status !== 'paid')
                    <form action="{{ route('admin.invoices.mark-paid', $invoice) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Mark as Paid
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-6">
        @if($invoice->status === 'paid')
            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold bg-green-100 text-green-800">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Paid
            </span>
        @elseif($invoice->status === 'pending')
            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold bg-amber-100 text-amber-800">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                Pending
            </span>
        @elseif($invoice->status === 'overdue')
            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold bg-red-100 text-red-800">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                Overdue
            </span>
        @elseif($invoice->status === 'draft')
            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold bg-gray-100 text-gray-800">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2H6a1 1 0 110-2V4z"></path>
                </svg>
                Draft
            </span>
        @else
            <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold bg-red-100 text-red-800">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                Cancelled
            </span>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Invoice Header -->
            <div class="bg-white rounded-lg shadow p-8">
                <div class="grid grid-cols-2 gap-8 mb-8 pb-8 border-b border-gray-200">
                    <!-- Company Info -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Bill To:</h3>
                        <p class="text-gray-900 font-semibold">{{ $invoice->company->name ?? 'N/A' }}</p>
                        <p class="text-gray-600">{{ $invoice->company->email ?? '' }}</p>
                        <p class="text-gray-600">{{ $invoice->company->phone ?? '' }}</p>
                        <p class="text-gray-600">{{ $invoice->company->address ?? '' }}</p>
                    </div>

                    <!-- Invoice Info -->
                    <div class="text-right">
                        <p class="text-sm text-gray-500 mb-2">Invoice Number</p>
                        <p class="text-2xl font-bold text-gray-900 mb-4">#{{ $invoice->invoice_number }}</p>

                        <p class="text-sm text-gray-500 mb-1">Issue Date</p>
                        <p class="text-gray-900 font-medium mb-4">{{ $invoice->issue_date->format('M d, Y') }}</p>

                        <p class="text-sm text-gray-500 mb-1">Due Date</p>
                        <p class="text-gray-900 font-medium">{{ $invoice->due_date->format('M d, Y') }}</p>
                    </div>
                </div>

                <!-- Project Info -->
                <div class="mb-8">
                    <p class="text-sm text-gray-500 mb-1">Project</p>
                    <p class="text-gray-900 font-semibold">{{ $invoice->project->name ?? 'N/A' }}</p>
                    @if($invoice->description)
                        <p class="text-gray-600 mt-2">{{ $invoice->description }}</p>
                    @endif
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">Unit Price</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">Tax</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($invoice->items as $item)
                                <tr>
                                    <td class="px-6 py-4">
                                        <p class="text-gray-900 font-medium">{{ $item->description }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <p class="text-gray-600">{{ $item->quantity }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <p class="text-gray-600">${{ number_format($item->unit_price, 2) }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <p class="text-gray-600">{{ $item->tax_percentage }}%</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <p class="text-gray-900 font-semibold">${{ number_format($item->total_amount, 2) }}</p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        No items in this invoice
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <p class="text-gray-600">Subtotal:</p>
                        <p class="text-gray-900 font-semibold">${{ number_format($invoice->subtotal, 2) }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-600">Tax:</p>
                        <p class="text-gray-900 font-semibold">${{ number_format($invoice->tax_amount, 2) }}</p>
                    </div>
                    <div class="border-t border-gray-200 pt-4 flex justify-between">
                        <p class="text-lg font-bold text-gray-900">Total Amount:</p>
                        <p class="text-2xl font-bold text-blue-600">${{ number_format($invoice->total_amount, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            @if($invoice->notes)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Notes</h3>
                    <p class="text-gray-600 whitespace-pre-wrap">{{ $invoice->notes }}</p>
                </div>
            @endif

            <!-- Terms Section -->
            @if($invoice->terms)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Terms & Conditions</h3>
                    <p class="text-gray-600 whitespace-pre-wrap">{{ $invoice->terms }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Summary Card -->
            <div class="bg-blue-50 rounded-lg shadow p-6 border-l-4 border-blue-600">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Invoice Summary</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Subtotal</p>
                        <p class="text-2xl font-bold text-gray-900">${{ number_format($invoice->subtotal, 2) }}</p>
                    </div>
                    <div class="border-t border-blue-200 pt-3">
                        <p class="text-sm text-gray-600">Tax</p>
                        <p class="text-xl font-bold text-gray-900">${{ number_format($invoice->tax_amount, 2) }}</p>
                    </div>
                    <div class="border-t border-blue-200 pt-3">
                        <p class="text-sm text-gray-600">Total Due</p>
                        <p class="text-3xl font-bold text-blue-600">${{ number_format($invoice->total_amount, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            @if($invoice->payments && $invoice->payments->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Payment History</h3>
                    <div class="space-y-3">
                        @foreach($invoice->payments as $payment)
                            <div class="border-l-2 border-green-500 pl-3 py-2">
                                <p class="text-sm text-gray-600">{{ $payment->payment_date->format('M d, Y') }}</p>
                                <p class="text-gray-900 font-semibold">${{ number_format($payment->amount, 2) }}</p>
                                <p class="text-xs text-gray-500">{{ $payment->payment_method ?? 'Unknown' }}</p>
                                @if($payment->status === 'completed')
                                    <span class="inline-block mt-1 px-2 py-1 bg-green-100 text-green-800 text-xs rounded font-medium">
                                        Completed
                                    </span>
                                @else
                                    <span class="inline-block mt-1 px-2 py-1 bg-amber-100 text-amber-800 text-xs rounded font-medium">
                                        Pending
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Payment History</h3>
                    <p class="text-gray-500 text-center py-4">No payments recorded</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function sendInvoice() {
    const confirmed = confirm('Send this invoice to {{ $invoice->company->email ?? "the client" }}?');
    if (confirmed) {
        // Add your send invoice logic here
        alert('Invoice would be sent to: {{ $invoice->company->email ?? "the client" }}');
    }
}
</script>
@endsection
