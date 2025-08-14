<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGiftCardsTable extends Migration
{
    public function up()
    {
        Schema::table('gift_cards', function (Blueprint $table) {
            $table->dropColumn(['buy_rate', 'sell_rate','ranges', 'denomination']);
            $table->json('countries');// Make non-nullable
        });
    }

    public function down()
    {
        Schema::table('gift_cards', function (Blueprint $table) {
            $table->decimal('buy_rate', 8, 2)->nullable();
            $table->decimal('sell_rate', 8, 2)->nullable();
            $table->json('ranges')->nullable();
            $table->decimal('denomination', 8, 2)->nullable();
            $table->json('countries');
        });
    }
}