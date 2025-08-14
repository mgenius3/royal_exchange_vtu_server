<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vtu_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->unique(); // eBills order_id
            $table->string('request_id')->unique(); // Your unique request_id
            $table->string('user_id'); // User who initiated the transaction
            $table->string('product_name'); // Airtime, Electricity, Betting, etc.
            $table->string('status'); // initiated-api, processing-api, completed-api, refunded
            $table->decimal('amount', 10, 2);
            $table->decimal('amount_charged', 10, 2);
            $table->json('meta_data')->nullable();
            $table->timestamp('date_created');
            $table->timestamp('date_updated');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vtu_orders');
    }
};
