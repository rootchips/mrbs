<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class SendMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $template;
    public $subject;
    public $receipt;
    /**
     * Create a new message instance.
     */
    public function __construct($subject, $template, $receipt = null)
    {
        $this->subject = $subject;
        $this->template = $template;
        $this->receipt = $receipt;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->subject}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail',
            with: [
                'template' => $this->template
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
        if ($this->receipt) {
            return [
                Attachment::fromPath($this->receipt)
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}