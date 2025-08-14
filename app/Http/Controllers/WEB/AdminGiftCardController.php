<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Services\GiftCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GiftCard;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AdminGiftCardController extends Controller
{
    protected $giftCardService;

    public function __construct(GiftCardService $giftCardService)
    {
        $this->giftCardService = $giftCardService;
    }

    // Create New Gift Card 
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'stock' => 'required|integer|min:0',
                'countries' => 'required|array|min:1',
                'countries.*.name' => 'required|string|max:255',
                'countries.*.buy_rate' => 'required|numeric|min:0.01',
                'countries.*.sell_rate' => 'required|numeric|min:0.01',
                'image' => 'nullable|image|max:2048', // Max 2MB
            ]);

            $data = $request->all();

            if ($request->hasFile('image')) {
                $cloudinaryService = new CloudinaryService();
                $uploadResult = $cloudinaryService->uploadImage(
                    $request->file('image'),
                    'ads',
                    env('CLOUDINARY_UPLOAD_PRESET', 'davyking')
                );

                $data['image'] = $uploadResult['secure_url'];
                $data['cloudinary_public_id'] = $uploadResult['public_id'];
            }

            $this->giftCardService->createGiftCard($data, Auth::id());

            return redirect()->back()->with('success', 'Gift card added successfully');
        } catch (\Exception $e) {
            Log::error('GiftCard store error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->withErrors('An error occurred while adding the gift card. Please try again.')->withInput();
        }
    }

    // Update Gift Card
    public function update(Request $request, $giftCardId)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'stock' => 'required|integer|min:0',
                'countries' => 'sometimes|array|min:1',
                'countries.*.name' => 'required|string|max:255',
                'countries.*.buy_rate' => 'required|numeric|min:0.01',
                'countries.*.sell_rate' => 'required|numeric|min:0.01',
                'image' => 'nullable|image|max:2048',
            ]);

            $data = $request->all();

            $this->giftCardService->updateGiftCard($giftCardId, $data, Auth::id());
            return redirect()->back()->with('success', 'Gift card updated successfully');
        } catch (\Exception $e) {
            Log::error('GiftCard update error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->withErrors('An error occurred while updating the gift card. Please try again.')->withInput();
        }
    }

    // Display the Gift Card Admin Page
    public function displayGiftCard()
    {
        $giftCards = $this->giftCardService->getGiftCards();
        $transactions = $this->giftCardService->getTransactions(['limit' => 5]);

        return view('gift_cards_management.display', compact('giftCards', 'transactions'));
    }

    // List gift cards
    public function index(Request $request)
    {
        $filters = $request->only(['category', 'is_enabled']);
        $giftCards = $this->giftCardService->getGiftCards($filters);

        return response()->json(['data' => $giftCards], 200);
    }

    // Show the create transaction form
    public function createTransaction()
    {
        $giftCards = GiftCard::all();
        $users = User::all();
        return view('gift_cards_management.create_transaction', compact('giftCards', 'users'));
    }

    // Store a new transaction
    public function storeTransaction(Request $request)
    {
        try {
            $rules = [
                'user_id' => 'required|exists:users,id',
                'gift_card_id' => 'required|exists:gift_cards,id',
                'gift_card_name' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'type' => 'required|in:buy,sell',
                // 'quantity' => 'required|integer|min:1',
                'balance' => 'required|numeric|min:0.01',
                'payment_method' => 'nullable|required_if:type,buy|in:bank_transfer,wallet_balance',
                'tx_hash' => 'nullable|string|max:255',
                'admin_notes' => 'nullable|string',
            ];

            if ($request->type === 'sell') {
                $rules['gift_card_type'] = 'required|in:physical,ecode';
            
                if ($request->gift_card_type === 'ecode') {
                    $rules['ecode'] = 'required|string';
                }
            }
            

            $rules['proof_file'] = [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048',
                \Illuminate\Validation\Rule::requiredIf(function () use ($request) {
                    return ($request->type === 'buy' && $request->payment_method === 'bank_transfer') ||
                        ($request->type === 'sell' && $request->gift_card_type === 'physical');
                }),
            ];

            // $request->validate($rules);
            try {
                $request->validate($rules);
            } catch (\Illuminate\Validation\ValidationException $e) {
                // This will return the exact errors that caused the validation to fail
                return response()->json([
                    'status' => 'validation_failed',
                    'errors' => $e->errors(), // <-- array of fields and messages
                ], 422);
            }


            $data = $request->all();

            // Handle proof file
            if ($request->hasFile('proof_file')) {
                $cloudinaryService = new CloudinaryService();
                $uploadResult = $cloudinaryService->uploadImage(
                    $request->file('proof_file'),
                    'gift_card_proofs',
                    env('CLOUDINARY_UPLOAD_PRESET', 'davyking')
                );
                $data['proof_file'] = $uploadResult['secure_url'];
                $data['cloudinary_public_id'] = $uploadResult['public_id'];
            } elseif (
                ($data['type'] === 'buy' && $data['payment_method'] === 'bank_transfer') ||
                ($data['type'] === 'sell' && $data['gift_card_type'] === 'physical')
            ) {
                throw ValidationException::withMessages(['proof_file' => 'Proof file is required.']);
            }

            $this->giftCardService->createTransaction($data, $data['user_id']);

            return redirect()->route('admin.gift-cards')->with('success', 'Transaction created successfully');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Transaction store error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    // Show all transactions
    public function allTransactions()
    {
        $transactions = $this->giftCardService->getAllTransactions();
        return view('gift_cards_management.all_transactions', compact('transactions'));
    }

    // Show transactions for a specific user
    public function userTransactions($userId)
    {
        $transactions = $this->giftCardService->getTransactionsByUser($userId);
        $user = User::findOrFail($userId);
        return view('gift_cards_management.user_transactions', compact('transactions', 'user'));
    }

    // Show a single transaction
    public function transaction($transactionId)
    {
        $transaction = $this->giftCardService->getTransactionById($transactionId);
        return view('gift_cards_management.transaction', compact('transaction'));
    }

    // List transactions
    public function transactions(Request $request)
    {
        $filters = $request->only(['status', 'user_id', 'date_range']);
        $transactions = $this->giftCardService->getTransactions($filters);

        return response()->json(['data' => $transactions], 200);
    }

    // Show the update transaction status form
    public function editTransactionStatus($transactionId)
    {
        $transaction = $this->giftCardService->getTransactionById($transactionId);
        return view('gift_cards_management.update_transaction_status', compact('transaction'));
    }

    // Update transaction status
    public function updateTransactionStatus(Request $request, $transactionId)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,completed,rejected,flagged',
                'notes' => 'nullable|string|max:500'
            ]);

            $this->giftCardService->updateTransactionStatus(
                $transactionId,
                $request->status,
                Auth::id(),
                $request->notes
            );

            return redirect()->route('transactions.show', $transactionId)->with('success', 'Transaction status updated successfully');
        } catch (\Exception $e) {
            Log::error('Update transaction status error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to update transaction status: ' . $e->getMessage())->withInput();
        }
    }

    // Update gift card rates
    public function updateRates(Request $request)
    {
        try {
            $request->validate([
                'gift_card_id' => 'required|exists:gift_cards,id',
                'countries' => 'required|array|min:1',
                'countries.*.name' => 'required|string|max:255',
                'countries.*.buy_rate' => 'required|numeric|min:0.01',
                'countries.*.sell_rate' => 'required|numeric|min:0.01',
            ]);

            $this->giftCardService->updateRates($request->gift_card_id, $request->countries, Auth::id());

            return redirect()->back()->with('success', 'Rates updated successfully');
        } catch (\Exception $e) {
            Log::error('Update rates error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors('An error occurred while updating rates. Please try again.')->withInput();
        }
    }

    // Toggle gift card availability
    public function toggle(Request $request, $giftCardId)
    {
        try {
            $request->validate([
                'is_enabled' => 'required|boolean',
            ]);

            $giftCard = $this->giftCardService->toggleGiftCard($giftCardId, $request->is_enabled, Auth::id());

            return response()->json(['data' => $giftCard, 'message' => 'Gift card status updated successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Toggle gift card error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTrace()
            ]);
            return response()->json(['error' => 'Failed to toggle gift card: ' . $e->getMessage()], 400);
        }
    }

    // Delete a gift card
    public function deleteGiftCard($giftCardId)
    {
        try {
            $this->giftCardService->deleteGiftCard($giftCardId, Auth::id());
            return redirect()->back()->with('success', 'Gift card deleted successfully');
        } catch (\Exception $e) {
            // Log Legislativa error('Delete gift card error: ' . $e->getMessage(), [
            //     'line' => $e->getLine(),
            //     'file' => $e->getFile(),
            //     'trace' => $e->getTraceAsString()
            // ]);
            return redirect()->back()->with('error', 'Failed to delete gift card: ' . $e->getMessage());
        }
    }

    // Delete a transaction
    public function deleteTransaction($transactionId)
    {
        try {
            $this->giftCardService->deleteTransaction($transactionId, Auth::id());
            return redirect()->route('admin.gift-transactions')->with('success', 'Transaction deleted successfully');
        } catch (\Exception $e) {
            Log::error('Delete transaction error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to delete transaction: ' . $e->getMessage());
        }
    }
}
