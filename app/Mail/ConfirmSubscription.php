<?php

namespace App\Mail;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class ConfirmSubscription extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Subscriber $subscriber) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('site.confirm_mail_subject'));
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.confirm-subscription', with: [
            'confirmUrl' => URL::temporarySignedRoute('subscribe.confirm', now()->addDays(7), [
                'locale' => $this->subscriber->locale,
                'subscriber' => $this->subscriber->id,
            ]),
            'unsubscribeUrl' => route('unsubscribe', [
                'locale' => $this->subscriber->locale,
                'token' => $this->subscriber->unsubscribe_token,
            ]),
        ]);
    }
}
