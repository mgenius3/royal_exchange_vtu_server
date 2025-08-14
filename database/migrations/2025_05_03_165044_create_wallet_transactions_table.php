<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('reference')->unique(); // Unique transaction reference (Paystack reference, Flutterwave transaction_id, or transfer reference)
            $table->decimal('amount', 15, 2); // Positive for deposits, negative for withdrawals
            $table->string('type'); // deposit or withdrawal
            $table->string('status'); // pending, success, failed
            $table->string('gateway'); // paystack, flutterwave, or none for manual adjustments
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallet_transactions');
    }
}
