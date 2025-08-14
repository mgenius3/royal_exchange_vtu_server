<?php
namespace App\Services;

use App\Models\TransactionLog;
use Illuminate\Support\Facades\Auth;

class TransactionLogger
{
    public static function log(
        string $transactionType,
        ?string $referenceId,
        array $details,
        bool $success = false
    ): TransactionLog {
        return TransactionLog::create([
            'user_id' => Auth::id(),
            'transaction_type' => $transactionType,
            'reference_id' => $referenceId,
            'details' => $details,
            'success' => $success
        ]);
    }

    public static function updateSuccess(int $logId, bool $success = true): void
    {
        TransactionLog::where('id', $logId)
            ->where('user_id', Auth::id())
            ->update(['success' => $success]);
    }
}