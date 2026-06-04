<?php

namespace Escalated\Filament\Support;

use Escalated\Laravel\Mail\NewsletterMail;
use Escalated\Laravel\Models\Contact;
use Escalated\Laravel\Models\Newsletter\Newsletter;
use Escalated\Laravel\Models\Newsletter\NewsletterDelivery;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NewsletterOperations
{
    public function sendTest(Newsletter $newsletter, string $email, ?string $name = null): void
    {
        $contact = new Contact([
            'email' => strtolower(trim($email)),
            'name' => $name ?? 'Test recipient',
        ]);
        $contact->id = 0;

        $delivery = new NewsletterDelivery([
            'newsletter_id' => $newsletter->id,
            'contact_id' => 0,
            'email_at_send' => $contact->email,
            'tracking_token' => Str::random(40),
            'is_test' => true,
        ]);
        $delivery->setRelation('newsletter', $newsletter);
        $delivery->setRelation('contact', $contact);

        Mail::to($contact->email)->send(new NewsletterMail($delivery));
    }
}
