<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up()
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('gateway'); // paystack or flutterwave
            $table->string('public_key')->nullable();
            $table->string('secret_key')->nullable();
            $table->string('encryption_key')->nullable(); // for Flutterwave
            $table->string('webhook_secret')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
