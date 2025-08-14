<?php

use App\Http\Controllers\WEB\AdminChatController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WEB\UserManagementController;
use App\Http\Controllers\WEB\WebAuthController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\WEB\AdminGiftCardController;
use App\Http\Controllers\WEB\AdminCryptoController;
use App\Http\Controllers\WEB\AdminVtuController;
use App\Http\Controllers\WEB\AdminAdController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\WEB\AdminBankDetailsController;
use App\Http\Controllers\WEB\AdminExchangeRateController;
use App\Http\Controllers\WEB\AdminEmailController;
use App\Http\Controllers\WEB\AdminPaymentSettingsController;
use App\Http\Controllers\WEB\WalletTransactionController;

//WILL BE TRANSFERED TO WEB LATER
use App\Http\Controllers\API\UserController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/dashboard', function () {
	return view('/pages/index');
});

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::post('/contact', [LandingController::class, 'contact'])->name('contact');
Route::get('/privacy', [LandingController::class, 'privacy'])->name('privacy');



Route::get('/auth/login', function () {
	return view('/auth/login');
})->name("user.login");



// User authentication routes
Route::prefix('user')->group(function () {
	// Route::post('/register', [AuthController::class, 'register']);
	Route::post('/login', [WebAuthController::class, 'login'])->name('web.login');
	Route::post('/logout', [WebAuthController::class, 'logout'])->name('web.logout');
});


//USERS MANAGEMENT
Route::middleware(['auth'])->group(function () {
	Route::prefix('users')->group(function () {
		Route::get('/', [UserManagementController::class, 'index'])->name('users.index');
		Route::get('/create_user', function () {
			return view('/user_management/create_user');
		});
		// Route::post('/create_user', [UserManagementController::class, 'create_user'])->name('users.create_user');
		Route::get('/{id}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
		Route::get('/{id}', [UserManagementController::class, 'show'])->name('users.get');


		///API FUNCTION THAT WILL BE LATER TRANSFERED ////
		 // Route::post('/create', [UserController::class, 'create_user']);
		 Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
		 Route::patch('/{id}', [UserController::class, 'update']); // Update user
		//  Route::get('/{id}', [UserController::class, 'show']); // View user details
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

//ADMIN GIFTCARD
Route::prefix('gift-cards')->middleware(['auth', 'admin'])->group(function () {
	Route::post('/', [AdminGiftCardController::class, 'store'])->name('admin.gift-cards.store'); //create new giftcards
	Route::put('/{giftCardId}', [AdminGiftCardController::class, 'update'])->name('admin.gift-cards.update'); // update giftcards

	Route::get('/', [AdminGiftCardController::class, 'displayGiftCard'])->name('admin.gift-cards'); //get giftcard dashboard
	Route::post('/update-rates', [AdminGiftCardController::class, 'updateRates'])->name('admin.gift-cards.update-rates');

	Route::get('/index', [AdminGiftCardController::class, 'index']); // List gift cards
	Route::get('/transaction/{transactionId}', [AdminGiftCardController::class, 'transaction'])->name('admin.gift-cards.transaction'); //single transactions
	Route::get('/transactions', [AdminGiftCardController::class, 'transactions']); // List transactions
	Route::get('/transaction/{transactionId}/edit-status', [AdminGiftCardController::class, 'editTransactionStatus'])->name('admin.gift-cards.edit-transaction-status');
	Route::put('/transactions/{transactionId}', [AdminGiftCardController::class, 'updateTransactionStatus'])->name('admin.gift-cards.update-transaction-status'); // Update transaction status
	Route::put('/{giftCardId}/rates', [AdminGiftCardController::class, 'updateRates']); // Update rates
	Route::put('/{giftCardId}/toggle', [AdminGiftCardController::class, 'toggle']); // Toggle availability
	Route::delete('/{giftCardId}', [AdminGiftCardController::class, 'deleteGiftCard'])->name('admin.gift-cards.delete');


	Route::get('/create-transaction', [AdminGiftCardController::class, 'createTransaction'])->name('admin.gift-cards.create-transaction');
	Route::post('/store-transaction', [AdminGiftCardController::class, 'storeTransaction'])->name('admin.gift-cards.store-transaction');
	Route::get('/created-transactions', [AdminGiftCardController::class, 'createdTransactions'])->name('admin.gift-cards.created-transactions');
	Route::delete('/transaction/{transactionId}', [AdminGiftCardController::class, 'deleteTransaction'])->name('admin.gift-cards.transaction.delete');
	Route::get('/all-transactions', [AdminGiftCardController::class, 'allTransactions'])->name('admin.gift-cards.all-transactions');
	Route::get('/user-transactions/{userId}', [AdminGiftCardController::class, 'userTransactions'])->name('admin.gift-cards.user-transactions');
});

//CRYPTO
Route::prefix('crypto')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminCryptoController::class, 'adminPage'])->name('admin.crypto');
    Route::post('/', [AdminCryptoController::class, 'store'])->name('admin.crypto.store');
	Route::patch('/{id}/update-current-price', [AdminCryptoController::class, 'updateCurrentPrice'])->name('admin.crypto.update-current-price');
    Route::post('/update-rates', [AdminCryptoController::class, 'updateRates'])->name('admin.crypto.update-rates');
    Route::get('/create-transaction', [AdminCryptoController::class, 'createTransaction'])->name('admin.crypto.create-transaction');
    Route::post('/store-transaction', [AdminCryptoController::class, 'storeTransaction'])->name('admin.crypto.store-transaction');
    Route::get('/all-transactions', [AdminCryptoController::class, 'allTransactions'])->name('admin.crypto.all-transactions');
    Route::get('/transaction/{transactionId}', [AdminCryptoController::class, 'transaction'])->name('admin.crypto.transaction');
    Route::get('/transaction/{transactionId}/edit-status', [AdminCryptoController::class, 'editTransactionStatus'])->name('admin.crypto.edit-transaction-status');
    Route::put('/transaction/{transactionId}/status', [AdminCryptoController::class, 'updateTransactionStatus'])->name('admin.crypto.update-transaction-status');
    Route::post('/update-liquidity', [AdminCryptoController::class, 'updateLiquidity'])->name('admin.crypto.update-liquidity');
	Route::post('/{cryptoId}/toggle', [AdminCryptoController::class, 'toggle'])->name('admin.crypto.toggle');
	Route::delete('/{cryptoId}', [AdminCryptoController::class, 'deleteCrypto'])->name('admin.crypto.delete');
	Route::delete('/transaction/{transactionId}', [AdminCryptoController::class, 'deleteTransaction'])->name('admin.crypto.transaction.delete');

	  // New routes for wallet address
	  Route::get('/edit-wallet-address/{cryptoId}', [AdminCryptoController::class, 'editWalletAddress'])->name('admin.crypto.edit-wallet-address');
	  Route::post('/update-wallet-address/{cryptoId}', [AdminCryptoController::class, 'updateWalletAddress'])->name('admin.crypto.update-wallet-address');
});

//VTU
Route::prefix('vtu')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminVtuController::class, 'adminPage'])->name('admin.vtu');
    Route::post('/provider', [AdminVtuController::class, 'storeProvider'])->name('admin.vtu.store-provider');
    Route::post('/provider/{providerId}', [AdminVtuController::class, 'updateProvider'])->name('admin.vtu.update-provider');
    Route::post('/plan', [AdminVtuController::class, 'storePlan'])->name('admin.vtu.store-plan');
    Route::get('/create-transaction', [AdminVtuController::class, 'createTransaction'])->name('admin.vtu.create-transaction');
    Route::post('/store-transaction', [AdminVtuController::class, 'storeTransaction'])->name('admin.vtu.store-transaction');
    Route::get('/all-transactions', [AdminVtuController::class, 'allTransactions'])->name('admin.vtu.all-transactions');
    Route::post('/refund/{transactionId}', [AdminVtuController::class, 'refundTransaction'])->name('admin.vtu.refund-transaction');
});

//ADS 
Route::middleware(['auth', 'admin'])->prefix('ads')->name('admin.ads.')->group(function () {
    Route::get('/', [AdminAdController::class, 'index'])->name('index');
    Route::get('/create', [AdminAdController::class, 'create'])->name('create');
    Route::post('/', [AdminAdController::class, 'store'])->name('store');
    Route::get('/{adId}/edit', [AdminAdController::class, 'edit'])->name('edit');
    Route::put('/{adId}', [AdminAdController::class, 'update'])->name('update');
    Route::delete('/{adId}', [AdminAdController::class, 'destroy'])->name('destroy');
});

//BANK DETAILS
Route::prefix('bank-details')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminBankDetailsController::class, 'index'])->name('admin.bank-details.index');
    Route::post('/', [AdminBankDetailsController::class, 'store'])->name('admin.bank-details.store');
    Route::delete('/{id}', [AdminBankDetailsController::class, 'delete'])->name('admin.bank-details.delete');
});

//PAYMENT DETAILS
Route::prefix('payment-settings')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminPaymentSettingsController::class, 'index'])->name('admin..index');
    Route::post('/{gateway}', [AdminPaymentSettingsController::class, 'update'])->name('admin.payment-settings.update');
});

Route::middleware(['auth', 'admin'])->prefix('wallet-transactions')->group(function () {
    Route::get('/', [WalletTransactionController::class, 'index'])->name('admin.wallet-transactions.index');
    Route::delete('/{id}', [WalletTransactionController::class, 'destroy'])->name('admin.wallet-transactions.destroy');
});


//EXCHAGE REATE
 // New exchange rate routes
 Route::prefix('exchange-rates')->middleware(['auth', 'admin'])->group(function () {
	Route::get('/', [AdminExchangeRateController::class, 'index'])->name('admin.exchange-rates.index');
	Route::get('/create', [AdminExchangeRateController::class, 'create'])->name('admin.exchange-rates.create');
	Route::post('/', [AdminExchangeRateController::class, 'store'])->name('admin.exchange-rates.store');
	Route::get('/{exchangeRate}/edit', [AdminExchangeRateController::class, 'edit'])->name('admin.exchange-rates.edit');
	Route::put('/{exchangeRate}', [AdminExchangeRateController::class, 'update'])->name('admin.exchange-rates.update');
	Route::delete('/{exchangeRate}', [AdminExchangeRateController::class, 'destroy'])->name('admin.exchange-rates.destroy');
});

//EMAIL
Route::middleware(['auth', 'admin'])->prefix('email')->group(function () {
    // Show form to send email to a specific user
    Route::get('/send-to-user', [AdminEmailController::class, 'showSendToUserForm'])->name('admin.email.send-to-user.form');
    // Handle form submission for sending email to a specific user
    Route::post('/send-to-user', [AdminEmailController::class, 'sendToUser'])->name('admin.email.send-to-user');

    // Show form to broadcast email to all users
    Route::get('/broadcast', [AdminEmailController::class, 'showBroadcastForm'])->name('admin.email.broadcast.form');
    // Handle form submission for broadcasting email
    Route::post('/broadcast', [AdminEmailController::class, 'broadcast'])->name('admin.email.broadcast');
});


Route::prefix('chat')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminChatController::class, 'index'])->name('admin.chats.index');
    Route::get('/{id}', [AdminChatController::class, 'show'])->name('admin.chats.show');
    Route::post('/{id}/reply', [AdminChatController::class, 'reply'])->name('admin.chats.reply');
    Route::post('/{id}/mark-as-read', [AdminChatController::class, 'markAsRead'])->name('admin.chats.markAsRead');
	Route::delete('/chats/{id}', [AdminChatController::class, 'destroy'])->name('admin.chats.destroy');
});
 

Route::get('/clear-cache', function () {
	Artisan::call('cache:clear');
	Artisan::call('config:clear');
	Artisan::call('route:clear');
	Artisan::call('view:clear');
	return "Cache cleared successfully!";
});


Route::get('/analytics', function () {
	return view('/pages/analytics');
});

Route::get('/email/inbox', function () {
	return view('/pages/email-inbox');
});

Route::get('/email/compose', function () {
	return view('/pages/email-compose');
});

Route::get('/email/detail', function () {
	return view('/pages/email-detail');
});

Route::get('/widgets', function () {
	return view('/pages/widgets');
});

Route::get('/pos/customer-order', function () {
	return view('/pages/pos-customer-order');
});

Route::get('/pos/kitchen-order', function () {
	return view('/pages/pos-kitchen-order');
});

Route::get('/pos/counter-checkout', function () {
	return view('/pages/pos-counter-checkout');
});

Route::get('/pos/table-booking', function () {
	return view('/pages/pos-table-booking');
});

Route::get('/pos/menu-stock', function () {
	return view('/pages/pos-menu-stock');
});

Route::get('/ui/bootstrap', function () {
	return view('/pages/ui-bootstrap');
});

Route::get('/ui/buttons', function () {
	return view('/pages/ui-buttons');
});

Route::get('/ui/card', function () {
	return view('/pages/ui-card');
});

Route::get('/ui/icons', function () {
	return view('/pages/ui-icons');
});

Route::get('/ui/modal-notifications', function () {
	return view('/pages/ui-modal-notifications');
});

Route::get('/ui/typography', function () {
	return view('/pages/ui-typography');
});

Route::get('/ui/tabs-accordions', function () {
	return view('/pages/ui-tabs-accordions');
});

Route::get('/form/elements', function () {
	return view('/pages/form-elements');
});

Route::get('/form/plugins', function () {
	return view('/pages/form-plugins');
});

Route::get('/form/wizards', function () {
	return view('/pages/form-wizards');
});

Route::get('/table/elements', function () {
	return view('/pages/table-elements');
});

Route::get('/table/plugins', function () {
	return view('/pages/table-plugins');
});

Route::get('/chart/chart-js', function () {
	return view('/pages/chart-js');
});

Route::get('/chart/chart-apex', function () {
	return view('/pages/chart-apex');
});

Route::get('/map', function () {
	return view('/pages/map');
});

Route::get('/layout/starter-page', function () {
	return view('/pages/layout-starter-page');
});

Route::get('/layout/fixed-footer', function () {
	return view('/pages/layout-fixed-footer');
});

Route::get('/layout/full-height', function () {
	return view('/pages/layout-full-height');
});

Route::get('/layout/full-width', function () {
	return view('/pages/layout-full-width');
});

Route::get('/layout/boxed-layout', function () {
	return view('/pages/layout-boxed-layout');
});

Route::get('/layout/minified-sidebar', function () {
	return view('/pages/layout-minified-sidebar');
});

Route::get('/layout/top-nav', function () {
	return view('/pages/layout-top-nav');
});

Route::get('/layout/mixed-nav', function () {
	return view('/pages/layout-mixed-nav');
});

Route::get('/layout/mixed-nav-boxed-layout', function () {
	return view('/pages/layout-mixed-nav-boxed-layout');
});

Route::get('/page/scrum-board', function () {
	return view('/pages/page-scrum-board');
});

Route::get('/page/products', function () {
	return view('/pages/page-products');
});

Route::get('/page/product/details', function () {
	return view('/pages/page-product-details');
});

Route::get('/page/orders', function () {
	return view('/pages/page-orders');
});

Route::get('/page/order/details', function () {
	return view('/pages/page-order-details');
});

Route::get('/page/gallery', function () {
	return view('/pages/page-gallery');
});

Route::get('/page/search-results', function () {
	return view('/pages/page-search-results');
});

Route::get('/page/coming-soon', function () {
	return view('/pages/page-coming-soon');
});

Route::get('/page/error', function () {
	return view('/pages/page-error');
});

Route::get('/page/login', function () {
	return view('/pages/page-login');
});

Route::get('/page/register', function () {
	return view('/pages/page-register');
});

Route::get('/page/messenger', function () {
	return view('/pages/page-messenger');
});

Route::get('/page/data-management', function () {
	return view('/pages/page-data-management');
});

Route::get('/page/file-manager', function () {
	return view('/pages/page-file-manager');
});

Route::get('/page/pricing', function () {
	return view('/pages/page-pricing');
});

Route::get('/landing', function () {
	return view('/pages/landing');
});

Route::get('/profile', function () {
	return view('/pages/profile');
});

Route::get('/calendar', function () {
	return view('/pages/calendar');
});

Route::get('/settings', function () {
	return view('/pages/settings');
});

Route::get('/helper', function () {
	return view('/pages/helper');
});
