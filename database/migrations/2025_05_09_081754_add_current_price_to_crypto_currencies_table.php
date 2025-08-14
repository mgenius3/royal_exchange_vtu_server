<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrentPriceToCryptoCurrenciesTable extends Migration
{
    public function up()
    {
        Schema::table('crypto_currencies', function (Blueprint $table) {
            $table->decimal('current_price', 20, 8)->default(0.00000000)->after('wallet_address');
        });
    }

    public function down()
    {
        Schema::table('crypto_currencies', function (Blueprint $table) {
            $table->dropColumn('current_price');
        });
    }
}