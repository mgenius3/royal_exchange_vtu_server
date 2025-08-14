<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEcodeToGiftCardTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('gift_card_transactions', function (Blueprint $table) {
            $table->string('ecode')->nullable()->after('gift_card_type'); // e-code for electronic gift cards
        });
    }

    public function down()
    {
        Schema::table('gift_card_transactions', function (Blueprint $table) {
            $table->dropColumn(['ecode']);
        });
    }
}