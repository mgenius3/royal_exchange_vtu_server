<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftCardsTable extends Migration
{
    public function up()
    {
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // e.g., "Amazon"
            $table->string('category'); // e.g., "Retail"
            $table->decimal('denomination', 8, 2); // e.g., 50.00
            $table->decimal('buy_rate', 4, 2); // e.g., 0.80
            $table->decimal('sell_rate', 4, 2); // e.g., 0.95
            $table->boolean('is_enabled')->default(true); // Default: true
            $table->integer('stock')->nullable(); // Nullable, for physical cards
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('gift_cards');
    }
}