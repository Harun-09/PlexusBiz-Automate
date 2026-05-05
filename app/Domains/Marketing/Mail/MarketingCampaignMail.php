<?php

namespace App\Domains\Marketing\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class MarketingCampaignMail extends Mailable
{
    public function __construct(
        public readonly string $subjectLine,
        public readonly string $body,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.marketing.campaign',
            with: [
                'body' => $this->body,
            ],
        );
    }
}
