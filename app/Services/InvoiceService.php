<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateInvoiceDTO;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Service class for handling invoice-related business logic.
 *
 * @package App\Services
 */
class InvoiceService
{
    /**
     * Create a new InvoiceService instance.
     */
    public function __construct(
        private readonly Invoice $invoiceModel,
    ) {}

    /**
     * Create a new invoice with items.
     *
     * @param CreateInvoiceDTO $dto
     * @return Invoice
     * @throws \Throwable
     */
    public function create(CreateInvoiceDTO $dto): Invoice
    {
        return DB::transaction(function () use ($dto) {
            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber($dto->company_id);

            // Create invoice
            $invoice = Invoice::create([
                'company_id' => $dto->company_id,
                'project_id' => $dto->project_id,
                'invoice_number' => $invoiceNumber,
                'status' => $dto->status,
                'issue_date' => $dto->issue_date,
                'due_date' => $dto->due_date,
                'tax_rate' => $dto->tax_rate,
                'discount_amount' => $dto->discount_amount,
                'currency' => $dto->currency,
                'notes' => $dto->notes,
                'terms' => $dto->terms,
                'subtotal' => 0,
                'tax_amount' => 0,
                'total' => 0,
                'paid_amount' => 0,
                'balance' => 0,
            ]);

            // Add items
            foreach ($dto->items as $itemData) {
                $this->addItem($invoice, $itemData);
            }

            // Calculate totals
            $this->calculate($invoice);

            return $invoice->fresh(['items', 'company', 'project']);
        });
    }

    /**
     * Add an item to an invoice.
     *
     * @param Invoice $invoice
     * @param array<string, mixed> $itemData
     * @return InvoiceItem
     * @throws \Throwable
     */
    public function addItem(Invoice $invoice, array $itemData): InvoiceItem
    {
        return DB::transaction(function () use ($invoice, $itemData) {
            $quantity = (float) $itemData['quantity'];
            $unitPrice = (float) $itemData['unit_price'];
            $total = $quantity * $unitPrice;

            $item = InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $itemData['description'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total' => $total,
                'tax_rate' => $itemData['tax_rate'] ?? 0,
            ]);

            // Recalculate invoice totals
            $this->calculate($invoice);

            return $item;
        });
    }

    /**
     * Calculate invoice totals.
     *
     * @param Invoice $invoice
     * @return Invoice
     * @throws \Throwable
     */
    public function calculate(Invoice $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice) {
            $subtotal = $invoice->items()->sum('total');
            $taxAmount = ($subtotal - $invoice->discount_amount) * ($invoice->tax_rate / 100);
            $total = $subtotal - $invoice->discount_amount + $taxAmount;
            $balance = $total - $invoice->paid_amount;

            $invoice->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'balance' => $balance,
            ]);

            return $invoice->fresh();
        });
    }

    /**
     * Mark an invoice as paid.
     *
     * @param Invoice $invoice
     * @param float|null $amount
     * @return Invoice
     * @throws \Throwable
     */
    public function markAsPaid(Invoice $invoice, ?float $amount = null): Invoice
    {
        return DB::transaction(function () use ($invoice, $amount) {
            $paidAmount = $amount ?? $invoice->total;

            $invoice->update([
                'status' => 'paid',
                'paid_amount' => $paidAmount,
                'balance' => $invoice->total - $paidAmount,
                'paid_at' => now(),
            ]);

            // If invoice is associated with time entries, mark them as invoiced
            if ($invoice->project_id) {
                $invoice->project->timeEntries()
                    ->where('is_billable', true)
                    ->where('is_invoiced', false)
                    ->update([
                        'is_invoiced' => true,
                        'invoice_id' => $invoice->id,
                    ]);
            }

            return $invoice->fresh();
        });
    }

    /**
     * Send an invoice to the client.
     *
     * @param Invoice $invoice
     * @return Invoice
     * @throws \Throwable
     */
    public function send(Invoice $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice) {
            $invoice->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            // TODO: Implement email sending logic here
            // Mail::to($invoice->company->email)->send(new InvoiceMail($invoice));

            return $invoice->fresh();
        });
    }

    /**
     * Generate a unique invoice number.
     *
     * @param int $companyId
     * @return string
     */
    private function generateInvoiceNumber(int $companyId): string
    {
        $year = Carbon::now()->year;
        $month = Carbon::now()->format('m');

        $lastInvoice = Invoice::where('company_id', $companyId)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastInvoice ? ((int) substr($lastInvoice->invoice_number, -4)) + 1 : 1;

        return sprintf('INV-%s-%s-%04d', $year, $month, $sequence);
    }

    /**
     * Get overdue invoices for a company.
     *
     * @param int $companyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOverdueInvoices(int $companyId): \Illuminate\Database\Eloquent\Collection
    {
        return Invoice::where('company_id', $companyId)
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->with(['company', 'project'])
            ->orderBy('due_date', 'asc')
            ->get();
    }
}
