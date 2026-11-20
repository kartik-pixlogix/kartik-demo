<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Internal notification: a new website enquiry has arrived. */
class ReviewRequested extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Lead $lead) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Website enquiry — '.$this->lead->company,
            replyTo: [$this->lead->email],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.review-requested');
    }
}
