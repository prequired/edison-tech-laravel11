<?php

declare(strict_types=1);

namespace App\DTOs;

/**
 * Data Transfer Object for creating a new invoice.
 *
 * @package App\DTOs
 */
readonly class CreateInvoiceDTO
{
    /**
     * Create a new CreateInvoiceDTO instance.
     *
     * @param int $company_id The company ID
     * @param int|null $project_id The project ID (optional)
     * @param string $issue_date The invoice issue date (Y-m-d format)
     * @param string $due_date The invoice due date (Y-m-d format)
     * @param array<int, array<string, mixed>> $items Invoice items array
     * @param float $tax_rate The tax rate (default: 0.0)
     * @param float $discount_amount The discount amount (default: 0.0)
     * @param string $currency The currency code (default: 'USD')
     * @param string|null $notes Additional notes
     * @param string|null $terms Payment terms
     * @param string $status The invoice status (default: 'draft')
     */
    public function __construct(
        public int $company_id,
        public ?int $project_id,
        public string $issue_date,
        public string $due_date,
        public array $items,
        public float $tax_rate = 0.0,
        public float $discount_amount = 0.0,
        public string $currency = 'USD',
        public ?string $notes = null,
        public ?string $terms = null,
        public string $status = 'draft',
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
            company_id: (int) $data['company_id'],
            project_id: isset($data['project_id']) ? (int) $data['project_id'] : null,
            issue_date: (string) $data['issue_date'],
            due_date: (string) $data['due_date'],
            items: (array) $data['items'],
            tax_rate: isset($data['tax_rate']) ? (float) $data['tax_rate'] : 0.0,
            discount_amount: isset($data['discount_amount']) ? (float) $data['discount_amount'] : 0.0,
            currency: $data['currency'] ?? 'USD',
            notes: $data['notes'] ?? null,
            terms: $data['terms'] ?? null,
            status: $data['status'] ?? 'draft',
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
            'company_id' => $this->company_id,
            'project_id' => $this->project_id,
            'issue_date' => $this->issue_date,
            'due_date' => $this->due_date,
            'items' => $this->items,
            'tax_rate' => $this->tax_rate,
            'discount_amount' => $this->discount_amount,
            'currency' => $this->currency,
            'notes' => $this->notes,
            'terms' => $this->terms,
            'status' => $this->status,
        ];
    }
}
