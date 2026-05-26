<?php

namespace App\Mail;

use App\Models\Gym;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $gym;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(Gym $gym, User $user)
    {
        $this->gym = $gym;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'GlowGym SaaS Subscription Activated! - ' . $this->gym->subscription_plan,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-confirmation',
        );
    }
}
