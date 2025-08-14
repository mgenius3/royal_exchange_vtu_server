<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftCardTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('gift_card_transactions', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users
            $table->foreignId('gift_card_id')->constrained()->onDelete('cascade'); // Foreign key to gift_cards
            $table->enum('type', ['buy', 'sell']); // Enum: "buy", "sell"
            $table->decimal('amount', 10, 2); // e.g., 50.00
            $table->enum('status', ['pending', 'completed', 'rejected', 'flagged'])->default('pending'); // Enum with default
            $table->string('proof_file')->nullable(); // e.g., "uploads/proof.jpg"
            $table->string('tx_hash')->nullable(); // For crypto payments
            $table->text('admin_notes')->nullable(); // Admin notes
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('gift_card_transactions');
    }
}