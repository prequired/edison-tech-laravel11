<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\ContactSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormSubmission extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public readonly ContactSubmission $submission
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Contact Form Submission from ' . $this->submission->name,
            replyTo: [$this->submission->email],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-form-submission',
            with: [
                'name' => $this->submission->name,
                'email' => $this->submission->email,
                'phone' => $this->submission->phone,
                'subject' => $this->submission->subject,
                'message' => $this->submission->message,
                'company' => $this->submission->company,
                'submittedAt' => $this->submission->created_at,
                'viewUrl' => route('admin.contacts.show', $this->submission),
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
