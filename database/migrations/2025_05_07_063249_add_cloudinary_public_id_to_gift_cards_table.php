<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCloudinaryPublicIdToGiftCardsTable extends Migration
{
    public function up()
    {
        Schema::table('gift_cards', function (Blueprint $table) {
            $table->string('cloudinary_public_id')->nullable()->after('image');
        });
    }

    public function down()
    {
        Schema::table('gift_cards', function (Blueprint $table) {
            $table->dropColumn('cloudinary_public_id');
        });
    }
}
