<?php

// 2025_03_07_000003_create_system_wallets_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemWalletsTable extends Migration
{
    public function up()
    {
        Schema::create('system_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crypto_currency_id')->constrained()->onDelete('cascade');
            $table->decimal('balance', 15, 8)->default(0); // Current balance
            $table->string('address')->nullable(); // System wallet address
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_wallets');
    }
}