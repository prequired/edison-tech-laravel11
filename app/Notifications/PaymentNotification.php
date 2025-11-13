<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Payment $payment,
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
            ->greeting('Hello!')
            ->line($this->getBody());

        if ($this->action === 'completed') {
            $message->line('Transaction ID: ' . $this->payment->transaction_id);
        }

        if ($this->payment->invoice) {
            $message->action('View Invoice', route('client.invoices.show', $this->payment->invoice));
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
            'payment_id' => $this->payment->id,
            'invoice_id' => $this->payment->invoice_id,
            'action' => $this->action,
            'amount' => $this->payment->amount,
            'payment_method' => $this->payment->payment_method,
            'status' => $this->payment->status,
            'transaction_id' => $this->payment->transaction_id,
        ];
    }

    /**
     * Get the notification subject based on action.
     */
    private function getSubject(): string
    {
        $amount = number_format($this->payment->amount, 2);

        return match ($this->action) {
            'completed' => "Payment Received: \${$amount}",
            'failed' => "Payment Failed: \${$amount}",
            'refunded' => "Payment Refunded: \${$amount}",
            'pending' => "Payment Pending: \${$amount}",
            default => "Payment Update: \${$amount}",
        };
    }

    /**
     * Get the body message based on action.
     */
    private function getBody(): string
    {
        $amount = number_format($this->payment->amount, 2);
        $method = ucfirst(str_replace('_', ' ', $this->payment->payment_method));

        return match ($this->action) {
            'completed' => "Your payment of \${$amount} via {$method} has been successfully processed.",
            'failed' => "Your payment of \${$amount} via {$method} has failed. Please try again or contact support.",
            'refunded' => "A refund of \${$amount} has been processed to your {$method}. It may take 5-10 business days to appear.",
            'pending' => "Your payment of \${$amount} via {$method} is being processed.",
            default => "Your payment of \${$amount} status has been updated.",
        };
    }
}
