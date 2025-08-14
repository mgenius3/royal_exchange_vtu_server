<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCryptoTransactionsFiatAmountPrecision extends Migration
{
    public function up()
    {
        Schema::table('crypto_transactions', function (Blueprint $table) {
            $table->decimal('fiat_amount', 20, 8)->change();
            $table->decimal('amount', 20, 8)->change(); // Update 'amount' too for consistency
        });
    }

    public function down()
    {
        Schema::table('crypto_transactions', function (Blueprint $table) {
            $table->decimal('fiat_amount', 15, 8)->change();
            $table->decimal('amount', 15, 8)->change();
        });
    }
}