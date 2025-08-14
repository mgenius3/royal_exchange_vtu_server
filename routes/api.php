<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AdController;
use App\Http\Controllers\API\GiftCardController;
use App\Http\Controllers\API\TransactionLogController;
use App\Http\Controllers\API\CryptoController;
use App\Http\Controllers\API\BankDetailsController;
use App\Http\Controllers\API\ExchangeRateController;
use App\Http\Controllers\API\EmailController;
use App\Http\Controllers\API\PaymentGateWayController;
use App\Http\Controllers\API\WalletTransactionController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\EbillsController;
use App\Http\Controllers\ProfitWalletController;




/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Version 1 (v1) of the API
Route::prefix('v1')->group(function () {
    // User authentication routes
    Route::prefix('user')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot_password', [UserController::class, 'forgetPassword'])->middleware('throttle:5,60');
        Route::post('/verify_reset_code', [UserController::class, 'verifyResetCode']);
        Route::post('/set_new_password', [UserController::class, 'setNewPassword']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
         // Email Verification Routes
         Route::prefix('email-verification')->group(function () {
            // Send verification code
            Route::post('/send', [UserController::class, 'sendEmailVerification']);

            // Verify email with code
            Route::post('/verify', [UserController::class, 'verifyEmail']);

            // Resend verification code
            Route::post('/resend', [UserController::class, 'resendEmailVerification']);

            // Check verification status
            Route::post('/status', [UserController::class, 'checkEmailVerificationStatus']);
        });
    });

    // Admin authentication routes
    // Route::prefix('admin')->group(function () {
    //     Route::post('/register', [AuthController::class, 'register']);
    //     Route::post('/login', [AuthController::class, 'login']);
    //     Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    // });

    // USER MANAGEMENT
    Route::middleware(['auth:sanctum'])->group(function () {
        // Group all user-related routes under 'users' prefix
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->middleware('admin'); // Only admin
            // Route::post('/create', [UserController::class, 'create_user']);
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
            Route::patch('/{id}', [UserController::class, 'update']); // Update user
            Route::get('/{id}', [UserController::class, 'show']); // View user details
            Route::patch('/{id}/suspend', [UserController::class, 'suspendUser']);
            Route::patch('/{id}/activate', [UserController::class, 'activateUser']);
            Route::patch('/{id}/update-password', [UserController::class, 'updateUserPassword']);
            Route::patch('/{id}/reset-password', [UserController::class, 'resetPassword']);
            Route::patch('/{id}/fund-wallet', [UserController::class, 'fundWallet'])->middleware('admin');
            Route::patch('/{id}/deduct-wallet', [UserController::class, 'deductWallet']);
            Route::patch('/{id}/approve-kyc', [UserController::class, 'approveKYC']);
            Route::patch('/{id}/reject-kyc', [UserController::class, 'rejectKYC']);
            Route::patch('/{id}/withdrawal-bank', [UserController::class, 'updateWithdrawalBank']);
        });
    });

    //ADS
    Route::get('/ads', [AdController::class, 'index'])->name('api.ads.index');

    //GIFTCARD
    Route::middleware(['auth:sanctum'])->group(function () {
        // Gift Card Endpoints
        Route::prefix('gift-cards')->group(function () {
            Route::get('/', [GiftCardController::class, 'index'])->name('api.gift-cards.index');
            // Route::get('/{giftCardId}', [GiftCardController::class, 'show'])->name('api.gift-cards.show');

            // Transaction Endpoints
            Route::post('/transactions', [GiftCardController::class, 'storeTransaction'])->name('api.transactions.store');
            Route::get('/transactions', [GiftCardController::class, 'userTransactions'])->name('api.transactions.user');
            Route::get('/transactions/{transactionId}', [GiftCardController::class, 'transaction'])->name('api.transactions.show');
            Route::put('/transactions/{transactionId}/status', [GiftCardController::class, 'updateTransactionStatus'])->name('api.transactions.update-status');
        });
    });

    //CRYPTO
    Route::middleware(['auth:sanctum'])->group(
        function () {
            Route::prefix('crypto')->group(function () {
                Route::get('/', [CryptoController::class, 'getCryptocurrencies']);
                Route::post('/transactions', [CryptoController::class, 'storeTransaction']);
                Route::get('/transactions', [CryptoController::class, 'getUserTransactions']);
                Route::get('/transactions/{transactionId}', [CryptoController::class, 'getTransaction']);
            });
        }
    );

    //TRANSACTION LOG
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/transaction-logs', [TransactionLogController::class, 'getTransactionLogs']);
    });

    //ADMIN BANK DETAILS
    Route::get('/bank-details', [BankDetailsController::class, 'index'])->name('api.bank-details.index');

    //EXCHANGE RATES
    Route::get('/exchange-rates', [ExchangeRateController::class, 'index'])->name('api.exchange-rates.index');
    Route::get('/exchange-rates/{currencyCode}', [ExchangeRateController::class, 'show'])->name('api.exchange-rates.show');
    Route::post('/exchange-rates', [ExchangeRateController::class, 'store'])->name('api.exchange-rates.store');
    Route::put('/exchange-rates/{currencyCode}', [ExchangeRateController::class, 'update'])->name('api.exchange-rates.update');
    Route::delete('/exchange-rates/{currencyCode}', [ExchangeRateController::class, 'destroy'])->name('api.exchange-rates.destroy');


    //email
    Route::middleware('auth:sanctum')->group(function () {
        // Send email to a specific user (admin only)
        Route::post('/email/send-to-user', [EmailController::class, 'sendToUser']);

        // Broadcast email to all users (admin only)
        Route::post('/email/broadcast', [EmailController::class, 'broadcast']);
    });

    //PAYMENT GATEWAY
    Route::prefix('payment')->group(function () {
        // Paystack
        Route::post('/paystack/initialize', [PaymentGateWayController::class, 'initializePaystackPayment']);
        Route::get('/paystack/verify/{reference}', [PaymentGateWayController::class, 'verifyPaystackTransaction']);
        Route::get('/paystack/callback', [PaymentGateWayController::class, 'handlePaystackCallback'])->name('paystack.callback');
        Route::post('/paystack/transfer', [PaymentGateWayController::class, 'initializePaystackTransfer']);
        Route::post('/paystack/webhook', [PaymentGateWayController::class, 'handlePaystackWebhook']);
        Route::get('/paystack/banks', [PaymentGateWayController::class, 'getBanks']);

        // Flutterwave
        Route::post('/flutterwave/initialize', [PaymentGateWayController::class, 'initializeFlutterwavePayment']);
        Route::get('/flutterwave/callback', [PaymentGateWayController::class, 'handleFlutterwaveCallback'])->name('flutterwave.callback');
        Route::get('/flutterwave/verify/{transaction_id}', [PaymentGateWayController::class, 'verifyFlutterwaveTransaction']);
        Route::post('/flutterwave/webhook', [PaymentGateWayController::class, 'handleFlutterwaveWebhook']);

        //wallet transaction
        Route::get('/wallet-transactions', [WalletTransactionController::class, 'index']);
    });

    // Authenticated user and admin routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user-profile', function (Request $request) {
            return $request->user(); // Authenticated user
        });

        Route::get('/admin-dashboard', function (Request $request) {
            // Ensure only admins can access this
            if (!$request->user()->tokenCan('admin')) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            return response()->json(['message' => 'Welcome to Admin Dashboard']);
        });
    });

    //CHAT CONTROLLER
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/chat', [ChatController::class, 'getOrCreateChat']);
        Route::post('/chat/{chatId}/messages', [ChatController::class, 'sendMessage']);
        Route::get('/chat/{chatId}/messages', [ChatController::class, 'getMessages']);
    });

    //VTU
    Route::prefix('vtu')->middleware('auth:sanctum')->group(function () {
        Route::post('/buy-data', [EbillsController::class, 'buyData']);
        Route::post('/buy-airtime', [EbillsController::class, 'buyAirtime']);
        Route::get('/data-variations', [EbillsController::class, 'getDataVariations']);
        Route::post('/buy-electricity', [EbillsController::class, 'buyElectricity']);
        Route::post('/buy-betting', [EbillsController::class, 'buyBetting']);
        Route::get('/tv-variations', [EbillsController::class, 'getTvVariations']);
        Route::post('/buy-tv', [EbillsController::class, 'buyTv']);
        Route::post('/verify-customer', [EbillsController::class, 'verifyCustomer']);
        Route::post('/order-status', [EbillsController::class, 'getOrderStatus']);
    });

    Route::prefix('webhooks')->group(function () {
        Route::post('/ebills', [EbillsController::class, 'handleWebhook']);
    });


    Route::prefix('profit')->group(function (){
        Route::post('/wallets/withdraw', [ProfitWalletController::class, 'withdraw']);
        Route::post('/wallets/transfer', [ProfitWalletController::class, 'transferToUser']);   
    });

});
