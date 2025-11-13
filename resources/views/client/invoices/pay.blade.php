@extends('layouts.app')

@section('title', 'Pay Invoice')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('client.invoices.show', $invoice->id) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-700">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>
            <h1>Pay Invoice #{{ $invoice->number }}</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Payment Form -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('client.invoices.payment.store', $invoice->id) }}" class="space-y-6">
                @csrf

                <!-- Invoice Summary Card -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="px-4 py-6 sm:px-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Invoice Details</h3>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-500 mb-1">Invoice Number</p>
                                <p class="text-lg font-bold text-gray-900">#{{ $invoice->number }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-500 mb-1">Due Date</p>
                                <p class="text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</p>
                            </div>
                            <div class="bg-indigo-50 rounded-lg p-4 border-2 border-indigo-200">
                                <p class="text-sm text-indigo-600 mb-1">Amount Due</p>
                                <p class="text-2xl font-bold text-indigo-600">${{ number_format($invoice->total, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="px-4 py-6 sm:px-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Method</h3>
                        <div class="space-y-4">
                            <label class="relative flex cursor-pointer rounded-lg border border-gray-300 p-4 hover:bg-gray-50 focus-within:ring-2 focus-within:ring-indigo-500">
                                <input type="radio" name="payment_method" value="credit_card" class="h-4 w-4 text-indigo-600 mt-0.5" checked>
                                <div class="ml-4 flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900">Credit Card</span>
                                    <span class="block text-sm text-gray-500">Visa, Mastercard, American Express</span>
                                </div>
                            </label>

                            <label class="relative flex cursor-pointer rounded-lg border border-gray-300 p-4 hover:bg-gray-50 focus-within:ring-2 focus-within:ring-indigo-500">
                                <input type="radio" name="payment_method" value="bank_transfer" class="h-4 w-4 text-indigo-600 mt-0.5">
                                <div class="ml-4 flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900">Bank Transfer</span>
                                    <span class="block text-sm text-gray-500">Direct bank account transfer</span>
                                </div>
                            </label>

                            <label class="relative flex cursor-pointer rounded-lg border border-gray-300 p-4 hover:bg-gray-50 focus-within:ring-2 focus-within:ring-indigo-500">
                                <input type="radio" name="payment_method" value="paypal" class="h-4 w-4 text-indigo-600 mt-0.5">
                                <div class="ml-4 flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900">PayPal</span>
                                    <span class="block text-sm text-gray-500">Pay securely with your PayPal account</span>
                                </div>
                            </label>

                            <label class="relative flex cursor-pointer rounded-lg border border-gray-300 p-4 hover:bg-gray-50 focus-within:ring-2 focus-within:ring-indigo-500">
                                <input type="radio" name="payment_method" value="stripe" class="h-4 w-4 text-indigo-600 mt-0.5">
                                <div class="ml-4 flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900">Stripe</span>
                                    <span class="block text-sm text-gray-500">Pay securely with Stripe</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Payment Details Form -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="px-4 py-6 sm:px-6 space-y-6">
                        <div id="credit-card-form" class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900">Card Details</h3>

                            <!-- Card Holder Name -->
                            <div>
                                <label for="card_holder_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Cardholder Name
                                </label>
                                <input
                                    type="text"
                                    name="card_holder_name"
                                    id="card_holder_name"
                                    placeholder="John Doe"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border"
                                    value="{{ old('card_holder_name') }}"
                                >
                                @error('card_holder_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Card Number -->
                            <div>
                                <label for="card_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Card Number
                                </label>
                                <input
                                    type="text"
                                    name="card_number"
                                    id="card_number"
                                    placeholder="4111 1111 1111 1111"
                                    maxlength="19"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border"
                                    value="{{ old('card_number') }}"
                                >
                                @error('card_number')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Expiry and CVC -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="card_expiry" class="block text-sm font-medium text-gray-700 mb-2">
                                        Expiry Date
                                    </label>
                                    <input
                                        type="text"
                                        name="card_expiry"
                                        id="card_expiry"
                                        placeholder="MM/YY"
                                        maxlength="5"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border"
                                        value="{{ old('card_expiry') }}"
                                    >
                                    @error('card_expiry')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="card_cvc" class="block text-sm font-medium text-gray-700 mb-2">
                                        CVC
                                    </label>
                                    <input
                                        type="text"
                                        name="card_cvc"
                                        id="card_cvc"
                                        placeholder="123"
                                        maxlength="4"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border"
                                        value="{{ old('card_cvc') }}"
                                    >
                                    @error('card_cvc')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Bank Transfer Form (Hidden by default) -->
                        <div id="bank-transfer-form" class="hidden space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900">Bank Transfer Details</h3>
                            <div class="rounded-lg bg-blue-50 p-4 border border-blue-200">
                                <h4 class="font-semibold text-blue-900 mb-2">Transfer Information</h4>
                                <div class="space-y-2 text-sm text-blue-800">
                                    <p><strong>Bank Name:</strong> First National Bank</p>
                                    <p><strong>Account Name:</strong> Edison Tech Inc.</p>
                                    <p><strong>Account Number:</strong> 123456789</p>
                                    <p><strong>Routing Number:</strong> 987654321</p>
                                    <p><strong>Reference:</strong> Invoice #{{ $invoice->number }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Amount Field -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="px-4 py-6 sm:px-6">
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Amount to Pay
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                            <input
                                type="number"
                                name="amount"
                                id="amount"
                                step="0.01"
                                min="0"
                                max="{{ $invoice->total }}"
                                value="{{ old('amount', $invoice->total) }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pl-8 pr-3 py-2 border"
                            >
                        </div>
                        @error('amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">
                            Total amount due: ${{ number_format($invoice->total, 2) }}
                        </p>
                    </div>
                </div>

                <!-- Notes -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="px-4 py-6 sm:px-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Payment Notes (Optional)
                        </label>
                        <textarea
                            name="notes"
                            id="notes"
                            rows="4"
                            placeholder="Add any notes for this payment..."
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border"
                        >{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Security Notice -->
                <div class="rounded-lg bg-blue-50 p-4 border border-blue-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zM8 9a1 1 0 100-2 1 1 0 000 2zm5-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-800">
                                Your payment information is secure and encrypted
                            </p>
                            <p class="mt-1 text-sm text-blue-700">
                                We use industry-standard SSL encryption to protect your payment data. Your card information is never stored on our servers.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full rounded-md bg-indigo-600 px-4 py-3 text-base font-semibold text-white shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Submit Payment
                </button>
            </form>
        </div>

        <!-- Sidebar - Order Summary -->
        <div class="lg:col-span-1">
            <div class="sticky top-6 overflow-hidden rounded-lg bg-white shadow">
                <div class="px-4 py-6 sm:px-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Summary</h3>

                    <div class="space-y-4 border-b border-gray-200 pb-4 mb-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Invoice Number</span>
                            <span class="font-semibold text-gray-900">#{{ $invoice->number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Invoice Date</span>
                            <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($invoice->issued_date)->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Due Date</span>
                            <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="text-gray-900">${{ number_format($invoice->subtotal ?? $invoice->total, 2) }}</span>
                        </div>
                        @if(($invoice->tax ?? 0) > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax</span>
                                <span class="text-gray-900">${{ number_format($invoice->tax, 2) }}</span>
                            </div>
                        @endif
                        @if(($invoice->discount ?? 0) > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Discount</span>
                                <span class="text-gray-900">-${{ number_format($invoice->discount, 2) }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-900">Total Amount</span>
                            <span class="text-3xl font-bold text-indigo-600">${{ number_format($invoice->total, 2) }}</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('client.invoices.show', $invoice->id) }}" class="block text-center text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                            View Full Invoice
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Payment Method Toggle -->
    <script>
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('credit-card-form').classList.toggle('hidden', this.value !== 'credit_card');
                document.getElementById('bank-transfer-form').classList.toggle('hidden', this.value !== 'bank_transfer');
            });
        });

        // Format card number input
        document.getElementById('card_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.replace(/(\d{4})/g, '$1 ').trim();
            e.target.value = formattedValue;
        });

        // Format expiry date input
        document.getElementById('card_expiry').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
    </script>
@endsection
