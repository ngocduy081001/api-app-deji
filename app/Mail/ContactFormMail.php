<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Inertia\Inertia;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     *
     * @param array $details
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        $subject = 'Contact Form Submission';

        if (isset($this->details['return'])) {
            switch ($this->details['return']) {
                case 'Test':
                    $subject = 'Test Email Subject';
                    break;

                case 'Booking':
                    $subject = 'New Booking Confirmation';
                    break;

                case 'Order':
                    $subject = 'New Order Confirmation';
                    break;

                default:
                    $subject = 'Contact Form Submission';
                    break;
            }
        }

        return new Envelope(
            subject: $subject,
        );
    }


    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        if (isset($this->details['return']) && $this->details['return'] === 'Test') {
            return new Content(
                view: 'emails.test', // Use a different view for test
            );
        } else if (isset($this->details['return']) && $this->details['return'] === 'Booking') {
            return new Content(
                view: 'emails.booking', // Default view
            );
        } else if (isset($this->details['return']) && $this->details['return'] === 'Order') {
            return new Content(
                view: 'emails.order', // Default view
            );
        } else {
            return new Content(
                view: 'emails.contact', // Default view
            );
        }
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
