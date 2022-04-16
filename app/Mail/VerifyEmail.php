<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $pin;
    public $email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pin,$email)
    {
        $this->pin=$pin;
        $this->email=$email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("Email Verification")
            ->markdown('emails.verify');
    }
}
