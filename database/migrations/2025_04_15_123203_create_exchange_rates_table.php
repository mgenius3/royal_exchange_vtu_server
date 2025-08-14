<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExchangeRatesTable extends Migration
{
    public function up()
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('currency_code', 3)->unique(); // e.g., USD, EUR
            $table->decimal('rate', 10, 4); // Rate against NGN (e.g., 1600.0000)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exchange_rates');
    }
}