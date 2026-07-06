<?php

namespace App\Domains\Support\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupportEscalationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly string $subjectLine,
        public readonly string $description,
        public readonly ?string $orderNumber = null
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[AI Support Escalation] " . $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.support.escalation',
            with: [
                'user' => $this->user,
                'subjectLine' => $this->subjectLine,
                'description' => $this->description,
                'orderNumber' => $this->orderNumber,
            ],
        );
    }
}
