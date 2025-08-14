<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatesTable extends Migration
{
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('gift_card_id')->constrained()->onDelete('cascade'); // Foreign key to gift_cards
            $table->string('currency', 10); // e.g., "USD", "BTC"
            $table->decimal('buy_rate', 4, 2); // e.g., 0.80
            $table->decimal('sell_rate', 4, 2); // e.g., 0.95
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null'); // Nullable foreign key
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('rates');
    }
}