<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ProcessPaymentDTO;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\Stripe;

/**
 * Service class for handling payment-related business logic.
 *
 * @package App\Services
 */
class PaymentService
{
    /**
     * Create a new PaymentService instance.
     */
    public function __construct(
        private readonly Payment $paymentModel,
        private readonly InvoiceService $invoiceService,
    ) {
        // Set Stripe API key
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Process a Stripe payment.
     *
     * @param ProcessPaymentDTO $dto
     * @return Payment
     * @throws ApiErrorException
     * @throws \Throwable
     */
    public function processStripePayment(ProcessPaymentDTO $dto): Payment
    {
        return DB::transaction(function () use ($dto) {
            $invoice = Invoice::findOrFail($dto->invoice_id);

            // Retrieve the payment intent from Stripe
            $paymentIntent = PaymentIntent::retrieve($dto->stripe_payment_intent_id);

            if ($paymentIntent->status !== 'succeeded') {
                throw new \RuntimeException('Payment intent not succeeded');
            }

            // Record the payment
            $payment = $this->recordPayment($dto);

            // Update invoice status
            if ($payment->amount >= $invoice->balance) {
                $this->invoiceService->markAsPaid($invoice, $payment->amount);
            } else {
                // Partial payment
                $invoice->update([
                    'status' => 'partial',
                    'paid_amount' => $invoice->paid_amount + $payment->amount,
                    'balance' => $invoice->balance - $payment->amount,
                ]);
            }

            return $payment->fresh(['invoice', 'company']);
        });
    }

    /**
     * Record a payment in the database.
     *
     * @param ProcessPaymentDTO $dto
     * @return Payment
     * @throws \Throwable
     */
    public function recordPayment(ProcessPaymentDTO $dto): Payment
    {
        return DB::transaction(function () use ($dto) {
            $invoice = Invoice::findOrFail($dto->invoice_id);

            // Generate payment number
            $paymentNumber = $this->generatePaymentNumber($invoice->company_id);

            $payment = Payment::create([
                'invoice_id' => $dto->invoice_id,
                'company_id' => $invoice->company_id,
                'payment_number' => $paymentNumber,
                'amount' => $dto->amount,
                'payment_method' => $dto->payment_method,
                'status' => $dto->status,
                'payment_date' => $dto->payment_date ?? now()->format('Y-m-d'),
                'transaction_id' => $dto->transaction_id,
                'stripe_payment_intent_id' => $dto->stripe_payment_intent_id,
                'stripe_charge_id' => $dto->stripe_charge_id,
                'notes' => $dto->notes,
                'metadata' => $dto->metadata,
            ]);

            return $payment;
        });
    }

    /**
     * Refund a payment.
     *
     * @param Payment $payment
     * @param float|null $amount
     * @param string|null $reason
     * @return Payment
     * @throws ApiErrorException
     * @throws \Throwable
     */
    public function refund(Payment $payment, ?float $amount = null, ?string $reason = null): Payment
    {
        return DB::transaction(function () use ($payment, $amount, $reason) {
            $refundAmount = $amount ?? $payment->amount;

            // If payment was made via Stripe, process refund through Stripe
            if ($payment->stripe_payment_intent_id) {
                $refund = Refund::create([
                    'payment_intent' => $payment->stripe_payment_intent_id,
                    'amount' => (int) ($refundAmount * 100), // Convert to cents
                    'reason' => $reason ?? 'requested_by_customer',
                ]);

                // Update payment status
                $payment->update([
                    'status' => 'refunded',
                    'metadata' => array_merge($payment->metadata ?? [], [
                        'refund_id' => $refund->id,
                        'refund_amount' => $refundAmount,
                        'refund_date' => now()->toDateTimeString(),
                        'refund_reason' => $reason,
                    ]),
                ]);
            } else {
                // For non-Stripe payments, just update the status
                $payment->update([
                    'status' => 'refunded',
                    'metadata' => array_merge($payment->metadata ?? [], [
                        'refund_amount' => $refundAmount,
                        'refund_date' => now()->toDateTimeString(),
                        'refund_reason' => $reason,
                    ]),
                ]);
            }

            // Update invoice
            $invoice = $payment->invoice;
            $invoice->update([
                'status' => $invoice->paid_amount - $refundAmount <= 0 ? 'draft' : 'partial',
                'paid_amount' => $invoice->paid_amount - $refundAmount,
                'balance' => $invoice->balance + $refundAmount,
            ]);

            return $payment->fresh(['invoice']);
        });
    }

    /**
     * Generate a unique payment number.
     *
     * @param int $companyId
     * @return string
     */
    private function generatePaymentNumber(int $companyId): string
    {
        $year = now()->year;
        $month = now()->format('m');

        $lastPayment = Payment::where('company_id', $companyId)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastPayment ? ((int) substr($lastPayment->payment_number, -4)) + 1 : 1;

        return sprintf('PAY-%s-%s-%04d', $year, $month, $sequence);
    }

    /**
     * Get total payments for a company.
     *
     * @param int $companyId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return float
     */
    public function getTotalPayments(int $companyId, ?string $startDate = null, ?string $endDate = null): float
    {
        $query = Payment::where('company_id', $companyId)
            ->where('status', 'completed');

        if ($startDate) {
            $query->where('payment_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('payment_date', '<=', $endDate);
        }

        return (float) $query->sum('amount');
    }
}
