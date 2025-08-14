<?php

// 2025_03_07_000001_create_crypto_currencies_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCryptoCurrenciesTable extends Migration
{
    public function up()
    {
        Schema::create('crypto_currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., Bitcoin
            $table->string('symbol')->unique(); // e.g., BTC
            $table->string('network')->nullable(); // e.g., Bitcoin, ERC-20
            $table->decimal('buy_rate', 30, 8)->default(0); // Fixed buy rate (e.g., 1 BTC = $60,000)
            $table->decimal('sell_rate', 30, 8)->default(0); // Fixed sell rate
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('crypto_currencies');
    }
}