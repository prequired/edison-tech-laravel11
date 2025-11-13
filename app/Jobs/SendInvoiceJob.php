<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\InvoiceSent;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly int $invoiceId
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $invoice = Invoice::with(['company.users'])->findOrFail($this->invoiceId);

        // Get all client users from the company
        $clientUsers = $invoice->company->users()
            ->where('role', 'client')
            ->where('is_active', true)
            ->get();

        foreach ($clientUsers as $user) {
            Mail::to($user->email)->send(new InvoiceSent($invoice));
        }

        // Update invoice sent status
        $invoice->update([
            'sent_at' => now(),
            'status' => 'sent',
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Log the failure
        \Log::error('Failed to send invoice', [
            'invoice_id' => $this->invoiceId,
            'error' => $exception->getMessage(),
        ]);
    }
}
