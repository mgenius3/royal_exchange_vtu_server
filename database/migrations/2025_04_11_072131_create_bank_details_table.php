<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('bank_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Links to the admin user
            $table->string('bank_name');
            $table->string('account_name');
            $table->string('account_number');
            $table->string('ifsc_code')->nullable(); // Optional, for Indian banks
            $table->string('swift_code')->nullable(); // Optional, for international transactions
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bank_details');
    }
}