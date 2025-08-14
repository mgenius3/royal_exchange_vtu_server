<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserEmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $code;

    public function __construct($user, $code)
    {
        $this->user = $user;
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject("Email Verification")
            ->view('emails.email_verification')
            ->with([
                'user' => $this->user,
                'code' => $this->code,
                'expires_in' => 15 // minutes
            ]);
    }
}
