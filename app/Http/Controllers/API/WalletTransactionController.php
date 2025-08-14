<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WalletTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Fetch the authenticated user's wallet transactions.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 20); // Default to 20 items per page
            $transactions = WalletTransaction::where('user_id', $request->user()->id)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Wallet transactions retrieved successfully',
                'data' => $transactions,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch wallet transactions', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch wallet transactions: ' . $e->getMessage(),
            ], 500);
        }
    }
}