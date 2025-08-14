<?php

namespace App\Services;

use App\Mail\UserActivityEmail;
use App\Mail\AdminMessageEmail;
use App\Mail\UserEmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class EmailService
{
    /**
     * Send an email to a user after an activity.
     */
    public function sendActivityEmail(User $user, string $activity, array $details)
    {
        try {
            Mail::to($user->email)->send(new UserActivityEmail($user, $activity, $details));
            return true;
        } catch (\Exception $e) {
            // \Log::error('Failed to send activity email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send an email from admin to a specific user.
     */
    public function sendAdminEmailToUser(User $user, string $subject, string $message)
    {
        try {
            Mail::to($user->email)->send(new AdminMessageEmail($user->name, $subject, $message));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send admin email to user: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Broadcast an email from admin to all users.
     */
    public function broadcastAdminEmail(string $subject, string $message)
    {
        try {
            $users = User::all();
            foreach ($users as $user) {
                Mail::to($user->email)->send(new AdminMessageEmail($user->name, $subject, $message));
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to broadcast admin email: ' . $e->getMessage());
            return false;
        }


      
    }

    public function sendVerificationEmail(User $user, $code)
    {
        try {
            Log::info($code);

            Mail::to($user->email)->send(new UserEmailVerification($user, $code));

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send verification email: ' . $e->getMessage());
            throw $e;
        }
    }
}

