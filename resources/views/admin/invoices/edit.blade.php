@extends('layouts.admin')

@section('content')
<div class="py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Invoice #{{ $invoice->invoice_number }}</h1>
        <p class="text-gray-500 mt-2">Update invoice details and items</p>
    </div>

    <form action="{{ route('admin.invoices.update', $invoice) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Invoice Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Company -->
                <div>
                    <label for="company_id" class="block text-sm font-medium text-gray-700 mb-2">Company <span class="text-red-600">*</span></label>
                    <select id="company_id" name="company_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('company_id') border-red-500 @enderror">
                        <option value="">Select a company</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ (old('company_id') ?? $invoice->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                        @endforeach
                    </select>
                    @error('company_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Project -->
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Project <span class="text-red-600">*</span></label>
                    <select id="project_id" name="project_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('project_id') border-red-500 @enderror">
                        <option value="">Select a project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ (old('project_id') ?? $invoice->project_id) == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Invoice Date -->
                <div>
                    <label for="issue_date" class="block text-sm font-medium text-gray-700 mb-2">Invoice Date <span class="text-red-600">*</span></label>
                    <input type="date" id="issue_date" name="issue_date" value="{{ old('issue_date', $invoice->issue_date->format('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('issue_date') border-red-500 @enderror">
                    @error('issue_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Due Date -->
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Due Date <span class="text-red-600">*</span></label>
                    <input type="date" id="due_date" name="due_date" value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('due_date') border-red-500 @enderror">
                    @error('due_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-600">*</span></label>
                    <select id="status" name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                        <option value="draft" {{ (old('status') ?? $invoice->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending" {{ (old('status') ?? $invoice->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ (old('status') ?? $invoice->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="overdue" {{ (old('status') ?? $invoice->status) == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="cancelled" {{ (old('status') ?? $invoice->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $invoice->description) }}</textarea>
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Items Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-900">Invoice Items</h2>
                <button type="button" onclick="addItem()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Item
                </button>
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Description</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Quantity</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Unit Price</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Tax %</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Total</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody id="items-container" class="divide-y divide-gray-200">
                        <!-- Items will be added here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary Section -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Subtotal</p>
                    <p class="text-3xl font-bold text-gray-900" id="subtotal">$0.00</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm mb-1">Tax</p>
                    <p class="text-3xl font-bold text-gray-900" id="tax-total">$0.00</p>
                </div>
                <div class="bg-blue-600 rounded-lg p-4 text-white">
                    <p class="text-blue-100 text-sm mb-1">Total Amount</p>
                    <p class="text-4xl font-bold" id="total-amount">$0.00</p>
                </div>
            </div>
        </div>

        <!-- Notes and Terms -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Notes -->
            <div class="bg-white rounded-lg shadow p-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea id="notes" name="notes" rows="4" placeholder="Additional notes for the client..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror">{{ old('notes', $invoice->notes) }}</textarea>
                @error('notes')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Terms -->
            <div class="bg-white rounded-lg shadow p-6">
                <label for="terms" class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
                <textarea id="terms" name="terms" rows="4" placeholder="Payment terms and conditions..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('terms') border-red-500 @enderror">{{ old('terms', $invoice->terms) }}</textarea>
                @error('terms')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex gap-4 justify-between">
            <div>
                <button type="button" onclick="deleteInvoice()" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200 font-medium">
                    Delete Invoice
                </button>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('admin.invoices.show', $invoice) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200 font-medium">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                    Update Invoice
                </button>
            </div>
        </div>
    </form>

    <!-- Delete Form (Hidden) -->
    <form id="delete-form" action="{{ route('admin.invoices.destroy', $invoice) }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
</div>

<script>
let itemCount = 0;

function addItem() {
    const container = document.getElementById('items-container');
    const itemIndex = itemCount;
    itemCount++;

    const itemRow = document.createElement('tr');
    itemRow.id = 'item-' + itemIndex;
    itemRow.innerHTML = `
        <td class="px-4 py-3">
            <input type="text" name="items[${itemIndex}][description]" placeholder="Item description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
        </td>
        <td class="px-4 py-3 text-right">
            <input type="number" name="items[${itemIndex}][quantity]" placeholder="1" value="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right" step="0.01" min="0" required onchange="calculateItemTotal(${itemIndex})" onkeyup="calculateItemTotal(${itemIndex})">
        </td>
        <td class="px-4 py-3 text-right">
            <input type="number" name="items[${itemIndex}][unit_price]" placeholder="0.00" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right" step="0.01" min="0" required onchange="calculateItemTotal(${itemIndex})" onkeyup="calculateItemTotal(${itemIndex})">
        </td>
        <td class="px-4 py-3 text-right">
            <input type="number" name="items[${itemIndex}][tax_percentage]" placeholder="0" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right" step="0.01" min="0" max="100" onchange="calculateItemTotal(${itemIndex})" onkeyup="calculateItemTotal(${itemIndex})">
        </td>
        <td class="px-4 py-3 text-right">
            <p class="text-gray-900 font-semibold" id="item-total-${itemIndex}">$0.00</p>
        </td>
        <td class="px-4 py-3 text-center">
            <button type="button" onclick="removeItem(${itemIndex})" class="text-red-600 hover:text-red-900 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </td>
    `;
    container.appendChild(itemRow);
    calculateTotals();
}

function removeItem(index) {
    const item = document.getElementById('item-' + index);
    if (item) {
        item.remove();
        calculateTotals();
    }
}

function calculateItemTotal(index) {
    const quantity = parseFloat(document.querySelector(`input[name="items[${index}][quantity]"]`).value) || 0;
    const unitPrice = parseFloat(document.querySelector(`input[name="items[${index}][unit_price]"]`).value) || 0;
    const taxPercentage = parseFloat(document.querySelector(`input[name="items[${index}][tax_percentage]"]`).value) || 0;

    const subtotal = quantity * unitPrice;
    const tax = subtotal * (taxPercentage / 100);
    const total = subtotal + tax;

    document.getElementById('item-total-' + index).textContent = '$' + total.toFixed(2);
    calculateTotals();
}

function calculateTotals() {
    let grandSubtotal = 0;
    let grandTax = 0;

    const container = document.getElementById('items-container');
    const rows = container.querySelectorAll('tr');

    rows.forEach((row, idx) => {
        const quantityInput = row.querySelector('input[name*="[quantity]"]');
        const priceInput = row.querySelector('input[name*="[unit_price]"]');
        const taxInput = row.querySelector('input[name*="[tax_percentage]"]');

        if (quantityInput && priceInput && taxInput) {
            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const taxPercent = parseFloat(taxInput.value) || 0;

            const subtotal = quantity * price;
            const tax = subtotal * (taxPercent / 100);

            grandSubtotal += subtotal;
            grandTax += tax;
        }
    });

    const grandTotal = grandSubtotal + grandTax;

    document.getElementById('subtotal').textContent = '$' + grandSubtotal.toFixed(2);
    document.getElementById('tax-total').textContent = '$' + grandTax.toFixed(2);
    document.getElementById('total-amount').textContent = '$' + grandTotal.toFixed(2);
}

function deleteInvoice() {
    if (confirm('Are you sure you want to delete this invoice? This action cannot be undone.')) {
        document.getElementById('delete-form').submit();
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load existing items
    @if($invoice->items && $invoice->items->count() > 0)
        @foreach($invoice->items as $index => $item)
            addItem();
            const itemIndex = {{ $index }};
            setTimeout(() => {
                document.querySelector(`input[name="items[${itemIndex}][description]"]`).value = '{{ $item->description }}';
                document.querySelector(`input[name="items[${itemIndex}][quantity]"]`).value = '{{ $item->quantity }}';
                document.querySelector(`input[name="items[${itemIndex}][unit_price]"]`).value = '{{ $item->unit_price }}';
                document.querySelector(`input[name="items[${itemIndex}][tax_percentage]"]`).value = '{{ $item->tax_percentage }}';
                calculateItemTotal(itemIndex);
            }, 0);
        @endforeach
    @endif

    // Load old items if form was submitted with validation errors
    @if(old('items'))
        @foreach(old('items') as $index => $item)
            addItem();
            const itemIndex = {{ $index }};
            setTimeout(() => {
                document.querySelector(`input[name="items[${itemIndex}][description]"]`).value = '{{ $item["description"] ?? "" }}';
                document.querySelector(`input[name="items[${itemIndex}][quantity]"]`).value = '{{ $item["quantity"] ?? 1 }}';
                document.querySelector(`input[name="items[${itemIndex}][unit_price]"]`).value = '{{ $item["unit_price"] ?? 0 }}';
                document.querySelector(`input[name="items[${itemIndex}][tax_percentage]"]`).value = '{{ $item["tax_percentage"] ?? 0 }}';
                calculateItemTotal(itemIndex);
            }, 0);
        @endforeach
    @endif

    calculateTotals();
});
</script>
@endsection
