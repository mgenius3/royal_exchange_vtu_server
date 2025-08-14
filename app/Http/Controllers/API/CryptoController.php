<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\CryptoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CryptoCurrency;
use App\Services\TransactionLogger;


class CryptoController extends Controller
{
    protected $cryptoService;

    public function __construct(CryptoService $cryptoService)
    {
        $this->middleware('auth:sanctum'); // Requires API authentication
        $this->cryptoService = $cryptoService;
    }

    /**
     * Get all available cryptocurrencies for users
     */
    public function getCryptocurrencies(Request $request)
    {
        try {
            // $cryptos = CryptoCurrency::where('is_enabled', true)->get();
            $filters = $request->only(['category', 'is_enabled']);
            $cryptos = $this->cryptoService->getCryptoCurrency($filters);
            return response()->json([
                'status' => "success",
                'data' => $cryptos,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => "error",
                'message' => 'Failed to fetch cryptocurrencies: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new transaction (buy/sell) for the authenticated user
     */
    public function storeTransaction(Request $request)
    {
        $rules = [
            'crypto_currency_id' => 'required|exists:crypto_currencies,id',
            'type' => 'required|in:buy,sell',
            'amount' => 'required|numeric|min:0|max:1000000',
            'payment_method' => 'nullable|in:bank_transfer,wallet_balance',
            'tx_hash' => 'nullable|string|max:255',
        ];

        if ($request->type === 'sell') {
            // $rules['proof_file'] = 'required|file|mimes:jpg,png,pdf|max:2048';
            $rules['proof_file'] = 'required|string';
        }elseif ($request->type === 'buy') {
            $rules['wallet_address'] = 'required|string|max:255';
            if ($request->payment_method === 'bank_transfer') {
                // $rules['proof_file'] = 'required|file|mimes:jpg,png,pdf|max:2048';
                $rules['proof_file'] = 'required|string';
            }
            // No proof_file rule for wallet_balance
        }

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->all();
        $data['user_id'] = Auth::id(); // Automatically set the authenticated user's ID

        // if ($request->hasFile('proof_file')) {
        //     $data['proof_file'] = $request->file('proof_file');
        // }

        try {
            $transaction = $this->cryptoService->createTransaction($data, Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully',
                'data' => $transaction,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get all transactions for the authenticated user
     */
    public function getUserTransactions()
    {
        try {
            $transactions = $this->cryptoService->getAllTransactions(['user_id' => Auth::id()]);
            return response()->json([
                'status' => 'success',
                'data' => $transactions,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch transactions: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific transaction by ID for the authenticated user
     */
    public function getTransaction($transactionId)
    {
        try {
            $transaction = $this->cryptoService->getTransactionById($transactionId);

            // Ensure the transaction belongs to the authenticated user
            if ($transaction->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this transaction',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $transaction,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found or error occurred: ' . $e->getMessage(),
            ], 404);
        }
    }
}