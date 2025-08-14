<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCloudinaryPublicIdToGiftCardTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('gift_card_transactions', function (Blueprint $table) {
            $table->string('cloudinary_public_id')->nullable()->after('proof_file');
        });
    }

    public function down()
    {
        Schema::table('gift_card_transactions', function (Blueprint $table) {
            $table->dropColumn('cloudinary_public_id');
        });
    }
}

