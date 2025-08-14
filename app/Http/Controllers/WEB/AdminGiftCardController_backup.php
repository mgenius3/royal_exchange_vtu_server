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
                'denomination' => 'required|numeric|min:0',
                'buy_rate' => 'required|numeric|min:0',
                'sell_rate' => 'required|numeric|min:0',
                'image' => 'nullable|image|max:2048', // Max 2MB
                'stock' => 'required|integer|min:0',
                // 'ranges' => 'nullable|array',
                // 'ranges.*' => 'nullable|string',
            ]);

            $data = $request->all();

            if ($request->hasFile('image')) {
                $cloudinaryService = new CloudinaryService();
                $uploadResult = $cloudinaryService->uploadImage(
                    $request->file('image'), // Use file() instead of $data['image']
                    'ads',
                    env('CLOUDINARY_UPLOAD_PRESET', 'davyking')
                );

                $data['image'] = $uploadResult['secure_url'];
                $data['cloudinary_public_id'] = $uploadResult['public_id'];
            }

            $this->giftCardService->createGiftCard($data, Auth::id());

            return redirect()->back()->with('success', 'Gift card added successfully');
        } catch (\Exception $e) {
            // Optional: log error details for debugging
            Log::error('GiftCard store error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->withErrors('An error occurred while adding the gift card. Please try again.');
        }
    }



    //update giftcard
    public function update(Request $request, $giftCardId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'denomination' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'stock' => 'sometimes|integer|min:0',
            // 'ranges' => 'nullable|array', // Validate ranges as an array
            // 'ranges.*' => 'nullable|string', // Each range should be a string
        ]);

        $data = $request->all();
        // if ($request->hasFile('image')) {
        //     $data['image'] = $request->file('image')->store('giftcards', 'public');
        // }



        $this->giftCardService->updateGiftCard($giftCardId, $data, Auth::id());
        return redirect()->back()->with('success', 'Gift card updated successfully');
    }

    // Display the Gift Card Admin Page
    public function displayGiftCard()
    {
        $giftCards = $this->giftCardService->getGiftCards();
        $transactions = $this->giftCardService->getTransactions(['limit' => 5]); // Limit to 5 recent transactions

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
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'gift_card_id' => 'required|exists:gift_cards,id',
            'type' => 'required|in:buy,sell',
            'amount' => 'nullable|numeric|min:0', // Optional, calculated if not provided
            'proof_file' => 'nullable|file|max:2048',
            'tx_hash' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string', 
        ]);

        $data = $request->all();

        if ($request->type === 'sell') {
            $data['proof_file'] = $data['proof_file_sell'];
        }

        if ($request->hasFile('proof_file') || $request->hasFile('proof_file_sell')) {
            $cloudinaryService = new CloudinaryService();
            $uploadResult = $cloudinaryService->uploadImage(
                $data['proof_file'],
                'crypto_proof',
                env('CLOUDINARY_UPLOAD_PRESET', 'davyking')
            );
            $data['proof_file'] = $uploadResult['secure_url'];
            $data['cloudinary_public_id'] = $uploadResult['public_id'];
        }

        try{
            $this->giftCardService->createTransaction($data, Auth::id());
            return redirect()->route('admin.gift-cards')->with('success', 'Transaction created successfully');

        } catch (\Exception $e){
            Log::error('crypto error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', $e->getMessage())->withInput();
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

    // Show a single transaction (updated)
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
        $request->validate([
            'status' => 'required|in:pending,completed,rejected,flagged',
            'notes' => 'nullable|string|max:500'
        ]);

        $transaction = $this->giftCardService->updateTransactionStatus(
            $transactionId,
            $request->status,
            Auth::id(),
            $request->notes
        );

        return redirect()->route('admin.gift-cards.transaction', $transactionId)->with('success', 'Transaction status updated successfully');

        // return response()->json(['data' => $transaction, 'message' => 'Transaction updated'], 200);
    }

    // Update gift card rates (form submission)
    public function updateRates(Request $request)
    {
        $request->validate([
            'gift_card_id' => 'required|exists:gift_cards,id',
            'currency' => 'required|string|max:10',
            'buy_rate' => 'required|numeric|min:0|max:1',
            'sell_rate' => 'required|numeric|min:0|max:1',
        ]);

        $this->giftCardService->updateRates(
            $request->gift_card_id,
            $request->currency,
            $request->buy_rate,
            $request->sell_rate,
            Auth::id()
        );

        return redirect()->back()->with('success', 'Rates updated successfully');
    }

   

    // Toggle gift card availability
    public function toggle(Request $request, $giftCardId)
    {
        $request->validate([
            'is_enabled' => 'required|boolean',
        ]);

        $giftCard = $this->giftCardService->toggleGiftCard($giftCardId, $request->is_enabled, Auth::id());

        return response()->json(['data' => $giftCard, 'message' => 'Gift card status updated'], 200);
    }

     // Delete a giftcard
     public function deleteGiftCard($giftCardId)
     {
         try {
             $this->giftCardService->deleteGiftcard($giftCardId, Auth::id());
             return redirect()->back()->with('success', 'Giftcard deleted successfully');
         } catch (\Exception $e) {
             return redirect()->back()->with('error', 'Failed to delete Giftcard: ' . $e->getMessage());
         }
     }


       // Delete a transaction
    public function deleteTransaction($transactionId)
    {
        try {
            $this->giftCardService->deleteTransaction($transactionId, Auth::id());
            return redirect()->route('admin.gift-cards.all-transactions')->with('success', 'Transaction created successfully');
            // return redirect()->back()->with('success', 'Transaction deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete transaction: ' . $e->getMessage());
        }
    }
}
