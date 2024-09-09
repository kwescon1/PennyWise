<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordResetMail extends Mailable implements ShouldQueue
{
    public User $user;
    public string $otpCode;
    public bool $isRequest;

    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $otpCode, bool $isRequest)
    {
        $this->user = $user;
        $this->otpCode = $otpCode;
        $this->isRequest = $isRequest;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isRequest ? __('app.reset_password_request_mail_subject') : __('app.reset_password_success_mail_subject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.auth.password-reset',
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
