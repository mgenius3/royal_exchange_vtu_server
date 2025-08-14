<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        return response()->json($this->userService->getAllUsers(), 200);
    }

    public function show($id)
    {
        $user = $this->userService->getUserById($id);
        return $user ? response()->json([
            'status' => "success",
            'user' => $user
        ], 200) : response()->json(['message' => 'User not found'], 404);
    }

    public function showByEmail($email)
    {
        $user = $this->userService->getUserByEmail($email);
        return $user ? response()->json([
            'status' => "success",
            'user' => $user
        ], 200) : response()->json(['message' => 'User not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $user = $this->userService->updateUser($id, $request->only(['name', 'email', 'phone']));
        return $user ? response()->json(['message' => 'User updated successfully', 'user' => $user], 200)
            : response()->json(['message' => 'User not found'], 404);
    }


    public function destroy($id)
    {
        $deleted = $this->userService->deleteUser($id);
        return $deleted
            ? response()->json(['message' => 'User deleted successfully'], 200)
            : response()->json(['message' => 'User not found'], 404);
    }

    public function suspendUser($id)
    {
        $user = $this->userService->suspendUser($id);
        return $user ? response()->json(['message' => 'User suspended successfully'], 200)
            : response()->json(['message' => 'User not found'], 404);
    }

    public function activateUser($id)
    {
        $user = $this->userService->activateUser($id);
        return $user ? response()->json(['message' => 'User activated successfully'], 200)
            : response()->json(['message' => 'User not found'], 404);
    }

    public function resetPassword($id)
    {
        $result = $this->userService->resetPassword($id);
        return $result ? response()->json(['message' => 'Password reset successfully', 'new_password' => $result['new_password']], 200)
            : response()->json(['message' => 'User not found'], 404);
    }

    public function approveKYC($id)
    {
        $user = $this->userService->approveKYC($id);
        return $user ? response()->json(['message' => 'KYC approved successfully'], 200)
            : response()->json(['message' => 'User not found'], 404);
    }

    public function rejectKYC($id)
    {
        $user = $this->userService->rejectKYC($id);
        return $user ? response()->json(['message' => 'KYC rejected successfully'], 200)
            : response()->json(['message' => 'User not found'], 404);
    }

    public function fundWallet(Request $request, $id)
    {
        $user = $this->userService->fundWallet($id, $request->amount);
        return $user ? response()->json(['message' => 'Wallet funded successfully', 'new_balance' => $user->wallet_balance], 200)
            : response()->json(['message' => 'User not found'], 404);
    }

    public function deductWallet(Request $request, $id)
    {
        $user = $this->userService->deductWallet($id, $request->amount);
        return $user ? response()->json(['message' => 'Amount deducted successfully', 'new_balance' => $user->wallet_balance], 200)
            : response()->json(['message' => 'Insufficient balance or user not found'], 400);
    }

    public function updateUserPassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'required|string|min:8',
        ]);

        // $userService = new UserService();
        $user = $this->userService->updatePassword($id, $request->new_password);

        if ($user) {
            return response()->json(['message' => 'Password updated successfully', 'user' => $user], 200);
        } else {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    public function updateWithdrawalBank(Request $request, $id)
    {
        // Validate the bank details
        $request->validate([
            'bank_name' => 'required|string',
            'bank_code' => 'required|string',
            'account_number' => 'required|string|size:10', // Example validation
        ]);

        // Create the bank details object
        $bankDetails = [
            'bank_name' => $request->bank_name,
            'bank_code' => $request->bank_code,
            'account_number' => $request->account_number,
        ];

        $user = $this->userService->updateWithdrawalBank($id, $bankDetails);

        return $user
            ? response()->json(['message' => 'Withdrawal bank updated', 'user' => $user], 200)
            : response()->json(['message' => 'User not found'], 404);
    }


    public function forgetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $result = $this->userService->initiatePasswordReset($request->email);
        return $result
            ? response()->json(['message' => 'Password reset code sent to email'], 200)
            : response()->json(['message' => 'User not found or email sending failed'], 404);
    }

    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);
        $user = $this->userService->verifyResetCode($request->email, $request->code);
        return $user
            ? response()->json(['message' => 'Password reset successfully'], 200)
            : response()->json(['message' => 'Invalid code'], 400);
    }

    public function setNewPassword(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);
        $user = $this->userService->setNewPassword($request->email, $request->password);
        return $user
            ? response()->json(['message' => 'password reset successfully'], 200)
            : response()->json(['message' => 'user not found'], 400);
    }


     // ===== EMAIL VERIFICATION METHODS =====

    /**
     * Send email verification code to user
     */
    public function sendEmailVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $result = $this->userService->sendEmailVerificationCode($request->email);
            
            if ($result) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Verification code sent to your email'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found or email sending failed'
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Email verification sending failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send verification code'
            ], 500);
        }
    }

    /**
     * Verify email with the provided code
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6'
        ]);

        try {
            $result = $this->userService->verifyEmailCode($request->email, $request->code);
            
            if ($result) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Email verified successfully',
                    'user' => $result
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid verification code or code has expired'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Email verification failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Email verification failed'
            ], 500);
        }
    }

    /**
     * Resend email verification code
     */
    public function resendEmailVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $result = $this->userService->resendEmailVerificationCode($request->email);
            
            if ($result) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'New verification code sent to your email'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found or email already verified'
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Email verification resend failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to resend verification code'
            ], 500);
        }
    }

    /**
     * Check email verification status
     */
    public function checkEmailVerificationStatus(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $status = $this->userService->getEmailVerificationStatus($request->email);
            
            if ($status !== null) {
                return response()->json([
                    'status' => 'success',
                    'is_verified' => $status,
                    'message' => $status ? 'Email is verified' : 'Email is not verified'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Email verification status check failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to check verification status'
            ], 500);
        }
    }

    /**
     * Mark email as verified (admin only)
     */
    public function markEmailAsVerified($id)
    {
        try {
            $user = $this->userService->markEmailAsVerified($id);
            
            if ($user) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Email marked as verified successfully',
                    'user' => $user
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Mark email as verified failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to mark email as verified'
            ], 500);
        }
    }
}