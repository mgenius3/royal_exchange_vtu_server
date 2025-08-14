<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\GiftCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Services\CloudinaryService;

class GiftCardController extends Controller
{
    protected $giftCardService;

    public function __construct(GiftCardService $giftCardService)
    {
        $this->giftCardService = $giftCardService;
        $this->middleware('auth:sanctum');
    }

    /**
     * List all gift cards available to the user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['name', 'category', 'is_enabled']);
            $giftCards = $this->giftCardService->getGiftCards($filters);

            return response()->json([
                'status' => 'success',
                'data' => $giftCards,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch gift cards: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show details of a specific gift card
     *
     * @param int $giftCardId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($giftCardId)
    {
        try {
            $giftCard = $this->giftCardService->getGiftCardById($giftCardId);

            if (!$giftCard) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gift card not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $giftCard,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch gift card: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new transaction (buy/sell gift card)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeTransaction(Request $request)
    {
        try {

            $rules =  [
                'user_id' => 'required|exists:users,id',
                'gift_card_name' => 'required|string',
                'gift_card_id' => 'required|exists:gift_cards,id',
                'country' => 'required|string',
                'type' => 'required|in:buy,sell',
                'gift_card_type' => 'required|in:physical,ecode',
                'balance' => 'required|numeric|min:0.01',
                'payment_method' => 'required_if:type,buy|in:bank_transfer,wallet_balance',
                // 'proof_file' => 'nullable|required_if:type,sell|required_if:payment_method,bank_transfer'

            ];

            if (($request->type === 'buy' && $request->payment_method === 'bank_transfer') ||
                ($request->type === 'sell' && $request->gift_card_type === 'physical')
            ) {
                $rules['proof_file'] = 'required|string';
            }

            $request->validate($rules);

            $data = $request->all();


            $transaction = $this->giftCardService->createTransaction($data, Auth::id());

            return response()->json([
                'status' => 'success',
                'data' => $transaction,
                'message' => 'Transaction created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 403);
        }
    }

    /**
     * List transactions for the authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userTransactions(Request $request)
    {
        try {
            $filters = $request->only(['status', 'date_range']);
            $transactions = $this->giftCardService->getTransactionsByUser(Auth::id(), $filters);

            return response()->json([
                'status' => 'success',
                'data' => $transactions,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch transactions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show details of a specific transaction
     *
     * @param int $transactionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function transaction($transactionId)
    {
        try {
            $transaction = $this->giftCardService->getTransactionById($transactionId);

            if (!$transaction || $transaction->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction not found or unauthorized',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $transaction,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the status of a transaction (user-initiated, if allowed)
     *
     * @param Request $request
     * @param int $transactionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTransactionStatus(Request $request, $transactionId)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,completed,rejected,flagged',
                'notes' => 'nullable|string|max:500',
            ]);

            $transaction = $this->giftCardService->getTransactionById($transactionId);

            if (!$transaction || $transaction->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction not found or unauthorized',
                ], 404);
            }

            $updatedTransaction = $this->giftCardService->updateTransactionStatus(
                $transactionId,
                $request->status,
                Auth::id(),
                $request->notes
            );

            return response()->json([
                'status' => 'success',
                'data' => $updatedTransaction,
                'message' => 'Transaction status updated successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update transaction status: ' . $e->getMessage(),
            ], 400);
        }
    }
}
