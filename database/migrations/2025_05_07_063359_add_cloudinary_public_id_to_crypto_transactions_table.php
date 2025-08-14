<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCloudinaryPublicIdToCryptoTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('crypto_transactions', function (Blueprint $table) {
            $table->string('cloudinary_public_id')->nullable()->after('proof_file');
        });
    }

    public function down()
    {
        Schema::table('crypto_transactions', function (Blueprint $table) {
            $table->dropColumn('cloudinary_public_id');
        });
    }
}
