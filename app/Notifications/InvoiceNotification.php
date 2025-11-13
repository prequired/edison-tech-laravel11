<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Invoice $invoice,
        public readonly string $action
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->getSubject())
            ->line($this->getGreeting())
            ->line($this->getBody());

        if ($this->action === 'sent') {
            $message->action('View Invoice', route('client.invoices.show', $this->invoice))
                ->action('Pay Now', route('client.invoices.pay', $this->invoice));
        } elseif ($this->action === 'overdue') {
            $message->action('Pay Now', route('client.invoices.pay', $this->invoice));
        } else {
            $message->action('View Invoice', route('client.invoices.show', $this->invoice));
        }

        return $message->line('Thank you for your business!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'action' => $this->action,
            'amount' => $this->invoice->total_amount,
            'due_date' => $this->invoice->due_date?->toDateString(),
        ];
    }

    /**
     * Get the notification subject based on action.
     */
    private function getSubject(): string
    {
        return match ($this->action) {
            'sent' => 'New Invoice: ' . $this->invoice->invoice_number,
            'reminder' => 'Payment Reminder: Invoice ' . $this->invoice->invoice_number,
            'overdue' => 'Overdue Invoice: ' . $this->invoice->invoice_number,
            'paid' => 'Payment Received: Invoice ' . $this->invoice->invoice_number,
            default => 'Invoice Update: ' . $this->invoice->invoice_number,
        };
    }

    /**
     * Get the greeting based on action.
     */
    private function getGreeting(): string
    {
        return match ($this->action) {
            'sent' => 'You have received a new invoice.',
            'reminder' => 'This is a reminder about your upcoming payment.',
            'overdue' => 'Your invoice payment is overdue.',
            'paid' => 'We have received your payment.',
            default => 'Your invoice has been updated.',
        };
    }

    /**
     * Get the body message based on action.
     */
    private function getBody(): string
    {
        $amount = number_format($this->invoice->total_amount, 2);

        return match ($this->action) {
            'sent' => "Invoice #{$this->invoice->invoice_number} for \${$amount} has been issued.",
            'reminder' => "Invoice #{$this->invoice->invoice_number} for \${$amount} is due on {$this->invoice->due_date->format('F d, Y')}.",
            'overdue' => "Invoice #{$this->invoice->invoice_number} for \${$amount} is now overdue. Please make payment as soon as possible.",
            'paid' => "Your payment of \${$amount} for invoice #{$this->invoice->invoice_number} has been received and processed.",
            default => "Invoice #{$this->invoice->invoice_number} has been updated.",
        };
    }
}
