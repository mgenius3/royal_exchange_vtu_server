<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGiftCardTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('gift_card_transactions', function (Blueprint $table) {
            $table->dropColumn('amount');
            $table->dropColumn('fiat_amount');
            $table->string('gift_card_type')->nullable()->after('payment_method'); // 'physical' or 'ecode'
            $table->decimal('balance', 10, 2)->nullable()->after('gift_card_type'); // Gift card balance
        });
    }

    public function down()
    {
        Schema::table('gift_card_transactions', function (Blueprint $table) {
            $table->integer('amount')->nullable();
            $table->decimal('fiat_amount', 10, 2)->nullable();
            $table->dropColumn(['gift_card_type', 'balance']);
        });
    }
}