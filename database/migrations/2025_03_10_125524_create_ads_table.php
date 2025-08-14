<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdsTable extends Migration
{
    public function up()
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Ad title (e.g., "Get 10% off Crypto")
            $table->text('description')->nullable(); // Ad details
            $table->string('image_url')->nullable(); // URL to ad image
            $table->string('target_url')->nullable(); // URL to redirect when clicked
            $table->string('type')->default('banner'); // banner, popup, interstitial
            $table->boolean('is_active')->default(true); // Show/hide ad
            $table->dateTime('start_date')->nullable(); // When ad starts
            $table->dateTime('end_date')->nullable(); // When ad expires
            $table->integer('priority')->default(0); // Higher priority = shown first
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ads');
    }
}
