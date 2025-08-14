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
        Schema::table('gift_card_transactions', function (Blueprint $table) {
            $table->decimal('fiat_amount', 15, 2)->nullable()->after('balance');
        });
    }

    public function down()
    {
        Schema::table('gift_card_transactions', function (Blueprint $table) {
            $table->dropColumn('fiat_amount');
        });
    }
};
