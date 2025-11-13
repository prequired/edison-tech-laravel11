<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form request for invoice creation validation.
 *
 * @package App\Http\Requests
 */
class StoreInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Authorization is handled by InvoicePolicy
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'invoice_number' => ['required', 'string', 'max:50', 'unique:invoices,invoice_number'],
            'status' => ['required', 'string', Rule::in(['draft', 'sent', 'partial', 'paid', 'overdue', 'cancelled'])],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'subtotal' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tax_amount' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'discount_amount' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'total' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'balance' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'notes' => ['nullable', 'string'],
            'payment_terms' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'items.*.amount' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'company_id.required' => 'Please select a company.',
            'company_id.exists' => 'The selected company does not exist.',
            'project_id.exists' => 'The selected project does not exist.',
            'invoice_number.required' => 'Please enter an invoice number.',
            'invoice_number.unique' => 'This invoice number is already in use.',
            'invoice_number.max' => 'Invoice number must not exceed 50 characters.',
            'status.required' => 'Please select an invoice status.',
            'status.in' => 'Invalid invoice status selected.',
            'issue_date.required' => 'Please select an issue date.',
            'issue_date.date' => 'Please enter a valid issue date.',
            'due_date.required' => 'Please select a due date.',
            'due_date.date' => 'Please enter a valid due date.',
            'due_date.after_or_equal' => 'Due date must be equal to or after the issue date.',
            'subtotal.required' => 'Subtotal is required.',
            'subtotal.numeric' => 'Subtotal must be a valid number.',
            'subtotal.min' => 'Subtotal must be at least 0.',
            'subtotal.max' => 'Subtotal must not exceed 999,999,999.99.',
            'tax_rate.numeric' => 'Tax rate must be a valid number.',
            'tax_rate.min' => 'Tax rate must be at least 0.',
            'tax_rate.max' => 'Tax rate must not exceed 100.',
            'tax_amount.numeric' => 'Tax amount must be a valid number.',
            'tax_amount.min' => 'Tax amount must be at least 0.',
            'tax_amount.max' => 'Tax amount must not exceed 999,999,999.99.',
            'discount_amount.numeric' => 'Discount amount must be a valid number.',
            'discount_amount.min' => 'Discount amount must be at least 0.',
            'discount_amount.max' => 'Discount amount must not exceed 999,999,999.99.',
            'total.required' => 'Total is required.',
            'total.numeric' => 'Total must be a valid number.',
            'total.min' => 'Total must be at least 0.',
            'total.max' => 'Total must not exceed 999,999,999.99.',
            'balance.required' => 'Balance is required.',
            'balance.numeric' => 'Balance must be a valid number.',
            'balance.min' => 'Balance must be at least 0.',
            'balance.max' => 'Balance must not exceed 999,999,999.99.',
            'payment_terms.max' => 'Payment terms must not exceed 500 characters.',
            'items.required' => 'Please add at least one invoice item.',
            'items.array' => 'Items must be an array.',
            'items.min' => 'Invoice must have at least one item.',
            'items.*.description.required' => 'Item description is required.',
            'items.*.description.max' => 'Item description must not exceed 500 characters.',
            'items.*.quantity.required' => 'Item quantity is required.',
            'items.*.quantity.min' => 'Item quantity must be at least 0.01.',
            'items.*.unit_price.required' => 'Item unit price is required.',
            'items.*.unit_price.min' => 'Item unit price must be at least 0.',
            'items.*.unit_price.max' => 'Item unit price must not exceed 999,999,999.99.',
            'items.*.amount.required' => 'Item amount is required.',
            'items.*.amount.min' => 'Item amount must be at least 0.',
            'items.*.amount.max' => 'Item amount must not exceed 999,999,999.99.',
        ];
    }
}
