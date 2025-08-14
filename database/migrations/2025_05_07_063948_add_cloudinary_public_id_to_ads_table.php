<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCloudinaryPublicIdToAdsTable extends Migration
{
    public function up()
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->string('cloudinary_public_id')->nullable()->after('image_url');
        });
    }

    public function down()
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn('cloudinary_public_id');
        });
    }
}