<?php

namespace App\Services;

use App\Models\GiftCard;
use App\Models\GiftCardTransaction;
use App\Models\Rate;
use App\Models\AuditLog;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\DB;
use App\Models\WalletTransaction;
use Exception;
use Illuminate\Container\Attributes\Log as AttributesLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Services\ProfitDistributionService;



class GiftCardService
{
    protected $userService;
    protected $profitDistributionService;


    public function __construct(UserService $userService, ProfitDistributionService $profiitDistributionService)
    {
        $this->userService = $userService;
        $this->profitDistributionService = $profiitDistributionService;


    }

    /**
     * Create a new gift card
     *
     * @param array $data
     * @param int $userId
     * @return GiftCard
     */
    public function createGiftCard($data, $userId)
    {
        // Validate countries is a non-empty array
        if (!isset($data['countries']) || !is_array($data['countries']) || empty($data['countries'])) {
            throw new \Exception('At least one country with buy and sell rates is required.');
        }

        // Encode countries as JSON
        $data['countries'] = json_encode($data['countries']);

        $giftCard = GiftCard::create($data);
        AuditLog::create([
            'user_id' => $userId,
            'action' => 'gift_card_created',
            'details' => json_encode($data)
        ]);
        return $giftCard;
    }

    /**
     * Update an existing gift card
     *
     * @param int $giftCardId
     * @param array $data
     * @param int $userId
     * @return GiftCard
     */
    public function updateGiftCard($giftCardId, $data, $userId)
    {
        $giftCard = GiftCard::findOrFail($giftCardId);
        $oldData = $giftCard->toArray();

        // Validate countries if provided
        if (isset($data['countries'])) {
            if (!is_array($data['countries']) || empty($data['countries'])) {
                throw new \Exception('At least one country with buy and sell rates is required.');
            }
            $data['countries'] = json_encode($data['countries']);
        }

        // Delete previous image from Cloudinary if a new image is uploaded
        if ($giftCard->cloudinary_public_id && isset($data['image'])) {
            $cloudinaryService = new CloudinaryService();
            $cloudinaryService->deleteImage($giftCard->cloudinary_public_id);
        }

        // Upload new image
        if (isset($data['image'])) {
            $cloudinaryService = new CloudinaryService();
            $uploadResult = $cloudinaryService->uploadImage(
                $data['image'],
                'ads',
                env('CLOUDINARY_UPLOAD_PRESET', 'davyking')
            );
            $data['image'] = $uploadResult['secure_url'];
            $data['cloudinary_public_id'] = $uploadResult['public_id'];
        }

        $giftCard->update($data);
        AuditLog::create([
            'user_id' => $userId,
            'action' => 'gift_card_updated',
            'details' => json_encode(['old' => $oldData, 'new' => $data]),
        ]);
        return $giftCard;
    }

    /**
     * Create a new gift card transaction
     *
     * @param array $data
     * @param int $userId
     * @return GiftCardTransaction
     */
    public function createTransaction(array $data, int $userId)
    {
        try {
            return DB::transaction(function () use ($data, $userId) {
                $giftCard = GiftCard::findOrFail($data['gift_card_id']);


                // Validate gift_card_type
                if ($data['type'] === 'sell' && !in_array($data['gift_card_type'], ['physical', 'ecode'])) {
                    throw new Exception('Invalid gift card type. Must be "physical" or "ecode".');
                }

                // Validate balance
                if (!isset($data['balance']) || $data['balance'] <= 0) {
                    throw new Exception('Gift card balance must be greater than zero.');
                }

                // Validate country
                $countries = is_string($giftCard->countries) ? json_decode($giftCard->countries, true) : $giftCard->countries;

                if (!isset($countries[$data['country']])) {
                    throw ValidationException::withMessages(['country' => 'Selected country is not supported for this gift card.']);
                }

                // Validate stock for buy
                // if ($data['type'] === 'buy' && $giftCard->stock < $data['quantity']) {
                //     throw ValidationException::withMessages(['quantity' => 'Insufficient stock for this gift card.']);
                // }


                $countryRates = $countries[$data['country']];

                // Fetch exchange rate for NGN
                // $exchangeRate = ExchangeRate::where('currency_code', 'NGN')->first();
                // if (!$exchangeRate) {
                //     throw new Exception('Exchange rate for NGN not found');
                // }

                // Calculate transaction amount in USD
                $transactionAmountInFiat = $data['type'] === 'buy'
                    ? $data['balance'] * $countryRates['buy_rate']
                    : $data['balance'] * $countryRates['sell_rate'];

                // Convert to NGN
                $transactionAmount = $transactionAmountInFiat;

            $this->profitDistributionService->distributeProfit($transactionAmount);



                // Deduct from user's wallet for buy + wallet_balance
                if ($data['type'] === 'buy' && $data['payment_method'] === 'wallet_balance') {
                    $user = $this->userService->deductWallet($userId, $transactionAmount);
                    if (!$user) {
                        throw new Exception('Insufficient wallet balance for this transaction');
                    }
                }

                // Handle proof file and ecode requirements
                if ($data['type'] === 'sell') {
                    if ($data['gift_card_type'] === 'physical' && (!isset($data['proof_file']) || !$data['proof_file'])) {
                        throw new Exception('Proof file is required for physical gift card sell transactions.');
                    }
                    if ($data['gift_card_type'] === 'ecode' && (!isset($data['ecode']) || !$data['ecode'])) {
                        throw new Exception('E-code is required for e-code sell transactions.');
                    }
                } elseif ($data['type'] === 'buy' && $data['payment_method'] === 'bank_transfer') {
                    if (!isset($data['proof_file']) || !$data['proof_file']) {
                        throw new Exception('Proof file is required for buy transactions with bank transfer.');
                    }
                } else {
                    $data['proof_file'] = null;
                    $data['cloudinary_public_id'] = null;
                }


                // Set defaults for buy
                if ($data['type'] === 'buy') {
                    $data['gift_card_type'] = 'physical';
                    $data['ecode'] = null;
                }


                // Create transaction
                $transaction = GiftCardTransaction::create([
                    'user_id' => $userId,
                    'gift_card_id' => $data['gift_card_id'],
                    'gift_card_name' => $data['gift_card_name'],
                    'country_id' => $data['country'],
                    'type' => $data['type'],
                    'status' => 'pending',
                    'proof_file' => $data['proof_file'] ?? null,
                    'cloudinary_public_id' => $data['cloudinary_public_id'] ?? null,
                    'tx_hash' => $data['tx_hash'] ?? null,
                    'admin_notes' => $data['admin_notes'] ?? null,
                    'payment_method' => $data['payment_method'] ?? null,
                    'gift_card_type' => $data['gift_card_type'],
                    'ecode' => $data['ecode'] ?? null,
                    'balance' => $data['balance'],
                    'fiat_amount' => $transactionAmount
                ]);

                // Log transaction
                TransactionLogger::log(
                    transactionType: 'giftcard_purchase',
                    referenceId: $transaction->id,
                    details: [
                        'total_amount' => $transactionAmount,
                        'message' => "Transaction for Gift Card ({$data['gift_card_name']})",
                        'type' => $data['type'],
                    ],
                    success: true
                );

                // Create wallet transaction for bank_transfer buy
                if ($data['type'] === 'buy' && $data['payment_method'] === 'bank_transfer') {
                    WalletTransaction::updateOrCreate(
                        ['reference' => 'withdrawal_' . uniqid(), 'gateway' => 'admin', 'type' => 'withdrawal'],
                        [
                            'user_id' => $userId,
                            'amount' => $transactionAmount,
                            'description' => "Withdrawal for buying of {$data['gift_card_name']}",
                            'status' => 'completed',
                            'gateway' => 'admin',
                        ]
                    );
                }

                return $transaction;
            });
        } catch (Exception $e) {
            Log::error('Create gift card transaction error: ' . $e->getMessage(), [
                'data' => $data,
                'user_id' => $userId,
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception('Failed to create transaction: ' . $e->getMessage());
        }
    }

    /**
     * Update gift card rates
     *
     * @param int $giftCardId
     * @param array $countries
     * @param int $userId
     * @return array
     */
    public function updateRates($giftCardId, $countries, $userId)
    {
        $giftCard = GiftCard::findOrFail($giftCardId);
        $oldCountries = $giftCard->countries;

        // Validate countries
        if (!is_array($countries) || empty($countries)) {
            throw new \Exception('At least one country with rates is required.');
        }

        // Update gift card with new countries
        $giftCard->update(['countries' => json_encode($countries)]);

        // // Update rates table for each country
        // foreach ($countries as $country => $rates) {
        //     Rate::updateOrCreate(
        //         ['gift_card_id' => $giftCardId, 'rates' => $country],
        //         ['buy_rate' => $rates['buy_rate'], 'sell_rate' => $rates['sell_rate'], 'updated_by' => $userId]
        //     );
        // }



        return $giftCard;
    }

    /**
     * Fetch all transactions for a specific user
     *
     * @param int $userId
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTransactionsByUser($userId, $filters = [])
    {
        $query = GiftCardTransaction::where(['user_id' => $userId])
            ->with(['user', 'giftCard']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['date_range'])) {
            $query->whereBetween('created_at', $filters['date_range']);
        }

        return $query->latest()->get();
    }

    /**
     * Fetch a single transaction by its ID
     *
     * @param int $transactionId
     * @return GiftCard Transaction
     */
    public function getTransactionById($transactionId)
    {
        return GiftCardTransaction::with(['user', 'giftCard'])
            ->findOrFail($transactionId);
    }

    /**
     * Fetch all transactions in the system
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllTransactions($filters = [])
    {
        $query = GiftCardTransaction::with(['user', 'giftCard']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (isset($filters['date_range'])) {
            $query->where('created_at', $filters['date_range']);
        }

        return $query->latest()->get();
    }

    /**
     * Fetch all gift cards with filters
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getGiftCards($filters = [])
    {
        $query = GiftCard::query();

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if (isset($filters['is_enabled'])) {
            $query->where('is_enabled', $filters['is_enabled']);
        }

        return $query->with(['rates'])->get();
    }

    /**
     * Fetch a single gift card by its ID
     *
     * @param int $giftCardId
     * @return GiftCard|null
     */
    public function getGiftCardById($giftCardId)
    {
        $giftCard = GiftCard::with(['rates'])->find($giftCardId);

        return $giftCard;
    }

    /**
     * Fetch transactions with filters
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTransactions($filters = [])
    {
        $query = GiftCardTransaction::with(['user', 'giftCard']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (isset($filters['date_range'])) {
            $query->whereBetween('created_at', $filters['date_range']);
        }
        if (isset($filters['limit'])) {
            $query->limit($filters['limit']);
        }

        return $query->latest()->get();
    }

    /**
     * Update transaction status
     *
     * @param int $transactionId
     * @param string $status
     * @param int $userId
     * @param string|null $notes
     * @return GiftCardTransaction
     */
    public function updateTransactionStatus($transactionId, $status, $userId, $notes = null)
    {
        $transaction = GiftCardTransaction::findOrFail($transactionId);
        $transaction->status = $status;
        $transaction->admin_notes = $notes;
        $transaction->save();

        AuditLog::create([
            'user_id' => $userId,
            'action' => 'transaction_updated',
            'details' => json_encode(['transaction_id' => $transactionId, 'status' => $status]),
        ]);

        return $transaction;
    }

    public function deleteGiftCard($giftCardId, $userId)
    {
        $giftCard = GiftCard::findOrFail($giftCardId);

        // Delete image from Cloudinary
        if ($giftCard->cloudinary_public_id) {
            $cloudinaryService = new CloudinaryService();
            $cloudinaryService->deleteImage($giftCard->cloudinary_public_id);
        }

        $giftCard->delete();

        AuditLog::create([
            'user_id' => $userId,
            'action' => 'gift_card_deleted',
            'details' => json_encode(['gift_card_id' => $giftCardId]),
        ]);
    }

    /**
     * Toggle gift card availability
     *
     * @param int $giftCardId
     * @param bool $table
     * @param int $userId
     * @return GiftCard
     */
    public function toggleGiftCard($giftCardId, $isEnabled, $userId)
    {
        $giftCard = GiftCard::findOrFail($giftCardId);
        $giftCard->is_enabled = $isEnabled;
        $giftCard->save();

        AuditLog::create([
            'user_id' => $userId,
            'action' => 'gift_card_toggled',
            'details' => json_encode(['gift_card_id' => $giftCardId, 'is_enabled' => $isEnabled]),
        ]);

        return $giftCard;
    }

    public function deleteTransaction($transactionId, $userId)
    {
        $transaction = GiftCardTransaction::findOrFail($transactionId);

        // Delete image from Cloudinary
        if ($transaction->cloudinary_public_id) {
            $cloudinaryService = new CloudinaryService();
            $cloudinaryService->deleteImage($transaction->cloudinary_public_id);
        }

        $transaction->delete();

        AuditLog::create([
            'user_id' => $userId,
            'action' => 'transaction_deleted',
            'details' => json_encode(['transaction_id' => $transactionId]),
        ]);
    }
}
