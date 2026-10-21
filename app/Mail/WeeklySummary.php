<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class WeeklySummary extends Mailable
{
    public function __construct(public array $data)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'MyBlog weekly summary',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-summary',
        );
    }
}