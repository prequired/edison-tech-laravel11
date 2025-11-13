<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form request for payment processing validation.
 *
 * @package App\Http\Requests
 */
class StorePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Authorization is handled by PaymentPolicy
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
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
            'payment_method' => ['required', 'string', Rule::in(['credit_card', 'debit_card', 'bank_transfer', 'paypal', 'stripe', 'check', 'cash', 'other'])],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'payment_date' => ['required', 'date'],
            'status' => ['required', 'string', Rule::in(['pending', 'completed', 'failed', 'refunded'])],
            'notes' => ['nullable', 'string'],
            'stripe_payment_intent_id' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
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
            'invoice_id.required' => 'Please select an invoice.',
            'invoice_id.exists' => 'The selected invoice does not exist.',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Invalid payment method selected.',
            'transaction_id.max' => 'Transaction ID must not exceed 255 characters.',
            'amount.required' => 'Please enter a payment amount.',
            'amount.numeric' => 'Payment amount must be a valid number.',
            'amount.min' => 'Payment amount must be at least 0.01.',
            'amount.max' => 'Payment amount must not exceed 999,999,999.99.',
            'payment_date.required' => 'Please select a payment date.',
            'payment_date.date' => 'Please enter a valid payment date.',
            'status.required' => 'Please select a payment status.',
            'status.in' => 'Invalid payment status selected.',
            'stripe_payment_intent_id.max' => 'Stripe payment intent ID must not exceed 255 characters.',
            'metadata.array' => 'Metadata must be an array.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Check if payment amount doesn't exceed invoice balance
            if ($this->has('invoice_id') && $this->has('amount')) {
                $invoice = \App\Models\Invoice::find($this->input('invoice_id'));

                if ($invoice && $this->input('amount') > $invoice->balance) {
                    $validator->errors()->add(
                        'amount',
                        'Payment amount cannot exceed the invoice balance of ' . number_format($invoice->balance, 2) . '.'
                    );
                }
            }
        });
    }
}
