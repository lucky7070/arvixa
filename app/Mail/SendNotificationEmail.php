<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $site_settings;

    public function __construct($data, $site_settings)
    {
        $this->data             = $data;
        $this->site_settings    = $site_settings;
    }

    public function envelope()
    {
        return new Envelope(
            subject: $this->data['title'],
        );
    }

    public function content()
    {
        return new Content(
            view: 'email.custom',
        );
    }

    public function attachments()
    {
        if (!empty($this->data['file'])) {
            return [Attachment::fromPath($this->data['file'])];
        } else {
            return [];
        }
    }
}
