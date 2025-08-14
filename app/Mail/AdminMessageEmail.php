<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminMessageEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $subject;
    public $adminMessage; // Renamed from $message to $adminMessage

    public function __construct($userName, $subject, $adminMessage)
    {
        $this->userName = $userName;
        $this->subject = $subject;
        $this->adminMessage = $adminMessage; // Renamed
    }

    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.admin_message')
                    ->with([
                        'userName' => $this->userName,
                        'adminMessage' => $this->adminMessage, // Renamed
                    ]);
    }
}