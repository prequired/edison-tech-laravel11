{{--
    Reusable Invoice Item Row Component
    Usage: @include('admin.invoices._item-row', ['itemIndex' => $index, 'item' => $item])
--}}

<tr id="item-{{ $itemIndex }}">
    <!-- Description -->
    <td class="px-4 py-3">
        <input type="text"
               name="items[{{ $itemIndex }}][description]"
               placeholder="Item description"
               value="{{ $item->description ?? old('items.' . $itemIndex . '.description', '') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
               required>
    </td>

    <!-- Quantity -->
    <td class="px-4 py-3">
        <input type="number"
               name="items[{{ $itemIndex }}][quantity]"
               placeholder="1"
               value="{{ $item->quantity ?? old('items.' . $itemIndex . '.quantity', 1) }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-right"
               step="0.01"
               min="0"
               required
               onchange="calculateItemTotal({{ $itemIndex }})"
               onkeyup="calculateItemTotal({{ $itemIndex }})">
    </td>

    <!-- Unit Price -->
    <td class="px-4 py-3">
        <input type="number"
               name="items[{{ $itemIndex }}][unit_price]"
               placeholder="0.00"
               value="{{ $item->unit_price ?? old('items.' . $itemIndex . '.unit_price', 0) }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-right"
               step="0.01"
               min="0"
               required
               onchange="calculateItemTotal({{ $itemIndex }})"
               onkeyup="calculateItemTotal({{ $itemIndex }})">
    </td>

    <!-- Tax Percentage -->
    <td class="px-4 py-3">
        <input type="number"
               name="items[{{ $itemIndex }}][tax_percentage]"
               placeholder="0"
               value="{{ $item->tax_percentage ?? old('items.' . $itemIndex . '.tax_percentage', 0) }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-right"
               step="0.01"
               min="0"
               max="100">
    </td>

    <!-- Total (Calculated) -->
    <td class="px-4 py-3 text-right">
        <p class="text-gray-900 font-semibold text-sm" id="item-total-{{ $itemIndex }}">
            ${{ isset($item) ? number_format($item->total_amount, 2) : '0.00' }}
        </p>
    </td>

    <!-- Remove Button -->
    <td class="px-4 py-3 text-center">
        <button type="button"
                onclick="removeItem({{ $itemIndex }})"
                class="text-red-600 hover:text-red-900 font-medium transition duration-150"
                title="Remove item">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
        </button>
    </td>
</tr>
