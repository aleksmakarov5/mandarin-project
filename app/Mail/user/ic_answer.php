<?php

namespace App\Mail\user;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ic_answer extends Mailable
{
    use Queueable, SerializesModels;
    public $app_number;
    public $subject;
    public $message;
    public $location;

    /**
     * Create a new message instance.
     */
    public function __construct($app_number, $subject, $message, $location)
    {
        $this->app_number = $app_number;
        $this->subject = $subject;
        $this->message = $message;
        $this->location = $location;
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'email.user.ic_answer',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromStorage('/storage/files/' . $this->app_number . '/' . $this->location)
        ];
    }
}
