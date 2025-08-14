<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserActivityEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $activity;
    public $details;

    public function __construct($user, $activity, $details)
    {
        $this->user = $user;
        $this->activity = $activity;
        $this->details = $details;
    }

    public function build()
    {
        return $this->subject("Activity Notification: {$this->activity}")
                    ->view('emails.user_activity')
                    ->with([
                        'userName' => $this->user->name,
                        'activity' => $this->activity,
                        'details' => $this->details
                    ]);
    }
}