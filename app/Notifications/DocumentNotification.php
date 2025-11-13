<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Document $document,
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
        return (new MailMessage)
            ->subject($this->getSubject())
            ->greeting('Hello!')
            ->line($this->getBody())
            ->action('View Document', route('client.documents.show', $this->document))
            ->action('Download', route('client.documents.download', $this->document))
            ->line('If you have any questions about this document, please contact us.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'document_id' => $this->document->id,
            'document_title' => $this->document->title,
            'document_type' => $this->document->document_type,
            'action' => $this->action,
            'file_name' => $this->document->file_name,
        ];
    }

    /**
     * Get the notification subject based on action.
     */
    private function getSubject(): string
    {
        return match ($this->action) {
            'uploaded' => 'New Document: ' . $this->document->title,
            'updated' => 'Document Updated: ' . $this->document->title,
            'version_uploaded' => 'New Version Available: ' . $this->document->title,
            default => 'Document Notification: ' . $this->document->title,
        };
    }

    /**
     * Get the body message based on action.
     */
    private function getBody(): string
    {
        $type = ucfirst($this->document->document_type);

        return match ($this->action) {
            'uploaded' => "A new {$type} document \"{$this->document->title}\" has been uploaded and is now available for you to view and download.",
            'updated' => "The {$type} document \"{$this->document->title}\" has been updated with new information.",
            'version_uploaded' => "A new version of the {$type} document \"{$this->document->title}\" has been uploaded.",
            default => "The document \"{$this->document->title}\" has been updated.",
        };
    }
}
