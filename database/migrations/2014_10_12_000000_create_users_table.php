<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->decimal('wallet_balance', 15, 2)->default(0);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_admin')->default(false);
            $table->timestamp('date_joined')->useCurrent();
            $table->timestamp('last_login')->nullable();
            $table->string('referral_code')->nullable();
            $table->json('wallet_addresses')->nullable(); // For crypto
            $table->string('password');
            $table->rememberToken();
            $table->enum('status', ['active', 'banned', 'suspended'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
