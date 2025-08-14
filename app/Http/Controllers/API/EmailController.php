<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\EmailService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Send an email to a specific user (admin only).
     */
    public function sendToUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::find($request->user_id);
        $result = $this->emailService->sendAdminEmailToUser(
            $user,
            $request->subject,
            $request->message
        );

        if ($result) {
            return response()->json(['message' => 'Email sent successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to send email'], 500);
        }
    }

    /**
     * Broadcast an email to all users (admin only).
     */
    public function broadcast(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $result = $this->emailService->broadcastAdminEmail(
            $request->subject,
            $request->message
        );

        if ($result) {
            return response()->json(['message' => 'Broadcast email sent successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to broadcast email'], 500);
        }
    }
}