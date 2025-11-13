<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\InvoicePaid;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ProcessPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly int $paymentId
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(PaymentService $paymentService): void
    {
        $payment = Payment::with(['invoice.company.users'])->findOrFail($this->paymentId);

        DB::transaction(function () use ($payment, $paymentService) {
            // Process payment through payment gateway
            $result = $paymentService->processPaymentGateway($payment);

            if ($result['success']) {
                // Update payment status
                $payment->update([
                    'status' => 'completed',
                    'transaction_id' => $result['transaction_id'],
                    'payment_date' => now(),
                ]);

                // Update invoice if fully paid
                $invoice = $payment->invoice;
                $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');

                if ($totalPaid >= $invoice->total_amount) {
                    $invoice->update(['status' => 'paid']);
                } elseif ($totalPaid > 0) {
                    $invoice->update(['status' => 'partial']);
                }

                // Send confirmation email
                $clientUsers = $invoice->company->users()
                    ->where('role', 'client')
                    ->where('is_active', true)
                    ->get();

                foreach ($clientUsers as $user) {
                    Mail::to($user->email)->send(new InvoicePaid($invoice, $payment));
                }
            } else {
                // Mark payment as failed
                $payment->update([
                    'status' => 'failed',
                    'notes' => $result['error'] ?? 'Payment processing failed',
                ]);
            }
        });
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $payment = Payment::find($this->paymentId);

        if ($payment) {
            $payment->update([
                'status' => 'failed',
                'notes' => 'Job failed: ' . $exception->getMessage(),
            ]);
        }

        \Log::error('Failed to process payment', [
            'payment_id' => $this->paymentId,
            'error' => $exception->getMessage(),
        ]);
    }
}
