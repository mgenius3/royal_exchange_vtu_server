<?php

namespace App\Services;

use App\Mail\PasswordResetCode;
use App\Models\EmailVerification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Services\EmailService;


class UserService
{

    protected $emailService;


    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }


    public function createUser(array $data)
    {
        $data['password'] = Hash::make($data['password']); // Hash password before saving

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => $data['password'],
            'wallet_balance' => 0,
            'status' => 'active',
            'kyc_status' => 'pending'
        ]);
    }

    public function getAllUsers()
    {
        return User::all();
    }

    public function getUserById($id)
    {
        return User::find($id);
    }

    public function getUserByEmail($email)
    {
        return User::find($email);
    }
    
    public function updateUser($id, $data)
    {
        $user = User::find($id);
        if (!$user) {
            return null;
        }
        $user->update($data);
        return $user;
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return false; // User not found
        }
        $user->delete();
        return true; // User deleted successfully
    }

    public function suspendUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return null;
        }
        $user->status = 'suspended';
        $user->save();
        return $user;
    }

    public function activateUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return null;
        }
        $user->status = 'active';
        $user->save();
        return $user;
    }

    public function resetPassword($id)
    {
        $user = User::find($id);
        if (!$user) {
            return null;
        }
        $newPassword = 'password123';
        $user->password = Hash::make($newPassword);
        $user->save();
        return ['user' => $user, 'new_password' => $newPassword];
    }

    public function updatePassword($id, $newPassword)
    {
        $user = User::find($id);
        if (!$user) {
            return null; // User not found
        }

        // Hash the new password
        $user->password = Hash::make($newPassword);
        $user->save();

        return $user;
    }

    public function approveKYC($id)
    {
        return $this->updateKYCStatus($id, 'approved');
    }

    public function rejectKYC($id)
    {
        return $this->updateKYCStatus($id, 'rejected');
    }

    private function updateKYCStatus($id, $status)
    {
        $user = User::find($id);
        if (!$user) {
            return null;
        }
        $user->kyc_status = $status;
        $user->save();
        return $user;
    }

    public function fundWallet($id, $amount)
    {
        $user = User::find($id);
        if (!$user) {
            return null;
        }
        $user->wallet_balance += $amount;
        $user->save();
        return $user;
    }

    public function deductWallet($id, $amount)
    {
        $user = User::find($id);
        if (!$user || $user->wallet_balance < $amount) {
            return null;
        }
        $user->wallet_balance -= $amount;
        $user->save();
        return $user;
    }

    public function updateWithdrawalBank($id, $bankDetails)
    {
        $user = User::find($id);
        if (!$user) {
            return null;
        }
        $user->withdrawal_bank = $bankDetails; // Store the bank details as an object
        $user->save();
        return $user;
    }

    public function initiatePasswordReset($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return false;
        }
        // Generate a 6-digit code
        $code = sprintf("%06d", mt_rand(100000, 999999));
        // Store the code in password_resets table
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $code,
                'created_at' => now()
            ]
        );
        // Send email with the code
        try {
            Mail::to($email)->send(new PasswordResetCode($code));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email: ' . $e->getMessage());
            return false;
        }
    }

    public function verifyResetCode($email, $code)
    {
        $reset = DB::table('password_resets')
            ->where('email', $email)
            ->where('token', $code)
            ->where('created_at', '>=', now()->subMinutes(15)) // Code expires in 15 minutes
            ->first();
        if (!$reset) {
            return false;
        }


        return true;
    }

    public function setNewPassword($email, $newPassword)
    {
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->password = bcrypt($newPassword);
            $user->save();

            // Delete the used reset code
            DB::table('password_resets')->where('email', $email)->delete();
            return $user;
        }
        return false;
    }


    public function sendEmailVerificationCode($email)
    {
        try {
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                return false;
            }

            // Check if email is already verified
            if ($user->email_verified_at) {
                return false; // Email already verified
            }

            // Generate 6-digit verification code
            $verificationCode = $this->generateVerificationCode();
            
            // Store or update verification code in database
            EmailVerification::updateOrCreate(
                ['email' => $email],
                [
                    'code' => $verificationCode,
                    'expires_at' => Carbon::now()->addMinutes(15), // 15 minutes expiry
                    'created_at' => Carbon::now(),
                ]
            );

            // Send verification email
            $this->emailService->sendVerificationEmail($user, $verificationCode);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email verification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify email with the provided code
     */
    public function verifyEmailCode($email, $code)
    {
        try {
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                return false;
            }

            // Check if email is already verified
            if ($user->email_verified_at) {
                return $user; // Already verified, return user
            }

            $verification = EmailVerification::where('email', $email)
                ->where('code', $code)
                ->where('expires_at', '>', Carbon::now())
                ->first();
   

            if (!$verification) {
                return false; // Invalid or expired code
            }

            // Mark email as verified
            $user->is_verified = 1;
            $user->save();

            // Delete the verification record
            $verification->delete();

            return $user;
        } catch (\Exception $e) {
            Log::error('Failed to verify email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Resend email verification code
     */
    public function resendEmailVerificationCode($email)
    {
        try {
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                return false;
            }

            // Check if email is already verified
            if ($user->email_verified_at) {
                return false; // Email already verified
            }

            // Check if a code was sent recently (rate limiting)
            $recentVerification = EmailVerification::where('email', $email)
                ->where('created_at', '>', Carbon::now()->subMinutes(2))
                ->first();

            if ($recentVerification) {
                return false; // Code sent too recently
            }

            // Generate new verification code
            return $this->sendEmailVerificationCode($email);
        } catch (\Exception $e) {
            Log::error('Failed to resend email verification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get email verification status
     */
    public function getEmailVerificationStatus($email)
    {
        try {
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                return null;
            }

            return !is_null($user->email_verified_at);
        } catch (\Exception $e) {
            Log::error('Failed to get email verification status: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Mark email as verified (admin function)
     */
    public function markEmailAsVerified($userId)
    {
        try {
            $user = User::find($userId);
            
            if (!$user) {
                return false;
            }

            $user->is_verified = 1;

            $user->save();

            // Clean up any pending verification codes
            EmailVerification::where('email', $user->email)->delete();

            return $user;
        } catch (\Exception $e) {
            Log::error('Failed to mark email as verified: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clean up expired verification codes
     */
    public function cleanupExpiredVerifications()
    {
        try {
            EmailVerification::where('expires_at', '<', Carbon::now())->delete();
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to cleanup expired verifications: ' . $e->getMessage());
            return false;
        }
    }

    // ===== PRIVATE HELPER METHODS =====

    /**
     * Generate a 6-digit verification code
     */
    private function generateVerificationCode()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send verification email to user
     */
   
}