<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoicePaid extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public readonly Invoice $invoice,
        public readonly Payment $payment
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Received for Invoice ' . $this->invoice->invoice_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice-paid',
            with: [
                'invoiceNumber' => $this->invoice->invoice_number,
                'companyName' => $this->invoice->company->name,
                'paymentAmount' => $this->payment->amount,
                'paymentMethod' => $this->payment->payment_method,
                'paymentDate' => $this->payment->payment_date,
                'transactionId' => $this->payment->transaction_id,
                'viewUrl' => route('client.invoices.show', $this->invoice),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
