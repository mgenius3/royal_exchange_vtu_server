<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageToGiftCardsTable extends Migration
{
    public function up()
    {
        Schema::table('gift_cards', function (Blueprint $table) {
            $table->string('image')->nullable()->after('stock'); // Add image column, nullable
        });
    }

    public function down()
    {
        Schema::table('gift_cards', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
}