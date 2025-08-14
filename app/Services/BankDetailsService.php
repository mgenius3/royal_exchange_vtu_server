<?php

namespace App\Services;

use App\Models\BankDetails;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class BankDetailsService
{
    public function getBankDetails($userId = null)
    {
        $query = BankDetails::query();

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        return $query->get(); // Fetch all bank details, optionally filtered by user_id
    }

    public function createBankDetails($data, $userId)
    {
        $bankDetails = BankDetails::create([
            'user_id' => $userId,
            'bank_name' => $data['bank_name'],
            'account_name' => $data['account_name'],
            'account_number' => $data['account_number'],
            'ifsc_code' => $data['ifsc_code'] ?? null,
            'swift_code' => $data['swift_code'] ?? null,
        ]);

        // Log the action
        AuditLog::create([
            'user_id' => $userId,
            'action' => 'bank_details_created',
            'details' => json_encode(['user_id' => $userId, 'bank_details_id' => $bankDetails->id]),
            'created_at' => now(),
        ]);

        return $bankDetails;
    }

    public function deleteBankDetails($bankDetailsId, $userId)
    {
        $bankDetails = BankDetails::where('id', $bankDetailsId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $bankDetails->delete();

        // Log the action
        AuditLog::create([
            'user_id' => $userId,
            'action' => 'bank_details_deleted',
            'details' => json_encode(['user_id' => $userId, 'bank_details_id' => $bankDetailsId]),
            'created_at' => now(),
        ]);
    }
}