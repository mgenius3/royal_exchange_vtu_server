<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Services\EmailService;
use App\Models\User;
use Illuminate\Http\Request;

class AdminEmailController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Show the form to send an email to a specific user.
     */
    public function showSendToUserForm()
    {
        $users = User::all(); // Fetch all users for the dropdown
        return view('email_management.send-to-user', compact('users'));
    }

    /**
     * Handle the form submission to send an email to a specific user.
     */
    public function sendToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $user = User::find($request->user_id);
        $result = $this->emailService->sendAdminEmailToUser(
            $user,
            $request->subject,
            $request->message
        );

        if ($result) {
            return redirect()->route('admin.email.send-to-user.form')
                ->with('success', 'Email sent successfully!');
        } else {
            return redirect()->route('admin.email.send-to-user.form')
                ->with('error', 'Failed to send email.');
        }
    }

    /**
     * Show the form to broadcast an email to all users.
     */
    public function showBroadcastForm()
    {
        return view('email_management.broadcast');
    }

    /**
     * Handle the form submission to broadcast an email to all users.
     */
    public function broadcast(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $result = $this->emailService->broadcastAdminEmail(
            $request->subject,
            $request->message
        );

        if ($result) {
            return redirect()->route('admin.email.broadcast.form')
                ->with('success', 'Broadcast email sent successfully!');
        } else {
            return redirect()->route('admin.email.broadcast.form')
                ->with('error', 'Failed to broadcast email.');
        }
    }
}