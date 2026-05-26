<?php

namespace App\Mail;

use App\Models\Gym;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SuperAdminSubscriptionNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $gym;

    /**
     * Create a new message instance.
     */
    public function __construct(Gym $gym)
    {
        $this->gym = $gym;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Gym SaaS Subscription Alert! - ' . $this->gym->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.super-admin-subscription-notification',
        );
    }
}
