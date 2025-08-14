<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentMethodAndFiatAmountToGiftCardTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('gift_card_transactions', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('admin_notes'); // e.g., 'wallet_balance', 'bank_transfer'
            $table->decimal('fiat_amount', 15, 2)->nullable()->after('payment_method'); // Fiat amount in NGN (or other currency)
        });
    }

    public function down()
    {
        Schema::table('gift_card_transactions', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'fiat_amount']);
        });
    }
}