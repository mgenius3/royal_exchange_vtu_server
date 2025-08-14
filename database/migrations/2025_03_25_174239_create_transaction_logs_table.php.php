// database/migrations/2025_03_25_create_transaction_logs_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionLogsTable extends Migration
{
    public function up()
    {
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('transaction_type'); // e.g., "giftcard_purchase", "wallet_funding"
            $table->string('reference_id')->nullable(); // e.g., giftcard_id, order_id
            $table->json('details'); // Flexible details about the transaction
            $table->boolean('success')->default(false); // Success status
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaction_logs');
    }
}