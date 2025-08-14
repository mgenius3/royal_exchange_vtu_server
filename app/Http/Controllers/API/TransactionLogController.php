<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\TransactionLogger;
use App\Models\TransactionLog;
use Illuminate\Support\Facades\Auth;



class TransactionLogController extends Controller
{
    public function getTransactionLogs()
    {
        $logs = TransactionLog::where('user_id', Auth::id())
            ->latest()
            ->get();
    
        return response()->json([
            'transaction_logs' => $logs
        ]);
    }
}