<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWalletAddressToCryptoCurrenciesTable extends Migration
{
    public function up()
    {
        Schema::table('crypto_currencies', function (Blueprint $table) {
            $table->string('wallet_address')->nullable()->after('image'); // Add wallet_address column
        });
    }

    public function down()
    {
        Schema::table('crypto_currencies', function (Blueprint $table) {
            $table->dropColumn('wallet_address');
        });
    }
}