<?php

// 2025_03_07_000006_create_vtu_transactions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVtuTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('vtu_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vtu_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('vtu_provider_id')->constrained()->onDelete('cascade');
            $table->string('phone_number')->nullable(); // For airtime/data
            $table->string('account_number')->nullable(); // For electricity/TV
            $table->decimal('amount', 10, 2); // Amount charged
            $table->string('status')->default('pending'); // pending, success, failed
            $table->string('transaction_id')->nullable(); // From API provider
            $table->text('response_message')->nullable(); // API response
            $table->boolean('is_refunded')->default(false);
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vtu_transactions');
    }
}