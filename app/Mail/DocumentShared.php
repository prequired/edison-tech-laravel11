<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Document;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentShared extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public readonly Document $document,
        public readonly User $sharedBy
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Document Shared: ' . $this->document->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.document-shared',
            with: [
                'documentTitle' => $this->document->title,
                'documentType' => $this->document->document_type,
                'sharedByName' => $this->sharedBy->name,
                'description' => $this->document->description,
                'viewUrl' => route('client.documents.show', $this->document),
                'downloadUrl' => route('client.documents.download', $this->document),
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
