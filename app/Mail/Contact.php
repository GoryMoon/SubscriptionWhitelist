<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Contact extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * @var string
     */
    public string $contact;

    /**
     * @var string
     */
    public string $message;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $display_name;

    /**
     * Create a new message instance.
     *
     * @param string $display_name
     * @param string $name
     * @param string $contact
     * @param string $message
     */
    public function __construct($display_name, $name, $contact, $message)
    {
        $this->contact = $contact;
        $this->message = nl2br($message);
        $this->name = $name;
        $this->display_name = $display_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): Contact
    {
        return $this
            ->subject('Contact from ' . $this->display_name . ' (' . $this->name . ')')
            ->markdown('emails.contact');
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags(): array
    {
        return ['mail', 'contact'];
    }
}
