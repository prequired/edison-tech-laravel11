<?php

declare(strict_types=1);

namespace App\DTOs;

/**
 * Data Transfer Object for processing a payment.
 *
 * @package App\DTOs
 */
readonly class ProcessPaymentDTO
{
    /**
     * Create a new ProcessPaymentDTO instance.
     *
     * @param int $invoice_id The invoice ID
     * @param float $amount The payment amount
     * @param string $payment_method The payment method (e.g., 'stripe', 'bank_transfer', 'check')
     * @param string|null $stripe_payment_intent_id The Stripe payment intent ID
     * @param string|null $transaction_id The transaction ID
     * @param string|null $stripe_charge_id The Stripe charge ID
     * @param string|null $payment_date The payment date (Y-m-d format)
     * @param string $status The payment status (default: 'completed')
     * @param string|null $notes Additional notes
     * @param array|null $metadata Additional metadata
     */
    public function __construct(
        public int $invoice_id,
        public float $amount,
        public string $payment_method,
        public ?string $stripe_payment_intent_id = null,
        public ?string $transaction_id = null,
        public ?string $stripe_charge_id = null,
        public ?string $payment_date = null,
        public string $status = 'completed',
        public ?string $notes = null,
        public ?array $metadata = null,
    ) {}

    /**
     * Create a new instance from an array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function from(array $data): self
    {
        return new self(
            invoice_id: (int) $data['invoice_id'],
            amount: (float) $data['amount'],
            payment_method: (string) $data['payment_method'],
            stripe_payment_intent_id: $data['stripe_payment_intent_id'] ?? null,
            transaction_id: $data['transaction_id'] ?? null,
            stripe_charge_id: $data['stripe_charge_id'] ?? null,
            payment_date: $data['payment_date'] ?? null,
            status: $data['status'] ?? 'completed',
            notes: $data['notes'] ?? null,
            metadata: $data['metadata'] ?? null,
        );
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'invoice_id' => $this->invoice_id,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'stripe_payment_intent_id' => $this->stripe_payment_intent_id,
            'transaction_id' => $this->transaction_id,
            'stripe_charge_id' => $this->stripe_charge_id,
            'payment_date' => $this->payment_date,
            'status' => $this->status,
            'notes' => $this->notes,
            'metadata' => $this->metadata,
        ];
    }
}
