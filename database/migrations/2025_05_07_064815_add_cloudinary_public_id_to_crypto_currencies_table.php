<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCloudinaryPublicIdToCryptoCurrenciesTable extends Migration
{
    public function up()
    {
        Schema::table('crypto_currencies', function (Blueprint $table) {
            if (!Schema::hasColumn('crypto_currencies', 'cloudinary_public_id')) {
                $table->string('cloudinary_public_id')->nullable()->after('image');
            }
        });
    }

    public function down()
    {
        Schema::table('crypto_currencies', function (Blueprint $table) {
            $table->dropColumn('cloudinary_public_id');
        });
    }
}
