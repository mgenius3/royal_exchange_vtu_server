<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\PaymentSetting;
use App\Models\WalletTransaction;
use App\Services\UserService;
use App\Services\ProfitDistributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentGateWayController extends Controller
{
    protected $userService;
    protected $profitDistributionService;

    public function __construct(UserService $userService, ProfitDistributionService $profiitDistributionService)
    {
        $this->userService = $userService;
        $this->profitDistributionService = $profiitDistributionService;

        $this->middleware('auth:sanctum')->only([
            'initializePaystackPayment',
            'initializeFlutterwavePayment',
            'initializePaystackTransfer',
            'verifyPaystackTransaction',
            'verifyFlutterwaveTransaction'
        ]);
    }

    public function initializePaystackPayment(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'amount' => 'required|numeric|min:100',
                'reference' => 'required|string|unique:wallet_transactions,reference',
                'user_id' => 'required|exists:users,id',
            ]);

            $paystack = PaymentSetting::where('gateway', 'paystack')->where('is_active', true)->firstOrFail();

            $total_amount = ($request->amount * 0.10) + $request->amount;


            $response = Http::withToken($paystack->secret_key)
                ->post('https://api.paystack.co/transaction/initialize', [
                    'email' => $request->email,
                    'amount' => $total_amount * 100, // in kobo
                    'reference' => $request->reference,
                    'callback_url' => route('paystack.callback'),
                    'metadata' => ['user_id' => $request->user_id]
                ]);

            $responseData = $response->json();

            // Log initialization attempt
            Log::info('Paystack payment initialized', [
                'user_id' => $request->user_id,
                'reference' => $request->reference,
                'amount' => $request->amount
            ]);

            // Record pending deposit transaction
            WalletTransaction::create([
                'user_id' => $request->user_id,
                'reference' => $request->reference,
                'amount' => $request->amount,
                'type' => 'deposit',
                'status' => 'pending',
                'gateway' => 'paystack',
            ]);

            return response()->json($responseData);
        } catch (\Exception $e) {
            Log::error('Paystack payment initialization failed', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to initialize Paystack payment: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function handlePaystackWebhook(Request $request)
    {
        $paystack = PaymentSetting::where('gateway', 'paystack')->where('is_active', true)->firstOrFail();
        $secretKey = $paystack->secret_key;

        // Verify the x-paystack-signature header
        $signature = hash_hmac('sha512', $request->getContent(), $secretKey);
        if ($signature !== $request->header('x-paystack-signature')) {
            Log::error('Paystack webhook signature verification failed', [
                'provided_signature' => $request->header('x-paystack-signature'),
                'computed_signature' => $signature,
            ]);
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
        }

        $event = $request->all();
        Log::info('Paystack webhook received', ['event' => $event]);

        if ($event['event'] === 'charge.success') {
            $reference = $event['data']['reference'];
            $amount = $event['data']['amount'] / 100; // Convert from kobo to NGN
            $userId = $event['data']['metadata']['user_id'] ?? null;

            if (!$userId) {
                Log::warning('No user_id in Paystack webhook metadata', [
                    'reference' => $reference,
                    'metadata' => $event['data']['metadata'] ?? [],
                ]);
                return response()->json(['status' => 'success'], 200);
            }

            // Check for duplicate processing
            $transaction = WalletTransaction::where('reference', $reference)
                ->where('gateway', 'paystack')
                ->where('type', 'deposit')
                ->first();

            if ($transaction && $transaction->status === 'success') {
                Log::info('Paystack deposit webhook already processed', ['reference' => $reference]);
                return response()->json(['status' => 'success'], 200);
            }

            try {
                // Fund wallet
                $this->userService->fundWallet($userId, $amount);

                // Update or create transaction
                WalletTransaction::updateOrCreate(
                    ['reference' => $reference, 'gateway' => 'paystack', 'type' => 'deposit'],
                    [
                        'user_id' => $userId,
                        'amount' => $amount,
                        'type' => 'deposit',
                        'status' => 'success',
                        'gateway' => 'paystack',
                    ]
                );

                Log::info('Wallet funded via Paystack webhook', [
                    'user_id' => $userId,
                    'amount' => $amount,
                    'reference' => $reference,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to fund wallet via Paystack webhook', [
                    'user_id' => $userId,
                    'amount' => $amount,
                    'reference' => $reference,
                    'error' => $e->getMessage(),
                ]);
                // Update transaction to failed
                WalletTransaction::updateOrCreate(
                    ['reference' => $reference, 'gateway' => 'paystack', 'type' => 'deposit'],
                    ['status' => 'failed']
                );
            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    public function verifyPaystackTransaction(Request $request, $reference)
    {
        try {
            // Validate reference
            $request->merge(['reference' => $reference]);
            $request->validate([
                'reference' => 'required|string',
            ]);

            $paystack = PaymentSetting::where('gateway', 'paystack')->where('is_active', true)->firstOrFail();

            // Check if already processed
            $transaction = WalletTransaction::where('reference', $reference)
                ->where('gateway', 'paystack')
                ->where('type', 'deposit')
                ->first();

            if ($transaction && $transaction->status === 'success') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Transaction already verified and wallet funded',
                    'data' => $transaction
                ], 200);
            }

            // Verify with Paystack
            $response = Http::withToken($paystack->secret_key)
                ->get("https://api.paystack.co/transaction/verify/{$reference}");

            $result = $response->json();

            if (!$result['status'] || $result['data']['status'] !== 'success') {
                Log::warning('Paystack deposit transaction verification failed', [
                    'reference' => $reference,
                    'response' => $result,
                ]);
                // Update transaction to failed if exists
                if ($transaction) {
                    $transaction->update(['status' => 'failed']);
                }
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction verification failed',
                    'data' => $result,
                ], 400);
            }

            $amount = $result['data']['amount'] / 100; // Convert from kobo
            $userId = $result['data']['metadata']['user_id'] ?? null;

            if (!$userId) {
                Log::error('No user_id in Paystack deposit transaction metadata', [
                    'reference' => $reference,
                    'metadata' => $result['data']['metadata'] ?? [],
                ]);
                if ($transaction) {
                    $transaction->update(['status' => 'failed']);
                }
                return response()->json([
                    'status' => 'error',
                    'message' => 'User ID not found in transaction metadata',
                ], 400);
            }


            $profit = ($result['data']['amount'] / 100) * 0.10;
            $this->profitDistributionService->distributeProfit($profit);

            // Fund wallet
            $this->userService->fundWallet($userId, $amount - $profit);

            // Update or create transaction
            $transaction = WalletTransaction::updateOrCreate(
                ['reference' => $reference, 'gateway' => 'paystack', 'type' => 'deposit'],
                [
                    'user_id' => $userId,
                    'amount' => $amount,
                    'type' => 'deposit',
                    'status' => 'success',
                    'gateway' => 'paystack'
                ]
            );

            Log::info('Wallet funded via Paystack deposit verification', [
                'user_id' => $userId,
                'amount' => $amount,
                'reference' => $reference,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction verified and wallet funded',
                'data' => $result,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Paystack deposit transaction verification failed', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);
            // Update transaction to failed if exists
            $transaction = WalletTransaction::where('reference', $reference)
                ->where('gateway', 'paystack')
                ->where('type', 'deposit')
                ->first();
            if ($transaction) {
                $transaction->update(['status' => 'failed']);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to verify Paystack transaction: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function handlePaystackCallback(Request $request)
    {
        $reference = $request->query('reference');
        $status = 'pending';
        $message = 'Payment is being processed.';

        if ($reference) {
            try {
                $response = $this->verifyPaystackTransaction($request, $reference);
                $result = $response->getData(true);

                $status = $result['status'];
                $message = $result['message'];
            } catch (\Exception $e) {
                $status = 'error';
                $message = 'An error occurred while verifying your payment: ' . $e->getMessage();
            }
        } else {
            $status = 'error';
            $message = 'Invalid payment reference.';
        }

        return redirect(config('app.user_subdomain_url'));
        // return view('paystack.callback', compact('status', 'message', 'reference'));
    }

    public function initializePaystackTransfer(Request $request)
    {
        $reference = null; // Define early to avoid "undefined variable" in catch
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'amount' => 'required|numeric|min:100',
                'bank_code' => 'required|string',
                'account_number' => 'required|string',
                'account_name' => 'required|string'
            ]);

            $paystack = PaymentSetting::where('gateway', 'paystack')->where('is_active', true)->firstOrFail();

            // Generate unique reference for withdrawal
            $reference = 'withdrawal_' . uniqid();

            // Record pending withdrawal transaction
            WalletTransaction::create([
                'user_id' => $request->user_id,
                'reference' => $reference,
                'amount' => -$request->amount, // Negative for withdrawal
                'type' => 'withdrawal',
                'status' => 'pending',
                'gateway' => 'paystack'
            ]);

            $profit = $request->amount * 0.05;

            // Deduct from wallet
            $user = $this->userService->deductWallet($request->user_id, $request->amount + $profit);
            if (!$user) {
                Log::error('Wallet deduction failed', [
                    'user_id' => $request->user_id,
                    'amount' => $request->amount,
                    'reference' => $reference,
                ]);
                WalletTransaction::where('reference', $reference)->update(['status' => 'failed']);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient balance or user not found',
                ], 400);
            }

            // Create transfer recipient
            $recipientResponse = Http::withToken($paystack->secret_key)
                ->post('https://api.paystack.co/transferrecipient', [
                    'type' => 'nuban',
                    'name' => $request->account_name,
                    'account_number' => $request->account_number,
                    'bank_code' => $request->bank_code,
                    'currency' => 'NGN',
                ]);

            $recipientData = $recipientResponse->json();
            if (!$recipientData['status']) {
                // Revert wallet deduction
                $this->userService->fundWallet($request->user_id, $request->amount + $profit);
                WalletTransaction::where('reference', $reference)->update(['status' => 'failed']);
                Log::error('Failed to create Paystack transfer recipient', [
                    'user_id' => $request->user_id,
                    'response' => $recipientData,
                    'reference' => $reference,
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create transfer recipient: ' . $recipientData['message'],
                ], 400);
            }


            // Initiate transfer
            $transferResponse = Http::withToken($paystack->secret_key)
                ->post('https://api.paystack.co/transfer', [
                    'source' => 'balance',
                    'amount' => ($request->amount - $profit ) * 100, // in kobo
                    'recipient' => $recipientData['data']['recipient_code'],
                    'reason' => 'Wallet withdrawal ',
                    'reference' => $reference
                ]);

            $transferData = $transferResponse->json();
            if ($transferData['status']) {
                // Update transaction to success
                WalletTransaction::where('reference', $reference)->update([
                    'status' => 'success',
                    'reference' => $transferData['data']['reference'], // Update with Paystack's reference
                ]);

                $this->profitDistributionService->distributeProfit($profit);


                return response()->json([
                    'status' => 'success',
                    'message' => 'Transfer initiated successfully',
                    'transfer' => $transferData['data'],
                ], 200);
            }

            // Revert wallet deduction
            $this->userService->fundWallet($request->user_id, $request->amount + $profit);
            WalletTransaction::where('reference', $reference)->update(['status' => 'failed']);
            Log::error('Paystack withdrawal failed', [
                'user_id' => $request->user_id,
                'response' => $transferData,
                'reference' => $reference,
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Transfer failed: ' . $transferData['message'],
            ], 400);
        } catch (\Exception $e) {
            Log::error('Paystack withdrawal initiation failed', [
                'user_id' => $request->user_id,
                'error' => $e->getMessage(),
                'reference' => $reference ?? 'N/A',
            ]);
            WalletTransaction::where('reference', $reference)->update(['status' => 'failed']);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to initiate transfer: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function initializeFlutterwavePayment(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'amount' => 'required|numeric|min:100',
                'name' => 'required|string',
                'tx_ref' => 'required|string|unique:wallet_transactions,reference',
                'user_id' => 'required|exists:users,id',
            ]);

            $flutterwave = PaymentSetting::where('gateway', 'flutterwave')->where('is_active', true)->firstOrFail();

            $response = Http::withToken($flutterwave->secret_key)
                ->post('https://api.flutterwave.com/v3/payments', [
                    'tx_ref' => $request->tx_ref,
                    'amount' => $request->amount,
                    'currency' => 'NGN',
                    'redirect_url' => route('flutterwave.callback'),
                    'payment_options' => 'card,ussd,banktransfer',
                    'customer' => [
                        'email' => $request->email,
                        'name' => $request->name,
                    ],
                    'customizations' => [
                        'title' => 'Your App Name',
                        'description' => 'Deposit',
                    ],
                    'meta' => ['user_id' => $request->user_id],
                ]);

            $responseData = $response->json();

            // Log initialization attempt
            Log::info('Flutterwave payment initialized', [
                'user_id' => $request->user_id,
                'tx_ref' => $request->tx_ref,
                'amount' => $request->amount,
            ]);

            // Record pending deposit transaction
            WalletTransaction::create([
                'user_id' => $request->user_id,
                'reference' => $request->tx_ref,
                'amount' => $request->amount,
                'type' => 'deposit',
                'status' => 'pending',
                'gateway' => 'flutterwave',
            ]);

            return response()->json($responseData);
        } catch (\Exception $e) {
            Log::error('Flutterwave payment initialization failed', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to initialize Flutterwave payment: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function verifyFlutterwaveTransaction(Request $request, $transaction_id)
    {
        try {
            // Validate transaction_id
            $request->merge(['transaction_id' => $transaction_id]);
            $request->validate([
                'transaction_id' => 'required|string',
            ]);

            $flutterwave = PaymentSetting::where('gateway', 'flutterwave')->where('is_active', true)->firstOrFail();

            // Check if already processed
            $transaction = WalletTransaction::where('reference', $transaction_id)
                ->where('gateway', 'flutterwave')
                ->where('type', 'deposit')
                ->first();

            if ($transaction && $transaction->status === 'success') {
                Log::info('Flutterwave deposit transaction already processed', [
                    'transaction_id' => $transaction_id,
                    'transaction_id_db' => $transaction->id,
                ]);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Transaction already verified and wallet funded',
                    'data' => $transaction,
                ], 200);
            }

            // Verify with Flutterwave
            $response = Http::withToken($flutterwave->secret_key)
                ->get("https://api.flutterwave.com/v3/transactions/{$transaction_id}/verify");

            $result = $response->json();

            if ($result['status'] !== 'success' || $result['data']['status'] !== 'successful') {
                Log::warning('Flutterwave deposit transaction verification failed', [
                    'transaction_id' => $transaction_id,
                    'response' => $result,
                ]);
                if ($transaction) {
                    $transaction->update(['status' => 'failed']);
                }
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction verification failed',
                    'data' => $result,
                ], 400);
            }

            $amount = $result['data']['amount'];
            $userId = $result['data']['meta']['user_id'] ?? null;

            if (!$userId) {
                Log::error('No user_id in Flutterwave deposit transaction meta', [
                    'transaction_id' => $transaction_id,
                    'meta' => $result['data']['meta'] ?? [],
                ]);
                if ($transaction) {
                    $transaction->update(['status' => 'failed']);
                }
                return response()->json([
                    'status' => 'error',
                    'message' => 'User ID not found in transaction metadata',
                ], 400);
            }

            // Fund wallet
            $this->userService->fundWallet($userId, $amount);

            // Update or create transaction
            $transaction = WalletTransaction::updateOrCreate(
                ['reference' => $transaction_id, 'gateway' => 'flutterwave', 'type' => 'deposit'],
                [
                    'user_id' => $userId,
                    'amount' => $amount,
                    'type' => 'deposit',
                    'status' => 'success',
                    'gateway' => 'flutterwave',
                ]
            );

            Log::info('Wallet funded via Flutterwave deposit verification', [
                'user_id' => $userId,
                'amount' => $amount,
                'transaction_id' => $transaction_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction verified and wallet funded',
                'data' => $result,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Flutterwave deposit transaction verification failed', [
                'transaction_id' => $transaction_id,
                'error' => $e->getMessage(),
            ]);
            $transaction = WalletTransaction::where('reference', $transaction_id)
                ->where('gateway', 'flutterwave')
                ->where('type', 'deposit')
                ->first();
            if ($transaction) {
                $transaction->update(['status' => 'failed']);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to verify Flutterwave transaction: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function handleFlutterwaveCallback(Request $request)
    {
        $transactionId = $request->query('transaction_id');
        $status = 'pending';
        $message = 'Payment is being processed.';

        if ($transactionId) {
            try {
                $response = $this->verifyFlutterwaveTransaction($request, $transactionId);
                $result = $response->getData(true);

                $status = $result['status'];
                $message = $result['message'];
            } catch (\Exception $e) {
                $status = 'error';
                $message = 'An error occurred while verifying your payment: ' . $e->getMessage();
            }
        } else {
            $status = 'error';
            $message = 'Invalid transaction ID.';
        }

        return view('flutterwave.callback', compact('status', 'message', 'transactionId'));
    }

    public function handleFlutterwaveWebhook(Request $request)
    {
        $flutterwave = PaymentSetting::where('gateway', 'flutterwave')->where('is_active', true)->firstOrFail();
        if ($request->header('verif-hash') !== $flutterwave->webhook_secret) {
            Log::error('Flutterwave webhook signature verification failed', [
                'provided_hash' => $request->header('verif-hash'),
            ]);
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
        }

        $event = $request->all();
        Log::info('Flutterwave webhook received', ['event' => $event]);

        if ($event['status'] === 'successful') {
            $transactionId = $event['transaction_id'];
            $amount = $event['amount'];
            $userId = $event['meta']['user_id'] ?? null;

            if (!$userId) {
                Log::warning('No user_id in Flutterwave webhook meta', [
                    'transaction_id' => $transactionId,
                    'meta' => $event['meta'] ?? [],
                ]);
                return response()->json(['status' => 'success'], 200);
            }

            // Check for duplicate processing
            $transaction = WalletTransaction::where('reference', $transactionId)
                ->where('gateway', 'flutterwave')
                ->where('type', 'deposit')
                ->first();

            if ($transaction && $transaction->status === 'success') {
                Log::info('Flutterwave deposit webhook already processed', ['transaction_id' => $transactionId]);
                return response()->json(['status' => 'success'], 200);
            }

            try {
                // Fund wallet
                $this->userService->fundWallet($userId, $amount);

                // Update or create transaction
                WalletTransaction::updateOrCreate(
                    ['reference' => $transactionId, 'gateway' => 'flutterwave', 'type' => 'deposit'],
                    [
                        'user_id' => $userId,
                        'amount' => $amount,
                        'type' => 'deposit',
                        'status' => 'success',
                        'gateway' => 'flutterwave',
                    ]
                );

                Log::info('Wallet funded via Flutterwave webhook', [
                    'user_id' => $userId,
                    'amount' => $amount,
                    'transaction_id' => $transactionId,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to fund wallet via Flutterwave webhook', [
                    'user_id' => $userId,
                    'amount' => $amount,
                    'transaction_id' => $transactionId,
                    'error' => $e->getMessage(),
                ]);
                WalletTransaction::updateOrCreate(
                    ['reference' => $transactionId, 'gateway' => 'flutterwave', 'type' => 'deposit'],
                    ['status' => 'failed']
                );
            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    public function getBanks()
    {
        try {
            $paystack = PaymentSetting::where('gateway', 'paystack')->where('is_active', true)->firstOrFail();
            $response = Http::withToken($paystack->secret_key)->get('https://api.paystack.co/bank');
            $responseData = $response->json();

            if (!$responseData['status']) {
                Log::error('Failed to fetch Paystack banks', ['response' => $responseData]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to fetch banks: ' . $responseData['message'],
                ], 400);
            }

            return response()->json($responseData);
        } catch (\Exception $e) {
            Log::error('Failed to fetch Paystack banks', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch banks: ' . $e->getMessage(),
            ], 400);
        }
    }
}
