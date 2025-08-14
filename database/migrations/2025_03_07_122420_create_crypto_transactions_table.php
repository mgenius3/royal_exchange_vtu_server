<?php

// 2025_03_07_000002_create_crypto_transactions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCryptoTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('crypto_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('crypto_currency_id')->constrained()->onDelete('cascade');
            $table->string('type'); // buy, sell
            $table->decimal('amount', 15, 8); // Amount in crypto (e.g., 0.001 BTC)
            $table->decimal('fiat_amount', 15, 8); // Amount in fiat (e.g., $60)
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->string('payment_method'); // bank_transfer, wallet_balance
            $table->string('wallet_address')->nullable(); // Destination/source address
            $table->integer('confirmations')->default(0); // Blockchain confirmations
            $table->string('tx_hash')->nullable(); // Transaction hash
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('crypto_transactions');
    }
}