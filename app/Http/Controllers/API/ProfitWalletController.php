<?php

namespace App\Http\Controllers;

use App\Services\ProfitDistributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfitWalletController  extends Controller
{
    protected $profitDistributionService;

    public function __construct(ProfitDistributionService $profitDistributionService)
    {
        $this->profitDistributionService = $profitDistributionService;
    }

    public function withdraw(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'wallet_type' => 'required|in:admin,developer',
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $result = $this->profitDistributionService->withdrawProfit(
                $validated['wallet_type'],
                $validated['amount']
            );

            return response()->json([
                'message' => 'Withdrawal successful',
                'data' => $result,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Withdrawal failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function transferToUser(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'wallet_type' => 'required|in:admin,developer',
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $result = $this->profitDistributionService->transferToUser(
                $validated['wallet_type'],
                $validated['user_id'],
                $validated['amount']
            );

            return response()->json([
                'message' => 'Transfer to user successful',
                'data' => $result,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Transfer failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}