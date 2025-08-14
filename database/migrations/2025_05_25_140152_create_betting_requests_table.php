<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBettingRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('betting_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_id')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('betting_requests');
    }
}